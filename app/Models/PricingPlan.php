<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingPlan extends Model
{
    protected $fillable = [
        'pricing_category_id', 'name', 'tagline', 'price_label',
        'period_label', 'is_highlighted', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_highlighted' => 'boolean'];
    }

    public function category()
    {
        return $this->belongsTo(PricingCategory::class, 'pricing_category_id');
    }

    public function features()
    {
        return $this->hasMany(PricingPlanFeature::class)->orderBy('sort_order');
    }
}
