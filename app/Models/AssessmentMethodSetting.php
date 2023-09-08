<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentMethodSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function setAssessmentMethodSettingNameAttribute($value)
    {
        $this->attributes['assessment_method_setting_name'] = ucwords($value);
    }
}
