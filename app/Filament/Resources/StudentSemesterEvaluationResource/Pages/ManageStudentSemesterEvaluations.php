<?php

namespace App\Filament\Resources\StudentSemesterEvaluationResource\Pages;

use Filament\Actions;
use App\Models\SubjectUser;
use App\Models\StudentClassroom;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\StudentSemesterEvaluationResource;

class ManageStudentSemesterEvaluations extends ManageRecords
{
    protected static string $resource = StudentSemesterEvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('Create Assessment By Classroom')
            ->button()
            ->form([
                Select::make('subject_user_id')
                ->label('subject')
                ->options(SubjectUser::with('subject')->whereIn('id',auth()->user()->activeSubjects->pluck('id')->toArray())->get()->pluck('subject_user_name', 'id'))
                ->required()
                ->searchable()
                ->multiple()
                // ->live()
                ->selectablePlaceholder(false)
                ->preload(),
            ])
            ->action(function (array $data): void {
                $dataArray = [];
                $selectSubjectUser = SubjectUser::with('classroom')->whereIn('id',$data['subject_user_id'])->get();

                foreach ($selectSubjectUser as $key => $value) {
                    $getCLassroomStudentIds = StudentClassroom::query()
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
                            'subject_user_id' => $value->id,
                        ];
                    }
                }
                
                if(DB::table('student_semester_evaluations')->insertOrIgnore($dataArray)){
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
