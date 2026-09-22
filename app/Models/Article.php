<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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

    protected $appends = ['featured_image_url'];

    public function elections()
    {
        return $this->belongsToMany(Election::class, 'election_articles', 'articles_id', 'election_id');
    }

    /**
     * Normalized public URL for the featured image.
     * Returns null if no image is set.
     */
    public function getFeaturedImageUrlAttribute()
    {
        $featured = $this->featured_image;

        if (empty($featured)) {
            return null;
        }

        if (Str::startsWith($featured, ['http://', 'https://'])) {
            return $featured;
        }

        // If the stored value points to storage/... (e.g. article-images or featured-images),
        // always prefer the Laravel streaming route to avoid broken public/storage symlinks.
        if (Str::contains($featured, 'article-images') || Str::contains($featured, 'featured-images')) {
            $file = basename($featured);
            $folder = Str::contains($featured, 'featured-images') ? 'featured-images' : 'article-images';

            return route('storage.image', ['folder' => $folder, 'filename' => $file]);
        }

        if (Str::startsWith($featured, ['/storage/', 'storage/'])) {
            $normalized = Str::startsWith($featured, '/storage/') ? ltrim($featured, '/') : $featured;
            $parts = explode('/', $normalized);
            $folder = $parts[1] ?? null;
            $file = end($parts) ?: null;

            if (!empty($folder) && !empty($file)) {
                return route('storage.image', ['folder' => $folder, 'filename' => $file]);
            }

            return asset($normalized);
        }

        return asset('storage/' . ltrim($featured, '/'));
    }

    protected static function booted()
    {
        static::creating(function ($article) {
            $article->created_at = now();
        });
    }
}
