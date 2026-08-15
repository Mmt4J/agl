<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'client_name', 'client_role', 'quote', 'rating',
        'is_approved', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_approved' => 'boolean'];
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true)->orderBy('sort_order');
    }
}
