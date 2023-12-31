<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Panel;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, HasPanelShield;

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

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = ucwords($value);
    }

    public function setEmailAttribute($value)
    {
        if(!(str_ends_with($value, '@sekolahbasic.sch.id'))){
            $this->attributes['email'] = $value.'@sekolahbasic.sch.id';
        }else{
            $this->attributes['email'] = $value;
        }
    }


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
