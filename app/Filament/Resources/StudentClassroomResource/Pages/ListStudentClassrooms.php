<?php

namespace App\Filament\Resources\StudentClassroomResource\Pages;

use Filament\Actions;
use App\Models\Student;
use Filament\Forms\Get;
use App\Models\Classroom;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\HomeroomTeacher;
use App\Models\StudentClassroom;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ClassroomResource;
use \App\Filament\Resources\SchoolTermResource;
use \App\Filament\Resources\SchoolYearResource;
use App\Filament\Resources\StudentClassroomResource;

class ListStudentClassrooms extends ListRecords
{
    protected static string $resource = StudentClassroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Actions\Action::make('classup')->color('primary')
                ->visible(true)
                ->form([
                    \Filament\Forms\Components\Group::make([
                        \Filament\Forms\Components\Select::make('previous_school_year_id')
                            ->relationship('schoolYear', 'school_year_name')
                            ->label('Previous school year')
                            ->searchable(['school_year_name'])
                            ->preload()
                            ->live()
                            ->createOptionForm(SchoolYearResource::getForm())
                            ->editOptionForm(SchoolYearResource::getForm())
                            ->default(fn($state) => $state ?? SchoolYear::activeId())
                            ->required(),
                        \Filament\Forms\Components\Select::make('previous_school_term_id')
                            ->relationship('schoolTerm', 'school_term_name')
                            ->label('Previous school Term')
                            ->searchable(['school_term_name'])
                            ->preload()
                            ->live()
                            ->createOptionForm(SchoolTermResource::getForm())
                            ->editOptionForm(SchoolTermResource::getForm())
                            ->default(fn($state) => $state ?? SchoolTerm::activeId())
                            ->required(),
                        \Filament\Forms\Components\Select::make('previous_classroom_id')
                            ->relationship('classroom', 'classroom_name')
                            ->label('Previous Classroom')
                            ->searchable(['classroom_name'])
                            ->preload()
                            ->live()
                            ->createOptionForm(ClassroomResource::getForm())
                            ->editOptionForm(ClassroomResource::getForm())
                            ->default(fn($state) => $state)
                            ->required(),
                        
                        \Filament\Forms\Components\CheckboxList::make('student_id')
                            ->label('Students')
                            ->options(function(Get $get){
                                $studentIds = StudentClassroom::query()
                                ->where('classroom_id',$get('previous_classroom_id'))
                                ->where('school_year_id',$get('previous_school_year_id'))
                                ->where('school_term_id',$get('previous_school_term_id'))
                                ->get()
                                ->pluck('student_id')
                                ->toArray();
        
                                return Student::whereIn('id',$studentIds)->get()->pluck('student_name','id');
                            })
                            // ->default(fn (CheckboxList $component): array => dd($component))
                            ->searchable()
                            ->bulkToggleable()
                            ->columns(3)
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Select::make('school_year_id')
                            ->relationship('schoolYear', 'school_year_name')
                            ->searchable(['school_year_name'])
                            ->preload()
                            ->live()
                            ->required(),
                        \Filament\Forms\Components\Select::make('school_term_id')
                            ->relationship('schoolTerm', 'school_term_name')
                            ->searchable(['school_term_name'])
                            ->preload()
                            ->live()
                            ->required(),
                        \Filament\Forms\Components\Select::make('classroom_id')
                            ->relationship('classroom', 'classroom_name')
                            ->searchable(['classroom_name'])
                            ->preload()
                            ->live()
                            ->required(),
                        // \Filament\Forms\Components\Select::make('main_teacher')
                        //     ->options(fn()=> HomeroomTeacher::query()
                        //         ->get()
                        //         ->pluck('user_id','id')
                        //     )
                        //     ->default(function(Get $get){
                        //         $homeroom = HomeroomTeacher::where('school_year_id',$get('previous_school_year_id'))
                        //         ->where('school_term_id',$get('previous_school_term_id'))
                        //         ->where('classroom_id',$get('previous_classroom_id'))
                        //         ->first();
                        //         if($homeroom != null){return $homeroom->user_id;}
                        //         // if($get('classroom_id') != null){
                        //         //     dd(22);
                        //         //     HomeroomTeacher::query()
                        //         //     ->where('school_year_id',$get('previous_school_year_id'))
                        //         //     ->where('school_term_id',$get('previous_school_term_id'))
                        //         //     ->where('classroom_id',$get('previous_classroom_id'))
                        //         //     ->first()->user_id;
                        //         // }else {
                        //         //     dd('sss');
                        //         // }
                        //     })
                        //     ->required()
                        //     ->columnSpanFull(),
                    ])
                    ->columns(3)
                ])
                ->action(function(array $data){
                    DB::beginTransaction();
                    try {
                        foreach ($data['student_id'] as $value) {
                            StudentClassroom::firstOrCreate(
                                [
                                    'school_year_id' => $data['school_year_id'],
                                    'school_term_id' => $data['school_term_id'],
                                    'classroom_id' => $data['classroom_id'],
                                    'student_id' => $value,
                                ],
                            );
                        }
                        DB::commit();
                        Notification::make()
                            ->success()
                            ->title('Successfully adding student to the new class')
                            ->send();
                    } catch (\Throwable $th) {
                        DB::rollback();
                        Notification::make()
                            ->danger()
                            ->title($th->getMessage())
                            ->send();
                    }
                }),
            Actions\Action::make('studentSync')->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('school_year_id')
                        ->live()
                        ->relationship('schoolYear', 'school_year_name')
                        ->searchable(['school_year_name'])
                        ->preload()
                        ->createOptionForm(SchoolYearResource::getForm())
                        ->editOptionForm(SchoolYearResource::getForm())
                        ->default(fn($state) => $state ?? SchoolYear::activeId())
                        ->required(),
                    \Filament\Forms\Components\Select::make('school_term_id')
                        ->live()
                        ->relationship('schoolTerm', 'school_term_name')
                        ->searchable(['school_term_name'])
                        ->preload()
                        ->createOptionForm(SchoolTermResource::getForm())
                        ->editOptionForm(SchoolTermResource::getForm())
                        ->default(fn($state) => $state ?? SchoolTerm::activeId())
                        ->required()
                        ->helperText('This function is to sync data, before we use the homeroom_teacher_id in student_classroom table now we move the school_year and school_term and classoom_id in student_classroom table direcly, so we dont longer use homeroom_teacher table'),
                ])
                ->action(function(array $data){
                    DB::beginTransaction();
                    try {
                        $studentClassrooms = StudentClassroom::with('homeroomTeacher')->where('homeroom_teacher_id','!=',NULL)->get();
                        foreach ($studentClassrooms as $studentClassroom) {
                            $studentClassroom->school_term_id = $data['school_term_id'];
                            $studentClassroom->school_year_id = $data['school_year_id'];
                            $studentClassroom->classroom_id = $studentClassroom->homeroomTeacher->classroom_id;
                            $studentClassroom->save();
                        }
                        DB::commit();
                        Notification::make()
                            ->success()
                            ->title('Student Sync With new Table Design')
                            ->send();
                    } catch (\Throwable $th) {
                        DB::rollback();
                        Notification::make()
                            ->danger()
                            ->title($th->getMessage())
                            ->send();
                    }
                })
        ];
    }
}
