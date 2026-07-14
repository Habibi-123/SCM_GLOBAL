<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id', 'title', 'url', 'source', 'category',
        'positive_count', 'negative_count', 'sentiment', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}