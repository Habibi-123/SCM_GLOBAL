<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherData extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id', 'temperature', 'rainfall', 'wind_speed',
        'storm_risk', 'fetched_at',
    ];

    protected $casts = [
        'fetched_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}