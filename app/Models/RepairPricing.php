<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepairPricing extends Model
{
    // Table is 'repair_pricing' (singular) - "pricing" reads oddly pluralized,
    // so Eloquent's automatic repair_pricings guess is overridden here.
    protected $table = 'repair_pricing';

    protected $fillable = [
        'repair_device_type_id',
        'repair_issue_type_id',
        'price_min',
        'price_max',
    ];

    public function deviceType()
    {
        return $this->belongsTo(RepairDeviceType::class, 'repair_device_type_id');
    }

    public function issueType()
    {
        return $this->belongsTo(RepairIssueType::class, 'repair_issue_type_id');
    }

    /** "₦15,000 – ₦45,000" — matches the string the front-end estimator currently renders. */
    public function getFormattedRangeAttribute(): string
    {
        return '₦'.number_format($this->price_min).' – ₦'.number_format($this->price_max);
    }
}
