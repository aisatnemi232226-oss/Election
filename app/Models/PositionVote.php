<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PositionVote extends Model
{
    use HasFactory;

    protected $table = 'position_votes';

    protected $fillable = [
        'user_id',
        'position_id',
        'candidate_id',
    ];
}
