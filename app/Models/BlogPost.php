<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

    public function imageUrl(): ?string
    {
        if (blank($this->featured_image)) {
            return null;
        }

        // Stored on this codebase's public disk, or an absolute URL/root
        // path the admin supplied directly.
        return Str::startsWith($this->featured_image, ['http://', 'https://', '/'])
            ? $this->featured_image
            : Storage::disk('public')->url($this->featured_image);
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
            ->with('category')
            ->where('blog_category_id', $this->blog_category_id)
            ->where('id', '!=', $this->id)
            ->limit($limit)
            ->get();
    }
}
