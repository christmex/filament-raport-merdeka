<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentNotesByHomeroomTeacher extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('StudentActiveNotesByHomeroomTeacher', function (Builder $builder) {
            if(auth()->id()){     
                $builder->where('school_year_id', SchoolYear::active())->where('school_term_id', SchoolTerm::active());
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
}
