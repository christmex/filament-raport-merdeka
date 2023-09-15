<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function homerooms(): HasMany
    {
        return $this->hasMany(HomeroomTeacher::class);
    }

    public function activeHomeroom(){
        return $this->homerooms()->where('school_year_id', SchoolYear::active())->where('school_term_id', SchoolTerm::active())->latest();
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(SubjectUser::class);
    }
    public function activeSubjects(): HasMany
    {
        return $this->subjects()->where('school_year_id', SchoolYear::active())->where('school_term_id', SchoolTerm::active())->latest();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return str_ends_with($this->email, '@sekolahbasic.sch.id');
    }
}
