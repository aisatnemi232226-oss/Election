<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $table = 'courses';

    protected $fillable = [
        'coursecode', 'name',
    ];

    public function candidate()
    {
        return $this->hasOne(Candidate::class, 'courses_id');
    }
}
