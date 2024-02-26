<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HomeroomTeacher extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user() :BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function schoolYear() :BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }
    public function schoolTerm() :BelongsTo
    {
        return $this->belongsTo(SchoolTerm::class);
    }
    public function classroom() :BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function getClassroomNameAttribute()
    {
        return $this->classroom->classroom_name." - ".$this->schoolYear->school_year_name." - ".$this->SchoolTerm->school_term_name;
        // return !empty($this->subject->subject_name) ? "- ".$this->subject->subject_name : "";
        // return $this->department->department_name;
    }
}
