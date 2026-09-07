<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessHour extends Model
{
    protected $fillable = ['day_of_week', 'opens_at', 'closes_at', 'is_closed'];

    protected function casts(): array
    {
        return ['is_closed' => 'boolean'];
    }

    /** Server-side replacement for the prototype's client-only isOpenNow() check. */
    public static function isOpenNow(): bool
    {
        $now = now('Africa/Lagos');
        $today = static::where('day_of_week', $now->dayOfWeek)->first();

        if (! $today || $today->is_closed || ! $today->opens_at || ! $today->closes_at) {
            return false;
        }

        $opensAt = $now->copy()->setTimeFromTimeString($today->opens_at);
        $closesAt = $now->copy()->setTimeFromTimeString($today->closes_at);

        // Overnight span (e.g. opens 18:00, closes 02:00): closing time
        // is numerically "before" opening time on the same calendar day,
        // so the open window wraps past midnight. In that case we're
        // open if we're at/after opening OR still before closing -
        // rather than requiring both, which is what a same-day span needs.
        if ($closesAt->lessThanOrEqualTo($opensAt)) {
            return $now->greaterThanOrEqualTo($opensAt) || $now->lessThan($closesAt);
        }

        return $now->greaterThanOrEqualTo($opensAt) && $now->lessThan($closesAt);
    }
}