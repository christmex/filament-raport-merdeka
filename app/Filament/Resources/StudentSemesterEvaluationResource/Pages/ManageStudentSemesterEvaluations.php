<?php

namespace App\Filament\Resources\StudentSemesterEvaluationResource\Pages;

use App\Models\StudentSemesterEvaluation;
use Filament\Actions;
use App\Models\Student;
use Filament\Forms\Get;
use App\Models\SubjectUser;
use App\Models\StudentClassroom;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\StudentSemesterEvaluationResource;

class ManageStudentSemesterEvaluations extends ManageRecords
{
    protected static string $resource = StudentSemesterEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('CreatePAS')
            ->label('Create PAS')
            ->button()
            ->form([
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
                    ->searchable()
                    ->bulkToggleable()
                    ->columns(3),
            ])
            ->action(function (array $data): void {
                $dataArray = [];
                $getCLassroomStudentIds = $data['student_id'];

                if(!count($getCLassroomStudentIds)){
                    Notification::make()
                        ->warning()
                        ->title('Whopps, cant do that :(')
                        ->body("No student selected")
                        ->send();
                }else{
                    $check = StudentSemesterEvaluation::whereIn('student_id',$getCLassroomStudentIds)->where('subject_user_id',$data['subject_user_id'])->get();
                    
                    if(!$check->count()){
                        for($i=0; $i < count($getCLassroomStudentIds); $i++) {
                            $dataArray[] = [
                                'student_id' => $getCLassroomStudentIds[$i],
                                'subject_user_id' => $data['subject_user_id'],
                            ];
                        }
    
                        if(DB::table('student_semester_evaluations')->insertOrIgnore($dataArray)){
                            Notification::make()
                                ->success()
                                ->title('yeayy, success!')
                                ->body('Successfully added data')
                                ->send();
                        }
                    }else{
                        $arrayNames = array_unique($check->pluck('student.student_name')->toArray());
                        $names ='';
                        for ($i=0; $i < count($arrayNames); $i++) { 
                            $names .= $arrayNames[$i]."<br>";
                        }
                        
                        Notification::make()
                        ->danger()
                        ->title('Failed! data exist with selected subject')
                        ->body($names)
                        ->send();
                    }
                }
            }),

            // Actions\Action::make('Create Assessment By Classroom')
            // ->button()
            // ->form([
            //     Select::make('subject_user_id')
            //     ->label('subject')
            //     ->options(SubjectUser::with('subject')->whereIn('id',auth()->user()->activeSubjects->pluck('id')->toArray())->get()->pluck('subject_user_name', 'id'))
            //     ->required()
            //     ->searchable()
            //     ->multiple()
            //     // ->live()
            //     ->selectablePlaceholder(false)
            //     ->preload(),
            // ])
            // ->action(function (array $data): void {
            //     $dataArray = [];
            //     $selectSubjectUser = SubjectUser::with('classroom')->whereIn('id',$data['subject_user_id'])->get();

            //     foreach ($selectSubjectUser as $key => $value) {
            //         $getCLassroomStudentIds = StudentClassroom::query()
            //         ->where('classroom_id',$value->classroom_id)
            //         ->where('school_year_id',$value->school_year_id)
            //         ->where('school_term_id',$value->school_term_id)
            //         ->get()
            //         ->pluck('student_id')
            //         ->toArray();

            //         if(!count($getCLassroomStudentIds)){
            //             Notification::make()
            //                 ->warning()
            //                 ->title('Whopps, cant do that :(')
            //                 ->body("There is no student in {$value->classroom->classroom_name}, please check the data")
            //                 ->send();
            //             break;
            //         }

            //         for($i=0; $i < count($getCLassroomStudentIds); $i++) {
            //             $dataArray[] = [
            //                 'student_id' => $getCLassroomStudentIds[$i],
            //                 'subject_user_id' => $value->id,
            //             ];
            //         }
            //     }
                
            //     if(DB::table('student_semester_evaluations')->insertOrIgnore($dataArray)){
            //         Notification::make()
            //             ->success()
            //             ->title('yeayy, success!')
            //             ->body('Successfully added data')
            //             ->send();
            //     }
            // }),
        ];
    }
}
