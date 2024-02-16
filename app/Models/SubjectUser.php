<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubjectUser extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The "booted" method of the model.
     */
    // protected static function booted(): void
    // {
    //     static::addGlobalScope('OwnSubject', function (Builder $builder) {
    //         if(!auth()->user()->can('view_any_subject::user')){   
    //             $builder->where('user_id',auth()->id());
    //         }
    //     });
    // }


    
    /**
     * Scope a query to only include popular users.
     */
    public function scopeOwnSubject(Builder $query): void
    {
        // Check if the user it's not super admin with this permission
        if(!auth()->user()->can('view_any_subject::user')){
            $query->where('user_id',auth()->id())->where('school_year_id', SchoolYear::active())->where('school_term_id', SchoolTerm::active());
        }
    }

    public function subject() :BelongsTo
    {
        // return $this->belongsTo(Subject::class,'subject_id');
        return $this->belongsTo(Subject::class);
    }
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

    public function getSubjectUserNameAttribute()
    {
        return $this->subject->subject_name." - ".$this->classroom->classroom_name;
        // return !empty($this->subject->subject_name) ? "- ".$this->subject->subject_name : "";
        // return $this->department->department_name;
    }
}
