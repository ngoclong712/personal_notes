<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    protected $fillable = [
        'title',
        'content',
        'topic_id',
        'status',
    ];
}
