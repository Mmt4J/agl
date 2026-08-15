<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'blog_category_id', 'author_id', 'title', 'slug', 'excerpt',
        'body', 'featured_image', 'is_featured', 'read_time_minutes',
        'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'blog_post_tag');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /** Related posts: same category, excluding self — mirrors the front-end's related-articles logic. */
    public function related(int $limit = 2)
    {
        return static::published()
            ->where('blog_category_id', $this->blog_category_id)
            ->where('id', '!=', $this->id)
            ->limit($limit)
            ->get();
    }
}
