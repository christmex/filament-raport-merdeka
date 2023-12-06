<?php

namespace App\Filament\Resources\AssessmentResource\Pages;

use Filament\Actions;
use App\Helpers\Report;
use App\Models\Student;
use Filament\Forms\Get;
use App\Models\Classroom;
use App\Models\Assessment;
use App\Models\SubjectUser;
use App\Models\HomeroomTeacher;
use App\Models\StudentClassroom;
use App\Imports\AssessmentImport;
use App\Exports\ReportSheetExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\AssessmentResource;


class ManageAssessments extends ManageRecords
{
    protected static string $resource = AssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('reportSheet')
                ->form([
                    Select::make('classroom_id')
                    ->label('classroom')
                    ->options(function(){
                        return Classroom::whereIn('id',array_unique(auth()->user()->activeSubjects->pluck('classroom_id')->toArray()))->pluck('classroom_name','id');
                    })
                    ->required()
                    ->searchable()
                    ->selectablePlaceholder(false)
                    ->preload(),
                ])
                ->action(function(array $data){
                    return redirect()->route('students.print-report-sheet-for-teacher',$data['classroom_id']);

                    // return Excel::download(new ReportSheetExport(Report::generateReportSheet($data['classroom_id'])), 'report_sheet.xlsx');
                    // return redirect()->route('students.print-report-sheet-for-teacher',$data['classroom_id']);
                }),
            Actions\ActionGroup::make([
                Actions\Action::make('importAssessment')->color('success')
                ->form([
                    \Filament\Forms\Components\FileUpload::make('import_assessment')
                        ->storeFiles(false)
                        ->helperText(new HtmlString('Please export the assessment before you upload the file'))
                        ->columnSpanFull(),
                ])
                ->action(function(array $data){
                    DB::beginTransaction();
                    try {
                        Excel::import(new AssessmentImport, $data['import_assessment']);
                        DB::commit();
                        Notification::make()
                            ->success()
                            ->title('Assessment imported')
                            ->send();
                    } catch (\Throwable $th) {
                        DB::rollback();
                        Notification::make()
                            ->danger()
                            ->title($th->getMessage())
                            ->send();
                    }
                })
            ])
            ->label('Import')
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('success')
            ->button(),  
            // Actions\CreateAction::make(),
            // Actions\Action::make('Create Assessment By Student')
            // ->button()
            // ->form([
            //     Select::make('assessment_method_setting_id')
            //         ->relationship('assessmentMethodSetting','assessment_method_setting_name')
            //         ->required()
            //         ->searchable()
            //         ->preload(),
            //     Select::make('topic_setting_id')
            //         ->relationship('topicSetting','topic_setting_name')
            //         ->required()
            //         ->searchable()
            //         ->preload(),
            //     Select::make('subject_user_id')
            //         ->label('subject')
            //         ->options(SubjectUser::with('subject')->whereIn('id',auth()->user()->activeSubjects->pluck('id')->toArray())->get()->pluck('subject_user_name', 'id'))
            //         ->required()
            //         ->searchable()
            //         // ->multiple()
            //         ->live()
            //         ->selectablePlaceholder(false)
            //         ->preload(),
            //     Select::make('student_id')
            //         ->options(function(Get $get){

            //             if($get('subject_user_id')){
            //                 $querySubjectUser = [];
    
            //                 $SubjectUserIds = SubjectUser::whereIn('id',$get('subject_user_id'))
            //                     ->get()
            //                     // ->pluck('classroom_id')
            //                     // ->toArray()
            //                     ;
            //                 foreach ($SubjectUserIds as $key => $value) {
            //                     $querySubjectUser['classroom_id'][] = $value->classroom_id;
            //                     $querySubjectUser['school_year_id'][] = $value->school_year_id;
            //                     $querySubjectUser['school_term_id'][] = $value->school_term_id;
            //                 }
    
