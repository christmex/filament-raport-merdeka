<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolTerm extends Model
{
    use HasFactory;

    protected $guarded = [];

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
