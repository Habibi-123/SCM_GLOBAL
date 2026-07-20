<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsArticleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'source' => $this->source,
            'category' => $this->category,
            'sentiment' => $this->sentiment,
            'published_at' => $this->published_at?->toIso8601String(),
        ];
    }
}