<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function setSubjectNameAttribute($value)
    {
        $this->attributes['subject_name'] = ucwords($value);
    }
}
