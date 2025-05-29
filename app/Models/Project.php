<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'startDate', 'endDate', 'groupId', 'userId'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'groupId');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'projectId');
    }

    protected $casts = [
        'startDate' => 'datetime',
        'endDate' => 'datetime',
    ];
}
