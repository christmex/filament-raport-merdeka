<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function setClassroomNameAttribute($value)
    {
        $this->attributes['classroom_name'] = ucwords($value);
    }
}
