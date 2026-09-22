<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = "users";

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'idnumber',
        'course',
        'year_level',
        'status',
        'admin_role',
        'profile_image',
        'is_admin',
    ];

    protected $attributes = [
        'idnumber' => '',  // Default empty string instead of null
        'is_admin' => false,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts()
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Define the relationship with the VerifiedVoter model.
     */
    public function verifiedVoter()
    {
        return $this->hasOne(VerifiedVoter::class, 'users_id');
    }

    public function candidate()
    {
        return $this->hasOne(Candidate::class, 'users_id');
    }
}
