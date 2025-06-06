<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'candidate';

    const UPDATED_AT = null;

    protected $fillable = [
        'name', 'id_number', 'course_id', 'electionparty_id', 'platforms',
        'description', 'status', 'profile_img', 'banner_img',
    ];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    
    public function party()
    {
        return $this->belongsTo(ElectionParty::class, 'electionparty_id', 'id');
    }
}

