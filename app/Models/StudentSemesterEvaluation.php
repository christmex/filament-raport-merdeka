<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StudentSemesterEvaluation extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $guarded = [];

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

}
