<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PricingCategory extends Model
{
    protected $fillable = ['name', 'slug', 'sort_order'];

    public function plans()
    {
        return $this->hasMany(PricingPlan::class)->orderBy('sort_order');
    }
}
