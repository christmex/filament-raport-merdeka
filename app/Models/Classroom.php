<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classroom extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function setClassroomNameAttribute($value)
    {
        $this->attributes['classroom_name'] = ucwords($value);
    }
}
