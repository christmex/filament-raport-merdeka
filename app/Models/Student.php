<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function setStudentNameAttribute($value)
    {
        $this->attributes['student_name'] = ucwords($value);
    }

    public function studentClassrooms(): HasMany
    {
        return $this->hasMany(StudentClassroom::class);
    }
    public function activeStudentClassrooms(): HasMany
    {
        return $this->studentClassrooms()->latest();
    }


    public function classrooms(): BelongsToMany
    {
        return $this->belongsToMany(Classroom::class,'student_classrooms','student_id','classroom_id');
    }

    public function activeClassroom()
    {
        // return 'as';
        return $this->classrooms()->where('school_year_id', SchoolYear::active())
        ->where('school_term_id', SchoolTerm::active())->latest();


        // return StudentClassroom::where('school_year_id', SchoolYear::active())
        //         ->where('school_term_id', SchoolTerm::active());
        // return $this->hasMany(StudentClassroom::class)
        //     ->where('school_year_id', SchoolYear::active())
        //     ->where('school_term_id', SchoolTerm::active())
        //     ->first()
        //     ;
    }

    public function latestClassroom(){
        return $this->classrooms()->latest();
    }

    public function getActiveClassroomNameAttribute()
    {
        // return 'as';
        return $this->activeStudentClassrooms->first()->homeroomTeacher->classroom->classroom_name;
    }
}
