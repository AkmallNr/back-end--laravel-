<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;


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
        // Jika nilai profile_picture kosong (null atau string kosong)
        if (empty($value)) {
            return null; // Atau bisa return URL gambar default jika kamu mau
        }

        // Menyusun URL gambar dengan benar jika ada nilai
        return asset('storage/profile_pictures/' . $value);
    }


}