            //                 $HomeroomTeacherIds = HomeroomTeacher::query()
            //                     ->whereIn('classroom_id',$querySubjectUser['classroom_id'])
            //                     ->whereIn('school_year_id',$querySubjectUser['school_year_id'])
            //                     ->whereIn('school_term_id',$querySubjectUser['school_term_id'])
            //                     ->get()
            //                     ->pluck('id')
            //                     ->toArray()
            //                     ;
    
                                
            //                 $StudentClassroomIds = StudentClassroom::query()
            //                 ->whereIn('homeroom_teacher_id',$HomeroomTeacherIds)
            //                 ->get()
            //                 ->pluck('student_id')
            //                 ->toArray();
    
            //                 return Student::whereIn('id', $StudentClassroomIds)->get()->pluck('student_name_with_classroom','id');
            //             }
            //         })
            //         ,
            //     TextInput::make('topic_name')
            //         ->required(),
            // ])
            // ->action(function (array $data): void {
            //     $dataArray = [];

            //     for($i=0; $i < count($data['subject_user_id']); $i++) {
            //         $dataArray[] = [
            //             'student_id' => $data['student_id'],
            //             'assessment_method_setting_id' => $data['assessment_method_setting_id'],
            //             'topic_setting_id' => $data['topic_setting_id'],
            //             'subject_user_id' => $data['subject_user_id'][$i],
            //             'topic_name' => $data['topic_name'],
            //         ];
            //     }

            //     dd($dataArray);

            //     if(DB::table('assessments')->insertOrIgnore($dataArray)){
            //         Notification::make()
            //             ->success()
            //             ->title('yeayy, success!')
            //             ->body('Successfully added data')
            //             ->send();
            //     }
            // })
            // ,
            // ExportAction::make(),

