<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'code_alpha2', 'currency_code', 'region',
        'capital', 'population', 'flag_url',
        'latitude', 'longitude',
    ];

    public function economicIndicators(): HasMany
    {
        return $this->hasMany(EconomicIndicator::class);
    }

    public function weatherData(): HasMany
    {
        return $this->hasMany(WeatherData::class);
    }

    public function riskScores(): HasMany
    {
        return $this->hasMany(RiskScore::class);
    }

    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }

    public function newsArticles(): HasMany
    {
        return $this->hasMany(NewsArticle::class);
    }

    public function watchedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'watchlists')
                     ->withTimestamps();
    }

    public function latestEconomicIndicator()
    {
        return $this->hasOne(EconomicIndicator::class)->latestOfMany('year');
    }

    public function latestRiskScore()
    {
        return $this->hasOne(RiskScore::class)->latestOfMany('calculated_at');
    }
}