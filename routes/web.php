<?php

use App\Http\Controllers\CountryComparisonController;
use App\Http\Controllers\CountryDashboardController;
use App\Http\Controllers\CurrencyDashboardController;
use App\Http\Controllers\DataVisualizationController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PortDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WatchlistController;
use App\Http\Controllers\WeatherMapController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // Route bawaan Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Country Dashboard
    Route::get('/countries', [CountryDashboardController::class, 'index'])
        ->name('countries.index');

    Route::get('/countries/{country:code}', [CountryDashboardController::class, 'show'])
        ->name('countries.show');

    // Port Dashboard
    Route::get('/ports', [PortDashboardController::class, 'index'])
        ->name('ports.index');

    Route::get('/ports/data', [PortDashboardController::class, 'data'])
        ->name('ports.data');

    // Weather Map
    Route::get('/weather', [WeatherMapController::class, 'index'])
        ->name('weather.index');

    Route::get('/weather/data', [WeatherMapController::class, 'data'])
        ->name('weather.data');

    // Currency Dashboard
    Route::get('/currency', [CurrencyDashboardController::class, 'index'])
        ->name('currency.index');

    // Data Visualization Dashboard
    Route::get('/visualization', [DataVisualizationController::class, 'index'])
        ->name('visualization.index');

    // Country Comparison
    Route::get('/compare', [CountryComparisonController::class, 'index'])
        ->name('compare.index');

    // News Intelligence
    Route::get('/news', [NewsController::class, 'index'])
        ->name('news.index');

    // Watchlist
    Route::get('/watchlist', [WatchlistController::class, 'index'])
        ->name('watchlist.index');

    Route::post('/watchlist/{country:code}', [WatchlistController::class, 'store'])
        ->name('watchlist.store');

    Route::delete('/watchlist/{country:code}', [WatchlistController::class, 'destroy'])
        ->name('watchlist.destroy');

    Route::get('/news/search-countries', [NewsController::class, 'searchCountries'])
        ->name('news.search-countries');

    Route::get('/countries/{country:code}', [CountryDashboardController::class, 'show'])
        ->name('countries.show');
    
    Route::post('/countries/{country:code}/refresh-news', [CountryDashboardController::class, 'refreshNews'])
        ->name('countries.refresh-news');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::resource('users', \App\Http\Controllers\Admin\UserManagementController::class)
            ->except(['show']);

        Route::resource('ports', \App\Http\Controllers\Admin\PortManagementController::class);

        Route::resource('articles', \App\Http\Controllers\Admin\ArticleManagementController::class);
    });

require __DIR__.'/auth.php';