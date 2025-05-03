<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'profile_picture'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'userId');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'userId');
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'userId');
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getProfilePictureAttribute($value)
    {
        if (empty($value)) {
            return null; // atau URL default, misalnya: asset('storage/default.jpg')
        }
        // Kembalikan path relatif untuk fleksibilitas
        return 'profile_pictures/' . $value;
    }}
