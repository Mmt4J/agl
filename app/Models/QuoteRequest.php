<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $fillable = [
        'service_id', 'preferred_date', 'details', 'full_name',
        'phone', 'email', 'status', 'internal_notes',
    ];

    protected function casts(): array
    {
        return ['preferred_date' => 'date'];
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
