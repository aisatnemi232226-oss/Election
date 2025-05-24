<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionArticle extends Model
{
    use HasFactory;

    protected $table = 'election_articles'; 

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
        return $this->belongsTo(Election::class);
    }

    // Add this relationship
    public function article()
    {
        return $this->belongsTo(Article::class, 'articles_id');
    }
}