<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type'];

    /** Setting::get('company.rc_number') — cached, type-cast lookup. */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting.$key", function () use ($key, $default) {
            $row = static::where('key', $key)->first();

            if (! $row) {
                return $default;
            }

            return match ($row->type) {
                'number' => (float) $row->value,
                'boolean' => filter_var($row->value, FILTER_VALIDATE_BOOLEAN),
                'json' => json_decode($row->value, true),
                default => $row->value,
            };
        });
    }
}
