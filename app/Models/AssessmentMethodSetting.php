<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentMethodSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function setAssessmentMethodSettingNameAttribute($value)
    {
        $this->attributes['assessment_method_setting_name'] = ucwords($value);
    }
}
