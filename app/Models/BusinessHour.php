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

        return $now->format('H:i:s') >= $today->opens_at && $now->format('H:i:s') < $today->closes_at;
    }
}
