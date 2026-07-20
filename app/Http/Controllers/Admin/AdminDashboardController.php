<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Port;
use App\Models\User;
use App\Models\Article;
use App\Models\NewsArticle;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_countries' => Country::count(),
            'total_users' => User::count(),
            'total_ports' => Port::count(),
            'total_articles' => Article::count(),
            'total_news' => NewsArticle::count(),
            'high_risk_countries' => Country::whereHas('latestRiskScore', fn ($q) =>
                $q->where('risk_level', 'high'))->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}