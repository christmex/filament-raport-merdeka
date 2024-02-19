<?php

namespace App\Filament\Resources\CharacterReportResource\Pages;

use App\Models\Habit;
use Filament\Actions;
use App\Helpers\Helper;
use App\Models\Student;
use Filament\Forms\Get;
use App\Models\CharacterReport;
use App\Models\StudentClassroom;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\CharacterReportResource;

class ManageCharacterReports extends ManageRecords
{
    protected static string $resource = CharacterReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            // Actions\Action::make('Create Character Report By Student')
            // ->form([

            // ])
            // ->action(function(){

            // }),
            Actions\Action::make('Create Character Report '.auth()->user()->activeHomeroom->first()->classroom->classroom_name)
            ->form([
                CheckboxList::make('student_id')
                    ->label('Students')
                    ->options(function(Get $get){

                        $activeHomeroom = auth()->user()->activeHomeroom->first();
                        if($activeHomeroom){
                            $studentIds = StudentClassroom::query()
                            ->where('classroom_id',$activeHomeroom->classroom_id)
                            ->where('school_year_id',$activeHomeroom->school_year_id)
                            ->where('school_term_id',$activeHomeroom->school_term_id)
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
                CheckboxList::make('habit_id')
                    ->label('Habits')
                    ->options(function(Get $get){
                        return Habit::all()->pluck('name','id');
                    })
                    ->searchable()
                    ->bulkToggleable()
                    ->columns(3),
            ])
            ->button()
            ->color('info')
            ->action(function (array $data): void {
                $dataArray = [];
                $getCLassroomStudentIds = $data['student_id'];

                if(!count($getCLassroomStudentIds) || !count($data['habit_id'])){
                    Notification::make()
                        ->warning()
                        ->title('Whopps, cant do that :(')
                        ->body("No student selected or no habit selected")
                        ->send();
                }else{
                    $activeHomeroom = auth()->user()->activeHomeroom->first();
                    $check = CharacterReport::whereIn('student_id',$getCLassroomStudentIds)
                        ->where('school_year_id',$activeHomeroom->school_year_id)
                        ->where('school_term_id',$activeHomeroom->school_term_id)
                        ->get();
                    
                    if(!$check->count()){
                        $getHabits = Habit::whereIn('id',$data['habit_id'])->get();
                        // dd($getHabits);
                        for($i=0; $i < count($getCLassroomStudentIds); $i++) {
                            foreach ($getHabits as $value) {
                                for ($week=1; $week <= 17; $week++) { 
                                    $dataArray[] = [
                                        'student_id' => $getCLassroomStudentIds[$i],
                                        'school_year_id' => $activeHomeroom->school_year_id,
                                        'school_term_id' => $activeHomeroom->school_term_id,
                                        'habit_id' => $value->id,
                                        'week' => $week,
                                    ];
                                }
                            }
                        }
    
                        if(DB::table('character_reports')->insertOrIgnore($dataArray)){
                            Notification::make()
                                ->success()
                                ->title('yeayy, success!')
                                ->body('Successfully added data')
                                ->send();
                        }
                    }else{
                        dd('Data Exist');
                        // $arrayNames = array_unique($check->pluck('student.student_name')->toArray());
                        // $names ='';
                        // for ($i=0; $i < count($arrayNames); $i++) { 
                        //     $names .= $arrayNames[$i]."<br>";
                        // }
                        
                        // Notification::make()
                        // ->danger()
                        // ->title('Failed! data exist with selected subject')
                        // ->body($names)
                        // ->send();
                    }
                }
            })
        ];
    }

    public function getSubheading(): ?string
    {
        $classroom = null;
        if(auth()->user()){
            $classroom = 'Current School Year: '.Helper::getSchoolYearName().' - '.Helper::getSchoolTermName();
        }
        return $classroom;
    }
}
