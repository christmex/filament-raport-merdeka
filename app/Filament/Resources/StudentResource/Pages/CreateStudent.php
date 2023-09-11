<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Models\SchoolTerm;
use App\Models\SchoolYear;
use App\Models\StudentClassroom;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\StudentResource;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $student = static::getModel()::create($data);
        
        // $getUserHomerooms = auth()->user()->homerooms
        //     ->where('school_year_id', SchoolYear::active())
        //     ->where('school_term_id', SchoolTerm::active())
        //     ->first();
        $getUserHomerooms = auth()->user()->activeHomeroom->first();
        if($getUserHomerooms){
            StudentClassroom::create([
                'student_id' => $student->id,
                'homeroom_teacher_id' => $getUserHomerooms->id,
                // 'classroom_id' => $getUserHomerooms->classroom_id,
                // 'school_year_id' => $getUserHomerooms->school_year_id,
                // 'school_term_id' => $getUserHomerooms->school_term_id,
            ]);
        }

        return $student;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
