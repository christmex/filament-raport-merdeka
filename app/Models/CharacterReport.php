<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CharacterReport extends Model
{
    use HasFactory;

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('ownStudent', function (Builder $builder) {
            if(auth()->id()){     
                $activeHomeroom = auth()->user()->activeHomeroom->first();
                $studentIds = [];
                if($activeHomeroom){
                    $studentIds = StudentClassroom::query()
                    ->where('classroom_id',$activeHomeroom->classroom_id)
                    ->where('school_year_id',$activeHomeroom->school_year_id)
                    ->where('school_term_id',$activeHomeroom->school_term_id)
                    ->get()
                    ->pluck('student_id')
                    ->toArray();

                    $studentIds = Student::whereIn('id',$studentIds)->get()->pluck('id')->toArray();
                }
                $builder->whereIn('student_id',$studentIds);
            }
        });
    }

    public function student() :BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear() :BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }
    public function schoolTerm() :BelongsTo
    {
        return $this->belongsTo(SchoolTerm::class);
    }
    public function habit() :BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
