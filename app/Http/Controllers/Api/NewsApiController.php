<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsArticleResource;
use App\Models\NewsArticle;
use Illuminate\Http\Request;

class NewsApiController extends Controller
{
    // GET /api/news
    public function index(Request $request)
    {
        $news = NewsArticle::query()
            ->when($request->filled('category'), fn ($q) =>
                $q->where('category', $request->input('category')))
            ->when($request->filled('sentiment'), fn ($q) =>
                $q->where('sentiment', $request->input('sentiment')))
            ->orderByDesc('published_at')
            ->paginate($request->input('per_page', 15));

        return NewsArticleResource::collection($news);
    }
}