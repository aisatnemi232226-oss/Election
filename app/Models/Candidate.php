<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Candidate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'candidate'; // Matches schema

    // Disable automatic timestamps since your table doesn't have updated_at
    public $timestamps = false;
    const UPDATED_AT = null;

    protected $fillable = [
        'name', 'id_number', 'course_id', 'electionparty_id', 'platforms',
        'description', 'status', 'profile_img', 'banner_img', 'created_at'
    ];

    protected $dates = ['deleted_at', 'created_at'];

    // Cast attributes to proper types
    protected $casts = [
        'deleted_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Override the save method to handle created_at manually
    public function save(array $options = [])
    {
        // Set created_at only when creating a new record
        if (!$this->exists && !$this->created_at) {
            $this->created_at = now();
        }
        
        return parent::save($options);
    }

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
        return $this->belongsTo(ElectionParty::class, 'electionparty_id');
    }

    public function electionArticles()
    {
        return $this->hasMany(ElectionArticle::class, 'candidate_id');
    }

    // Accessors for image URLs
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_img) {
            return asset('storage/upload/profile-picture/' . $this->profile_img);
        }
        return asset('images/default-profile.png'); // fallback image
    }

    public function getBannerImageUrlAttribute()
    {
        if ($this->banner_img) {
            return asset('storage/upload/banner/' . $this->banner_img);
        }
        return asset('images/default-banner.png'); // fallback image
    }

    // Scope for active candidates
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // Scope for candidates with course
    public function scopeWithCourse($query)
    {
        return $query->with('course');
    }

    // Scope for candidates with party
    public function scopeWithParty($query)
    {
        return $query->with('party');
    }
}