<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\NewsArticle;
use App\Services\CountryNewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected array $categories = ['logistics', 'trade', 'shipping', 'economy', 'geopolitics'];

    public function index(Request $request, CountryNewsService $countryNewsService)
    {
        $selectedCategory = $request->input('category');
        $selectedCountryCode = $request->input('country');
        $countrySearch = $request->input('country_search');

        $selectedCountry = null;

        // Mode 1: user memilih negara → fetch/pakai cache berita spesifik negara itu
        if ($selectedCountryCode) {
            $selectedCountry = Country::where('code', $selectedCountryCode)->first();

            if ($selectedCountry) {
                $countryNews = $countryNewsService->getNewsForCountry($selectedCountry);

                // Bungkus jadi paginator manual supaya view bisa pakai pola yang sama
                $articles = new \Illuminate\Pagination\LengthAwarePaginator(
                    $countryNews,
                    $countryNews->count(),
                    15,
                    1,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                $sentimentSummary = $countryNews->countBy('sentiment');

                return view('news.index', compact(
                    'articles', 'sentimentSummary', 'selectedCategory',
                    'selectedCountry', 'countrySearch'
                ))->with('categories', $this->categories);
            }
        }

        // Mode 2: tampilan default, filter berdasarkan kategori (perilaku lama, tetap dipertahankan)
        $articles = NewsArticle::query()
            ->when($selectedCategory, fn ($q) => $q->where('category', $selectedCategory))
            ->orderByDesc('published_at')
            ->paginate(15)
            ->withQueryString();

        $sentimentSummary = NewsArticle::query()
            ->when($selectedCategory, fn ($q) => $q->where('category', $selectedCategory))
            ->selectRaw('sentiment, count(*) as total')
            ->groupBy('sentiment')
            ->pluck('total', 'sentiment');

        return view('news.index', compact(
            'articles', 'sentimentSummary', 'selectedCategory',
            'selectedCountry', 'countrySearch'
        ))->with('categories', $this->categories);
    }

    // Endpoint AJAX untuk autocomplete pencarian negara
    public function searchCountries(Request $request)
    {
        $search = $request->input('q', '');

        $countries = Country::where('name', 'like', "%{$search}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['code', 'name', 'flag_url']);

        return response()->json($countries);
    }
}