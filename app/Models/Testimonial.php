<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Testimonial extends Model
{
    protected $fillable = [
        'client_name', 'client_role', 'image_path', 'quote', 'rating',
        'is_approved', 'sort_order',
    ];

    public function imageUrl(): ?string
    {
        if (blank($this->image_path)) {
            return null;
        }

        // Stored on this codebase's public disk, or an absolute URL/link the
        // admin supplied directly.
        return Str::startsWith($this->image_path, ['http://', 'https://', '/'])
            ? $this->image_path
            : Storage::disk('public')->url($this->image_path);
    }

    protected function casts(): array
    {
        return ['is_approved' => 'boolean'];
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true)->orderBy('sort_order');
    }
}
