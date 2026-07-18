<?php

namespace App\Console\Commands;

use App\Models\NewsArticle;
use App\Services\GNewsService;
use Illuminate\Console\Command;

class SyncNews extends Command
{
    protected $signature = 'news:sync';

    protected $description = 'Sinkronisasi berita per kategori dari GNews API (hemat kuota: 5 request/hari)';

    // Kata kunci pencarian untuk tiap kategori, disesuaikan supaya hasilnya relevan
    protected array $categoryQueries = [
        'logistics'    => 'supply chain logistics',
        'trade'        => 'international trade',
        'shipping'     => 'shipping industry port',
        'economy'      => 'global economy',
        'geopolitics'  => 'geopolitical conflict',
    ];

    public function handle(GNewsService $service): int
    {
        $this->info('Mengambil berita per kategori dari GNews API...');
        $totalSaved = 0;

        foreach ($this->categoryQueries as $category => $query) {
            $this->line("Kategori: {$category} ({$query})");

            $articles = $service->search($query, max: 10);

            foreach ($articles as $item) {
                NewsArticle::updateOrCreate(
                    ['url' => $item['url']], // cegah duplikat kalau command dijalankan lagi
                    [
                        'title' => $item['title'] ?? '-',
                        'source' => $item['source']['name'] ?? null,
                        'category' => $category,
                        'published_at' => $item['publishedAt'] ?? null,
                    ]
                );
                $totalSaved++;
            }

            // Jeda 1.5 detik antar kategori, aman di bawah limit 1 request/detik dari GNews
            sleep(1);
        }

        $this->newLine();
        $this->info("Sinkronisasi selesai! Total berita tersimpan/diperbarui: {$totalSaved}");
        $this->comment('Catatan: kolom sentiment belum diisi — itu akan dihitung di tahap Sentiment Analysis berikutnya.');

        return self::SUCCESS;
    }
}