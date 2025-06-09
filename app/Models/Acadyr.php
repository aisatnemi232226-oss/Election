<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acadyr extends Model
{
    use HasFactory;

    protected $table = 'acadyr';  

    protected $fillable = [
        'name', 
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the formatted academic year name with semester
     */
    public function getFormattedNameAttribute(): string
    {
        // This will be used to combine semester and academic year
        return $this->name;
    }

    /**
     * Check if this academic year is active
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }
    
    // Define relationships if needed, for example:
    public function verifiedVoters()
    {
        return $this->hasMany(VerifiedVoter::class, 'acadyr_id');
    }
}
