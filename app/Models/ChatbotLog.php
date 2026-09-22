<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotLog extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'message',
        'reply',
        'matched_intent_id',
    ];
}
