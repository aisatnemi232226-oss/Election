<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'description', 'start_date', 'end_date',
        'reg_start', 'reg_end', 'campaign_start', 'campaign_end',
        'result_date', 'election_img', 'admin_id'
    ];

    /**
     * Define relationships
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function voters()
    {
        return $this->hasMany(Voter::class);
    }

    /**
     * Cast attributes to their native types
     */
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'reg_start' => 'datetime',
        'reg_end' => 'datetime',
        'campaign_start' => 'datetime',
        'campaign_end' => 'datetime',
        'result_date' => 'datetime',
    ];

    /**
     * Accessor to format the start date
     */
    public function getFormattedStartDateAttribute()
    {
        return $this->start_date->format('F j, Y');
    }
}
