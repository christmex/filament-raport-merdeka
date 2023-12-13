<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use IbrahimBougaoua\FilamentSortOrder\Traits\SortOrder;

class Subject extends Model
{
    use HasFactory, SoftDeletes, SortOrder;

    protected $guarded = [];

    // public function setSubjectNameAttribute($value)
    // {
    //     $this->attributes['subject_name'] = ucwords($value);
    // }

    public function subjectGroup() :BelongsTo
    {
        return $this->belongsTo(SubjectGroup::class);
    }
}
