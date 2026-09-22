<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'courses';

    // Disable automatic created_at/updated_at handling since the table does not have an updated_at column
    public $timestamps = false;

    protected $fillable = [
        'coursecode', 'name',
    ];

    protected $dates = ['deleted_at'];

    public function candidate()
    {
        return $this->hasOne(Candidate::class, 'course_id');
    }
}
