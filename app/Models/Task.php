<?php

// app/Models/Task.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'deadline', 'reminder', 'priority', 'status', 'completed_at', 'projectId', 'quoteId'];

    protected $casts = [
        'status' => 'boolean',
        'completed_at' => 'datetime',
    ];

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

    // Otomatis atur completed_at saat status berubah
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($task) {
            if ($task->isDirty('status')) {
                if ($task->status) {
                    // Jika status menjadi true, set completed_at ke waktu saat ini
                    $task->completed_at = $task->completed_at ?? now();
                } else {
                    // Jika status menjadi false, hapus completed_at
                    $task->completed_at = null;
                }
            }
        });
    }
}
