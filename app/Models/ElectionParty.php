<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ElectionParty extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'electionparty';

    public $timestamps = false;

    protected $fillable = [
        'name', 'description', 'logo', 'status',
    ];

    protected $dates = ['deleted_at'];
    

    public function candidate()
    {
        return $this->hasMany(Candidate::class, 'electionparty_id'); // Correct foreign key name
    }
}
