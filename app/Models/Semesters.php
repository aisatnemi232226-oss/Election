<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semesters extends Model
{
    use HasFactory;

    protected $table = 'semesters';

    protected $fillable = [
        'name',
    ];

        protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the formatted semester name
     */
    public function getFormattedNameAttribute(): string
    {
        return $this->name;
    }
    
    // Define relationships if needed, for example:
    public function verifiedVoters()
    {
        return $this->hasMany(VerifiedVoter::class, 'sem_id');
    }
}
