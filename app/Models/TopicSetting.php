<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TopicSetting extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function setTopicSettingNameAttribute($value)
    {
        $this->attributes['topic_setting_name'] = ucwords($value);
    }
}
