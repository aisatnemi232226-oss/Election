<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotIntent extends Model
{
    protected $fillable = [
        'name',
        'triggers',
        'reply',
        'quick_replies',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'triggers' => 'array',
        'quick_replies' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
    ];
}
