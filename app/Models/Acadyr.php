<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Acadyr extends Model
{
    use HasFactory;

    protected $table = 'acadyr';  // The name of the academic years table

    protected $fillable = [
        'name', // For example, the academic year name like '2022-2023'
        'status',
    ];

    // Define relationships if needed, for example:
    public function verifiedVoters()
    {
        return $this->hasMany(VerifiedVoter::class, 'acadyr_id');
    }
}
