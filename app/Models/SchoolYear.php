<?php

namespace App\Models;

use Illuminate\Http\Request;
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
                request()->session()->put('active_school_year_id',$obj->id);   
            }

            if($obj->original['school_year_status'] && ($obj->changes['school_year_status'] == false)){
                request()->session()->forget('active_school_year_id');
            }
        });

        // Remove active school status session
        // static::saving(function($obj) {
        //     request()->session()->forget('active_school_year_id');
        // });

        // if the school year saved data and the school_year_status == true then set it to session
        static::created(function($obj) {
            if($obj->school_year_status){
                SchoolYear::where('id','!=', $obj->id)->update(['school_year_status' => false]);
                request()->session()->put('active_school_year_id',$obj->id);
            }
        });

        // If the school year get deleted, then delete the sessions
        static::deleted(function() {
            request()->session()->forget('active_school_year_id');
        });
        
        
    }

    public static function active(){
        return self::where('school_year_status', true)->pluck('id')->first();
    }

    public static function activeId(){
        return self::where('school_year_status', true)->pluck('id')->first();
    }
}
