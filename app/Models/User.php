<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
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
        'phone',
        'license_number',
        'role_id',
        'requested_role_id',
        'registration_status',
        'is_active',
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
        'is_active' => 'boolean',
        'role_id' => 'integer',
        'requested_role_id' => 'integer',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function requestedRole()
    {
        return $this->belongsTo(Role::class, 'requested_role_id');
    }

    public function hasPermission(string $permission): bool
    {
        return $this->is_active
            && $this->role !== null
            && $this->role->permissions->contains('name', $permission);
    }

    public function isRole(string $role): bool
    {
        return $this->role !== null && $this->role->name === $role;
    }

    public function trips()
    {
        return $this->hasMany(Trip::class, 'driver_user_id');
    }
}
