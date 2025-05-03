<?php

// app/Models/Task.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'deadline', 'reminder', 'priority', 'status', 'projectId', 'quoteId'];

    // Relasi ke Project (Many to One)
    public function project()
    {
        return $this->belongsTo(Project::class, 'projectId');
    }

    // Relasi ke Attachment (One to Many)
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'taskId');
    }
}
