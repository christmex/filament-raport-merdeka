<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SchoolYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function setSchoolYearNameAttribute($value)
    {
        $this->attributes['school_year_name'] = ucwords($value);
    }

    public static function boot()
    {
        parent::boot();
        static::updated(function($obj) {
            if(($obj->original['school_year_status'] == false) && $obj->changes['school_year_status']){
                SchoolYear::where('id','!=', $obj->id)->update(['school_year_status' => false]);    
            }
        });
    }

    public static function active(){
        return self::where('school_year_status', true)->pluck('id')->first();
    }
}
