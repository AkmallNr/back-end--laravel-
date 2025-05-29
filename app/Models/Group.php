<?php

// app/Models/Group.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon', 'userId'];

    // Accessor untuk icon
    public function getIconAttribute($value)
    {
        return $value ?? 'fas fa-users'; // Menetapkan nilai default 'fas fa-users' jika icon null
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'groupId');
    }
}
