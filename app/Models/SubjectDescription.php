<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubjectDescription extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;

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

    protected $guarded = [];

    public function subjectUser(): BelongsTo
    {
        return $this->belongsTo(SubjectUser::class);
    }

    public function topicSetting(): BelongsTo
    {
        return $this->belongsTo(TopicSetting::class);
    }

    public function subjectUserThrough()
    {
        return $this->belongsToThrough(Subject::class, SubjectUser::class);
    }
}
