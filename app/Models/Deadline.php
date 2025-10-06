<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deadline extends Model
{
    public function subtasks()
    {
        return $this->hasMany(Subtask::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'topic_id',
    ];
}
