<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Support\Facades\Cache;

class SentimentAnalysisService
{
    /**
     * Analisis 1 teks (judul/isi berita), kembalikan jumlah kata positif,
     * negatif, dan kesimpulan sentimennya.
     */
    public function analyze(string $text): array
    {
        // Cache daftar kata selama 1 jam, supaya tidak query database
        // berulang-ulang tiap kali analisis 1 berita (bisa dipanggil ratusan kali)
        $positiveWords = Cache::remember('sentiment.positive_words', 3600, function () {
            return PositiveWord::pluck('word')->map(fn ($w) => strtolower($w))->toArray();
        });

        $negativeWords = Cache::remember('sentiment.negative_words', 3600, function () {
            return NegativeWord::pluck('word')->map(fn ($w) => strtolower($w))->toArray();
        });

        // Pecah teks jadi kata-kata individual, buang tanda baca, lowercase semua
        $cleanText = strtolower($text);
        $cleanText = preg_replace('/[^\w\s]/', ' ', $cleanText); // hapus tanda baca
        $words = preg_split('/\s+/', trim($cleanText));

        $positiveCount = 0;
        $negativeCount = 0;

        foreach ($words as $word) {
            if (in_array($word, $positiveWords, true)) {
                $positiveCount++;
            }
            if (in_array($word, $negativeWords, true)) {
                $negativeCount++;
            }
        }

        $sentiment = match (true) {
            $positiveCount > $negativeCount => 'positive',
            $negativeCount > $positiveCount => 'negative',
            default => 'neutral',
        };

        return [
            'positive_count' => $positiveCount,
            'negative_count' => $negativeCount,
            'sentiment' => $sentiment,
        ];
    }
}