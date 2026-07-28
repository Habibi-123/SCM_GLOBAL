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

        $positiveWordsMap = array_flip($positiveWords);
        $negativeWordsMap = array_flip($negativeWords);
        $negations = ['not', 'no', 'never', 'neither', 'nor', 'without', 'lack', 'non'];

        $positiveCount = 0;
        $negativeCount = 0;
        $wordCount = count($words);

        for ($i = 0; $i < $wordCount; $i++) {
            $word = $words[$i];
            if (empty($word)) continue;

            $isNegated = ($i > 0 && in_array($words[$i - 1], $negations, true));

            if (isset($positiveWordsMap[$word])) {
                if ($isNegated) {
                    $negativeCount++;
                } else {
                    $positiveCount++;
                }
            } elseif (isset($negativeWordsMap[$word])) {
                if ($isNegated) {
                    $positiveCount++;
                } else {
                    $negativeCount++;
                }
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