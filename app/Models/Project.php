<?php

// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'startDate', 'endDate', 'groupId', 'userId'];

    // Relasi ke Group (Many to One)
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'groupId');
    }

    // Relasi ke Task (One to Many)
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'projectId');
    }

    // Opsional: Jika kamu ingin mengatur format tanggal secara otomatis
    protected $casts = [
        'startDate' => 'datetime',
        'endDate' => 'datetime',
    ];
}

