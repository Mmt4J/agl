<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PortfolioProject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'portfolio_category_id', 'title', 'slug', 'summary',
        'body', 'image_path', 'is_featured', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_featured' => 'boolean'];
    }

    public function imageUrl(): ?string
    {
        if (blank($this->image_path)) {
            return null;
        }

        // Stored on this codebase's public disk, or an absolute URL/root
        // path the admin supplied directly.
        return Str::startsWith($this->image_path, ['http://', 'https://', '/'])
            ? $this->image_path
            : Storage::disk('public')->url($this->image_path);
    }

    public function category()
    {
        return $this->belongsTo(PortfolioCategory::class, 'portfolio_category_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}
