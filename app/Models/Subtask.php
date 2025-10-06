<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    protected $fillable = [
        'deadline_id',
        'title',
        'content',
        'due_date',
        'status',
    ];
}
