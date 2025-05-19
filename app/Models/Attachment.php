<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = ['taskId', 'file_name', 'file_url'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
