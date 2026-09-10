<?php

namespace App\Models;

use Database\Factories\CustomerMeasurementFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** @use HasFactory<CustomerMeasurementFactory> */
class CustomerMeasurement extends Model
{
    use HasFactory;

    public const CODE_PREFIX = 'AGL-CUS-';

    protected $fillable = [
        'customer_code',
        'full_name',
        'phone',
        'email',
        'unit',
        'chest',
        'bust',
        'waist',
        'shoulder',
        'arm_length',
        'hip',
        'inseam',
        'thigh',
        'ankle',
        'height',
        'weight',
        'dress_size',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit' => 'string',
        ];
    }

    /**
     * Find records by customer code, name, phone, or email — used by both
     * the records search page and the "find existing customer" lookup on
     * the recording page.
     *
     * @return Builder<CustomerMeasurement>
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $needle = '%'.addcslashes($term, '%_').'%';

        return $query->where(function (Builder $q) use ($needle) {
            $q->where('customer_code', 'like', $needle)
                ->orWhere('full_name', 'like', $needle)
                ->orWhere('phone', 'like', $needle)
                ->orWhere('email', 'like', $needle);
        });
    }
}
