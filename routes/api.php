<?php

use App\Http\Controllers\Api\CountryApiController;
use App\Http\Controllers\Api\CurrencyApiController;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\PortApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authenticated User
|--------------------------------------------------------------------------
|
| Endpoint bawaan Laravel untuk mendapatkan data user yang sedang login.
| Tetap dipertahankan karena merupakan route default dari Laravel.
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

/*
|--------------------------------------------------------------------------
| Public API Endpoints
|--------------------------------------------------------------------------
|
| Endpoint publik untuk kebutuhan integrasi/testing.
|
*/

Route::get('/countries', [CountryApiController::class, 'index']);
Route::get('/countries/{country:code}', [CountryApiController::class, 'show']);
Route::get('/risk', [CountryApiController::class, 'risk']);

Route::get('/ports', [PortApiController::class, 'index']);

Route::get('/news', [NewsApiController::class, 'index']);

Route::get('/currency', [CurrencyApiController::class, 'index']);