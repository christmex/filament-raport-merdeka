<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function setTopicNameAttribute($value)
    {
        $this->attributes['topic_name'] = ucwords($value);
    }

    public function assessmentMethodSetting(): BelongsTo
    {
        return $this->belongsTo(AssessmentMethodSetting::class);
    }
    public function topicSetting(): BelongsTo
    {
        return $this->belongsTo(TopicSetting::class);
    }
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
