<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = null;
    const DELETED_AT = 'deleted_at';

    protected $table = 'articles';

    protected $fillable = [
        'title',
        'author',
        'content',
        'type',
        'visible',
        'featured_image',
        'tags', 
        'image_urls'
    ];

    protected $casts = [
        'tags' => 'array',
        'visible' => 'boolean',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function elections()
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
