<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Election extends Model
{
    use SoftDeletes;

    protected $table = 'election';

    public $timestamps = false;


    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'reg_start',
        'reg_end',
        'campaign_start',
        'campaign_end',
        'result_date',
        'election_img',
        'admin_id',
        'academic_year_id',
        'candidates_group',
        'candidates_result_announcements',
        'result_visibility',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'reg_start' => 'date',
        'reg_end' => 'date',
        'campaign_start' => 'date',
        'campaign_end' => 'date',
        'result_date' => 'date',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'election_articles', 'election_id', 'articles_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'election_id');
    }

    public function positionVotes()
    {
        return $this->hasMany(PositionVote::class, 'election_id');
    }
}