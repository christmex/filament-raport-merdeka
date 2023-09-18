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
            if(($obj->original['school_term_status'] == false) && $obj->changes['school_term_status']){
                SchoolTerm::where('id','!=', $obj->id)->update(['school_term_status' => false]);
                request()->session()->put('active_school_term_id',$obj->id);
            }

            if($obj->original['school_term_status'] && ($obj->changes['school_term_status'] == false)){
                request()->session()->forget('active_school_term_id');
            }
            
        });

        // if the school year saved data and the school_term_status == true then set it to session
        static::saved(function($obj) {
            if($obj->school_term_status){
                SchoolTerm::where('id','!=', $obj->id)->update(['school_term_status' => false]);
                request()->session()->put('active_school_term_id',$obj->id);
            }
        });

        // If the school year get deleted, then delete the sessions
        static::deleted(function() {
            request()->session()->forget('active_school_term_id');
        });

    }

    public static function active(){
        return self::where('school_term_status', true)->pluck('id')->first();
    }

    public static function activeId(){
        return self::where('school_term_status', true)->pluck('id')->first();
    }
}
