<?php

namespace App\Filament\Resources\AssessmentResource\Pages;

use Filament\Actions;
use Filament\Forms\Get;
use App\Models\Assessment;
use App\Models\SubjectUser;
use App\Models\StudentClassroom;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\AssessmentResource;
use App\Models\HomeroomTeacher;

class ManageAssessments extends ManageRecords
{
    protected static string $resource = AssessmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('Create Assessment')
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
                    ->preload(),
                TextInput::make('topic_name')
                    ->required(),
                Select::make('subject_user_id')
                    ->label('subject')
                    ->options(SubjectUser::with('subject')->whereIn('id',auth()->user()->activeSubjects->pluck('id')->toArray())->get()->pluck('subject_user_name', 'id'))
                    ->required()
                    ->searchable()
                    ->multiple()
                    // ->live()
                    ->selectablePlaceholder(false)
                    ->preload(),
                // Select::make('classroom_id')
                //     ->options(fn (Get $get): array => match ($get('category')) {
                //         'web' => [
                //             'frontend_web' => 'Frontend development',
                //             'backend_web' => 'Backend development',
                //         ],
                //         'mobile' => [
                //             'ios_mobile' => 'iOS development',
                //             'android_mobile' => 'Android development',
                //         ],
                //         'design' => [
                //             'app_design' => 'Panel design',
                //             'marketing_website_design' => 'Marketing website design',
                //         ],
                //         default => [],
                //     })

                //     // ->options(function(Get $get){
                //     //     $getSubjectId = SubjectUser::where('id', $get('subject_user_id'))->first()->subject_id;
                //     //     auth()->user()->activeSubjects->where('subject_id',$getSubjectId);

                //     // })
                //     ->required()
                //     ->searchable()
                //     ->preload()
                // ,
                
            ])
            ->action(function (array $data): void {
                // dd($data);
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
                    $getHomeroomTeacherIds = HomeroomTeacher::query()
                    ->where('classroom_id',$value->classroom_id)
                    ->where('school_year_id',$value->school_year_id)
                    ->where('school_term_id',$value->school_term_id)
                    // ->where('user_id',auth()->id()) //i comment this because it wont select if the subject teacher not the homeroom teacher, please review this later 
                    ->get()
                    ->pluck('id')
                    ->toArray()
                    ;

                    // dd($value,$getHomeroomTeacherIds);

                    $getCLassroomStudentIds = StudentClassroom::query()
                    ->whereIn('id',$getHomeroomTeacherIds)
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
