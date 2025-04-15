<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionParty extends Model
{
    use HasFactory;

    protected $table = 'electionparty';

    protected $fillable = [
        'name', 'status',
    ];
    

    public function candidate()
    {
        return $this->hasMany(Candidate::class, 'electionparty_id'); // Correct foreign key name
    }
}