            Actions\Action::make('Create Assessment By Classroom')
            ->form([
                Select::make('assessment_method_setting_id')
                    ->relationship('assessmentMethodSetting','assessment_method_setting_name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('topic_setting_id')
                    ->relationship('topicSetting','topic_setting_name')
                    ->required()
                    ->searchable()
                    ->helperText('Topic 1 also called Chapter 1 or bab 1, etc, they are all the same 🤩')
                    ->preload(),
                Select::make('subject_user_id')
                    ->label('subject')
                    ->options(SubjectUser::with('subject')->whereIn('id',auth()->user()->activeSubjects->pluck('id')->toArray())->get()->pluck('subject_user_name', 'id'))
                    ->required()
                    ->searchable()
                    ->live()
                    ->selectablePlaceholder(false)
                    ->preload(),
                CheckboxList::make('student_id')
                    ->label('Students')
                    ->options(function(Get $get){
                        $selectSubjectUser = SubjectUser::with('classroom')->where('id',$get('subject_user_id'))->first();
                        if($selectSubjectUser){
                            $studentIds = StudentClassroom::query()
                            ->where('classroom_id',$selectSubjectUser->classroom_id)
                            ->where('school_year_id',$selectSubjectUser->school_year_id)
                            ->where('school_term_id',$selectSubjectUser->school_term_id)
                            ->get()
                            ->pluck('student_id')
                            ->toArray();
    
                            return Student::whereIn('id',$studentIds)->get()->pluck('student_name','id');
                        }
                    })
                    // ->default(fn (CheckboxList $component): array => dd($component))
                    ->searchable()
                    ->bulkToggleable()
                    ->columns(3),
                TextInput::make('topic_name')
                    ->helperText('Format : (Assessment Method Setting Name) - (Topic Name) | Example: Penugasan 1 - Berhitung 1-10')
                    ->required(),  
            ])
            ->button()
            ->color('info')
            ->action(function (array $data): void {
                $dataArray = [];
                $getCLassroomStudentIds = $data['student_id'];

                if(!count($getCLassroomStudentIds)){
                    Notification::make()
                        ->warning()
                        ->title('Whopps, cant do that :(')
                        ->body("No student selected")
                        ->send();
                }else {
                    for($i=0; $i < count($getCLassroomStudentIds); $i++) {
                        $dataArray[] = [
                            'student_id' => $getCLassroomStudentIds[$i],
                            'assessment_method_setting_id' => $data['assessment_method_setting_id'],
                            'topic_setting_id' => $data['topic_setting_id'],
                            'subject_user_id' => $data['subject_user_id'],
                            'topic_name' => $data['topic_name'],
                        ];
                    }
    
                    if(DB::table('assessments')->insertOrIgnore($dataArray)){
                        Notification::make()
                            ->success()
                            ->title('yeayy, success!')
                            ->body('Successfully added data')
                            ->send();
                    }
                }

            })
            ,
            Actions\Action::make('Create Bulk Assessment By Classroom')
            ->button()
            ->form([
                Select::make('assessment_method_setting_id')
                    ->relationship('assessmentMethodSetting','assessment_method_setting_name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('topic_setting_id')
                    ->relationship('topicSetting','topic_setting_name')
                    ->required()
                    ->searchable()
                    ->helperText('Topic 1 also called Chapter 1 or bab 1, etc, they are all the same 🤩')
                    ->preload(),
                Select::make('subject_user_id')
                    ->label('subject')
                    ->options(SubjectUser::with('subject')->whereIn('id',auth()->user()->activeSubjects->pluck('id')->toArray())->get()->pluck('subject_user_name', 'id'))
                    ->required()
                    ->searchable()
                    ->multiple()
                    ->selectablePlaceholder(false)
                    ->preload(),
                TextInput::make('topic_name')
                ->helperText('Format : (Assessment Method Setting Name) - (Topic Name) | Example: Penugasan 1 - Berhitung 1-10')
                ->required(),  
            ])
            ->action(function (array $data): void {
                
                $dataArray = [];
                $selectSubjectUser = SubjectUser::with('classroom')->whereIn('id',$data['subject_user_id'])->get();
                foreach ($selectSubjectUser as $key => $value) {
                    
                    // $getCLassroomStudentIds = StudentClassroom::query()
                    //     ->where('classroom_id',$value->classroom_id)
                    //     ->where('school_year_id',$value->school_year_id)
                    //     ->where('school_term_id',$value->school_term_id)
                    //     ->get()
                    //     ->pluck('student_id')
                    //     ->toArray()
                    //     ;

                    // why did i perform this stupid action?
                    // i want to select the homeroom teacher's id, so i can select the studentClassroom based on the homeroom teacher id, but the quistion is why i use where user_id in that condition? please find out again, for now it work fine
                    // $getHomeroomTeacherIds = HomeroomTeacher::query()
                    // ->where('classroom_id',$value->classroom_id)
                    // ->where('school_year_id',$value->school_year_id)
                    // ->where('school_term_id',$value->school_term_id)
                    // // ->where('user_id',auth()->id()) //i comment this because it wont select if the subject teacher not the homeroom teacher, please review this later 
                    // ->get()
                    // ->pluck('id')
                    // ->toArray()
                    // ;

                    // dd($value,$getHomeroomTeacherIds);

                    $getCLassroomStudentIds = StudentClassroom::query()
                    // ->whereIn('homeroom_teacher_id',$getHomeroomTeacherIds)
                    ->where('classroom_id',$value->classroom_id)
                    ->where('school_year_id',$value->school_year_id)
                    ->where('school_term_id',$value->school_term_id)
                    ->get()
                    ->pluck('student_id')
                    ->toArray();

                    if(!count($getCLassroomStudentIds)){
                        Notification::make()
                            ->warning()
                            ->title('Whopps, cant do that :(')
                            ->body("There is no student in {$value->classroom->classroom_name}, please check the data")
                            ->send();
                        break;
                    }
                    
                    for($i=0; $i < count($getCLassroomStudentIds); $i++) {
                        $dataArray[] = [
                            'student_id' => $getCLassroomStudentIds[$i],
                            'assessment_method_setting_id' => $data['assessment_method_setting_id'],
                            'topic_setting_id' => $data['topic_setting_id'],
                            'subject_user_id' => $value->id,
                            'topic_name' => $data['topic_name'],
                        ];
                    }
                }
                if(DB::table('assessments')->insertOrIgnore($dataArray)){
                    Notification::make()
                        ->success()
                        ->title('yeayy, success!')
                        ->body('Successfully added data')
                        ->send();
                }
            }),
        ];
    }
}
