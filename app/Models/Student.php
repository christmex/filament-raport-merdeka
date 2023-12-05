<?php

namespace App\Models;

use App\Models\StudentAbsence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */


    protected $casts = [
        'joined_at' => 'datetime'
    ];

    /**
     * The "booted" method of the model.
     */
    // protected static function booted(): void
    // {
    //     static::addGlobalScope('ownStudent', function (Builder $builder) {
    //         if(auth()->id()){
    //             // dd(model::activeStudentClassrooms());
    //             // dd();
    //             // dd(auth()->user()->activeHomeroom->first()->id);
                
    //             $builder
    //         }
    //     });
    // }

    /**
     * Scope a query to only include popular users.
     */
    public function scopeOwnStudent(Builder $query): void
    {
        if(auth()->user()->activeHomeroom->count()){
            // dd(auth()->user()->activeHomeroom, $query);
            // $query->whereHas("acti", function (Builder $query) {
            // $query->where('school_year_id',auth()->user()->activeHomeroom->school_year_id)
            //     ->where('school_term_id',auth()->user()->activeHomeroom->school_year_id)
            //     ->where('classroom_id',auth()->user()->activeHomeroom->classroom_id);
            
            // dd(auth()->user()->activeHomeroom);
            $query->whereIn('id',StudentClassroom::where('school_year_id',auth()->user()->activeHomeroom->first()->school_year_id)
            ->where('school_term_id',auth()->user()->activeHomeroom->first()->school_term_id)
            ->where('classroom_id',auth()->user()->activeHomeroom->first()->classroom_id)
            ->get()->pluck('student_id')->toArray());
        }
    }
 

    public function setStudentNameAttribute($value)
    {
        $this->attributes['student_name'] = ucwords($value);
    }

    public function studentClassrooms(): HasMany
    {
        return $this->hasMany(StudentClassroom::class);
    }
    public function activeStudentClassrooms(): HasMany
    {
        return $this->studentClassrooms()->latest();
    }   


    // public function classrooms(): BelongsToMany
    // {
    //     return $this->belongsToMany(Classroom::class,'student_classrooms','student_id','classroom_id');
    // }
    public function classrooms(): HasMany
    {
        return $this->HasMany(StudentClassroom::class);
    }

    public function activeClassroom()
    {
        // return 'as';
        return $this->classrooms()->where('school_year_id', SchoolYear::active())
        ->where('school_term_id', SchoolTerm::active())->latest();


        // return StudentClassroom::where('school_year_id', SchoolYear::active())
        //         ->where('school_term_id', SchoolTerm::active());
        // return $this->hasMany(StudentClassroom::class)
        //     ->where('school_year_id', SchoolYear::active())
        //     ->where('school_term_id', SchoolTerm::active())
        //     ->first()
        //     ;
    }

    public function latestClassroom(){
        return $this->classrooms()->latest();
    }

    public function getActiveClassroomNameAttribute()
    {
        // return 'as';
        // return $this->activeStudentClassrooms->first()->homeroomTeacher->classroom->classroom_name;
        // dd($this->activeStudentClassrooms);

        $activeClassroom = $this->activeStudentClassrooms->first(function ($activeSubject) {
            return $activeSubject->classroom->is_moving_class === false;
        });
        // dd($activeClassroom);

        // return $activeClassroom;
        return $activeClassroom->classroom->classroom_name;
        // return $this->activeClassroom->where('is_moving_class',0)->first()->classroom_name;
    }
    public function getActiveClassroomFaseAttribute()
    {
        $activeClassroom = $this->activeStudentClassrooms->first(function ($activeSubject) {
            return $activeSubject->classroom->is_moving_class === false;
        });
        return $activeClassroom->classroom->fase;
    }

    public function getActiveClassroomLevelAttribute()
    {
        // return 'as';
        // return $this->activeStudentClassrooms->first()->homeroomTeacher->classroom->school_level;
        return $this->activeStudentClassrooms->first()->classroom->school_level;
    }

    public function getStudentNameWithClassroomAttribute()
    {
        // return 'as';
        
        return $this->student_name.' - '.$this->active_classroom_name;
    }

    public function religion():BelongsTo
    {
        return $this->belongsTo(Religion::class);
    }

    public function extracurriculars():HasMany
    {
        return $this->HasMany(StudentExtracurricular::class);
    }
    public function absence():HasMany
    {
        return $this->HasMany(StudentAbsence::class);
    }
    public function characterReport():HasMany
    {
        return $this->HasMany(CharacterReport::class);
    }
    public function activeAbsence():HasMany
    {
        return $this->absence()->where('school_year_id', SchoolYear::active())
        ->where('school_term_id', SchoolTerm::active())->latest();
    }

    public function activeExtracurriculars()
    {
        return $this->extracurriculars()->where('school_year_id', SchoolYear::active())
        ->where('school_term_id', SchoolTerm::active())->latest();
    }
    public function activeCharacterReport()
    {
        return $this->characterReport()->where('school_year_id', SchoolYear::active())
        ->where('school_term_id', SchoolTerm::active())->latest();
    }

    public function forRaport(){
        // $activeClassroom = $this->activeStudentClassrooms->first(function ($activeSubject) {
        //     return $activeSubject->classroom->is_moving_class === false;
        // });
        // // dd($activeClassroom);

        // // return $activeClassroom;
        // return $activeClassroom->classroom->classroom_name;
    }
}
