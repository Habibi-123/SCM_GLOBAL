<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// =====================================================
// AUTOMATIC REAL-TIME SCHEDULER
// Jalankan: php artisan schedule:work (lokal)
// atau aktifkan Cron Job di server production
// =====================================================

// Sinkronisasi cuaca setiap 3 jam
Schedule::command('sync:weather')->everyThreeHours()->withoutOverlapping();

// Sinkronisasi kurs mata uang setiap 6 jam
Schedule::command('sync:currency-rates')->everySixHours()->withoutOverlapping();

// Ambil berita terbaru setiap jam
Schedule::command('sync:news')->hourly()->withoutOverlapping();

// Analisis sentimen berita baru setiap jam (setelah sync:news)
Schedule::command('news:analyze-sentiment')->hourlyAt(5)->withoutOverlapping();

// Hitung ulang Risk Score semua negara setiap jam
Schedule::command('risk:calculate')->hourlyAt(10)->withoutOverlapping();

// Sinkronisasi indikator ekonomi 1x sehari (data World Bank tidak sering berubah)
Schedule::command('sync:economic-indicators')->dailyAt('02:00')->withoutOverlapping();
