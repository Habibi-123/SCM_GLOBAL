<?php

namespace App\Console\Commands;

use App\Models\NewsArticle;
use App\Services\SentimentAnalysisService;
use Illuminate\Console\Command;

class AnalyzeNewsSentiment extends Command
{
    protected $signature = 'news:analyze';

    protected $description = 'Analisis sentimen semua berita berdasarkan lexicon kata positif/negatif';

    public function handle(SentimentAnalysisService $service): int
    {
        $articles = NewsArticle::all();

        if ($articles->isEmpty()) {
            $this->error('Tidak ada berita untuk dianalisis. Jalankan news:sync dulu.');
            return self::FAILURE;
        }

        $this->info("Menganalisis sentimen {$articles->count()} berita...");
        $bar = $this->output->createProgressBar($articles->count());
        $bar->start();

        foreach ($articles as $article) {
            $result = $service->analyze($article->title);

            $article->update([
                'positive_count' => $result['positive_count'],
                'negative_count' => $result['negative_count'],
                'sentiment' => $result['sentiment'],
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Tampilkan ringkasan hasil, mirip contoh output di spesifikasi:
        // Positive: 60%, Neutral: 25%, Negative: 15%
        $total = $articles->count();
        $positive = NewsArticle::where('sentiment', 'positive')->count();
        $neutral = NewsArticle::where('sentiment', 'neutral')->count();
        $negative = NewsArticle::where('sentiment', 'negative')->count();

        $this->info('Analisis selesai!');
        $this->line(sprintf('Positive : %.1f%% (%d berita)', $positive / $total * 100, $positive));
        $this->line(sprintf('Neutral  : %.1f%% (%d berita)', $neutral / $total * 100, $neutral));
        $this->line(sprintf('Negative : %.1f%% (%d berita)', $negative / $total * 100, $negative));

        return self::SUCCESS;
    }
}