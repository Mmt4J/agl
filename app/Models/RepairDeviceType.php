<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairDeviceType extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function pricing()
    {
        return $this->hasMany(RepairPricing::class);
    }
}
