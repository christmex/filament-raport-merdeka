<?php

namespace App\Filament\Resources\StudentClassroomResource\Pages;

use Filament\Actions;
use App\Models\Student;
use Filament\Forms\Get;
use App\Models\Classroom;
use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use \App\Filament\Resources\SchoolTermResource;
use \App\Filament\Resources\SchoolYearResource;
use App\Filament\Resources\StudentClassroomResource;
use App\Models\HomeroomTeacher;
use App\Models\StudentClassroom;

class ListStudentClassrooms extends ListRecords
{
    protected static string $resource = StudentClassroomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
                        ->required(),
                ])
                ->action(function(array $data){
                    DB::beginTransaction();
                    try {
                        $studentClassrooms = StudentClassroom::with('homeroomTeacher')->get();
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
