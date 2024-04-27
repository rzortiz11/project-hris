<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Panel;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasName
{
    // use HasFactory, Notifiable, HasRoles, SoftDeletes; 
    use HasFactory, Notifiable, HasRoles; 

    protected $primaryKey = 'user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'suffix',
        'mobile',
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
    

    public function getFilamentName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /** Modify name attribute  */
    public function getNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /** Modify name attribute  */
    public function getFullnameAttribute()
    {
        return $this->first_name . ' '. $this->middle_name .' ' . $this->last_name;
    }

    /** Set allowed user to access panel */
    public function canAccessPanel(Panel $panel): bool
    {
    //    return str_ends_with($this->email, '@morepower.ph');
        return true;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'user_id', 'user_id');
    }
}
