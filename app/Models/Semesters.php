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

    // Define relationships if needed, for example:
    public function verifiedVoters()
    {
        return $this->hasMany(VerifiedVoter::class, 'sem_id');
    }
}
