<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TopicSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function setTopicSettingNameAttribute($value)
    {
        $this->attributes['topic_setting_name'] = ucwords($value);
    }
}
