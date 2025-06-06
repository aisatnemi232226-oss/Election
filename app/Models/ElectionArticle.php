<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionArticle extends Model
{
    use HasFactory;

    protected $table = 'election_articles';

    // Disable updated_at, keep created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'election_id',
        'candidate_id',
        'articles_id',
        'show_status'
    ];

    protected $casts = [
        'show_status' => 'boolean'
    ];

    public function election()
    {
        return $this->belongsTo(Election::class, 'election_id')->withTrashed();
    }

    public function articles()
    {
        return $this->belongsTo(Article::class, 'articles_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id')->withTrashed();
    }
}