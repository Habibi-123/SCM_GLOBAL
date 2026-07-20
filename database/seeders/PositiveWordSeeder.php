<?php

namespace Database\Seeders;

use App\Models\PositiveWord;
use Illuminate\Database\Seeder;

class PositiveWordSeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            // Pertumbuhan & performa ekonomi
            'growth', 'increase', 'rise', 'rising', 'grew', 'expand', 'expansion',
            'boost', 'boosted', 'surge', 'surged', 'rally', 'recovery', 'recover',
            'improve', 'improved', 'improvement', 'gain', 'gains', 'upturn',

            // Stabilitas & kepercayaan
            'stable', 'stability', 'strengthen', 'strong', 'strength', 'resilient',
            'resilience', 'confidence', 'optimistic', 'optimism', 'positive',

            // Bisnis & perdagangan
            'profit', 'profitable', 'profits', 'success', 'successful', 'record',
            'breakthrough', 'agreement', 'deal', 'partnership', 'cooperation',
            'investment', 'invest', 'funding', 'opportunity', 'opportunities',

            // Perdamaian & penyelesaian
            'peace', 'peaceful', 'resolution', 'resolve', 'resolved', 'ceasefire',
            'stabilize', 'stabilized', 'thrive', 'thriving', 'efficient', 'efficiency',
        ];

        foreach ($words as $word) {
            PositiveWord::updateOrCreate(['word' => strtolower($word)]);
        }

        $this->command->info(count($words) . ' kata positif berhasil ditambahkan.');
    }
}