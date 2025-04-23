<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectionArticle extends Model
{
    use HasFactory;

    protected $table = 'election_articles'; 

    protected $fillable = [
        'title',
        'author',
        'content',
        'election_id',
        'visible',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array',
        'visible' => 'boolean'
    ];

    public function election()
    {
        return $this->belongsTo(Election::class);
    }
}