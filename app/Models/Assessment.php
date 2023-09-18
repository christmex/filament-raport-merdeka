<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;

class Assessment extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $guarded = [];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('subjectUser', function (Builder $builder) {
            if(auth()->id()){     
                $builder->whereIn('subject_user_id',auth()->user()->activeSubjects->pluck('id')->toArray());
            }
        });
    }

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
    public function subjectUser(): BelongsTo
    {
        return $this->belongsTo(SubjectUser::class);
    }
    public function subjectUserThrough()
    {
        return $this->belongsToThrough(Subject::class, SubjectUser::class);
    }

    public function classroomSubjectUserThrough()
    {
        return $this->belongsToThrough(Classroom::class, SubjectUser::class);
    }

    
}
