<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Election extends Model
{
    protected $table = 'election';
    
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
        'admin_id'
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
}