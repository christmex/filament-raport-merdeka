<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// class StudentExtracurricular extends Model
class StudentExtracurricular extends Pivot
{
    use HasFactory;
    
    protected $guarded = [];

    public function student() :BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolYear() :BelongsTo
    {
        return $this->belongsTo(SchoolYear::class);
    }
    public function schoolTerm() :BelongsTo
    {
        return $this->belongsTo(SchoolTerm::class);
    }
    public function extracurricular() :BelongsTo
    {
        return $this->belongsTo(Extracurricular::class);
    }
}
