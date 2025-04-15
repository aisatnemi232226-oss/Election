<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VerifiedVoter extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'verified_voters';

    protected $fillable = [
        'users_id', 
        'acadyr_id',  // Added for Academic Year filtering
        'sem_id',      // Added for filtering by Semester
    ];

    // Relationship with User
    public function users()
    {
        return $this->belongsTo(User::class, 'users_id'); 
    }

    // Relationship with Academic Year
    public function acadyr()
    {
        return $this->belongsTo(Acadyr::class, 'acadyr_id');
    }

    // Relationship with Semester
    public function semesters()
    {
        return $this->belongsTo(Semesters::class, 'sem_id');
    }
}
