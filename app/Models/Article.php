<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    public $timestamps = false;
    
    // Define custom timestamp columns that match your table
    const CREATED_AT = 'created_at';
    const UPDATED_AT = null; 
    const DELETED_AT = 'delete_at'; 
    
    protected $fillable = [
        'title',
        'author',
        'content',
        'type',
        'visible',
        'featured_image',
        'tags'
    ];

    protected $casts = [
        'tags' => 'array',
        'visible' => 'boolean',
        'created_at' => 'datetime',
        'delete_at' => 'datetime'
    ];
    
    public function election()
    {
        return $this->belongsToMany(Election::class, 'election_articles', 'articles_id', 'election_id');
    }
    
    protected static function booted()
    {
        static::creating(function ($article) {
            $article->created_at = now();
        });
    }
}