<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolTerm extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    public function setSchoolTermNameAttribute($value)
    {
        $this->attributes['school_term_name'] = ucwords($value);
    }

    public static function boot()
    {
        parent::boot();
        static::updated(function($obj) {
            SchoolTerm::where('id','!=', $obj->id)->update(['school_term_status' => false]);
        });
    }

    public static function active(){
        return self::where('school_term_status', true)->pluck('id')->first();
    }
}
