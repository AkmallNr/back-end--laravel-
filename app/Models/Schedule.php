<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'notes', 'repeat', 'day', 'startTime', 'endTime', 'userId'];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
