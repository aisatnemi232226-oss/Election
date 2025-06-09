<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'systemlogs';
    
    protected $fillable = [
        'user_id',
        'log_type',
        'action',
        'description',
        'table_name',
        'record_id',
        'operation',
        'old_data',
        'new_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Get the user that owns the system log.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the formatted timestamp
     */
    public function getFormattedTimestampAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : '';
    }

    /**
     * Get the user name or fallback
     */
    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->name : 'System';
    }
}