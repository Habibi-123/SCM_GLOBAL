<?php

namespace Database\Seeders;

use App\Models\NegativeWord;
use Illuminate\Database\Seeder;

class NegativeWordSeeder extends Seeder
{
    public function run(): void
    {
        $words = [
            // Konflik & keamanan
            'war', 'conflict', 'attack', 'attacks', 'violence', 'tension',
            'tensions', 'threat', 'threaten', 'threatened', 'invasion', 'military',
            'sanctions', 'sanction', 'terrorism', 'unrest',

            // Krisis ekonomi
            'crisis', 'inflation', 'recession', 'downturn', 'decline', 'declined',
            'drop', 'dropped', 'fall', 'fell', 'falling', 'plunge', 'plunged',
            'collapse', 'collapsed', 'slump', 'shortage', 'shortages', 'debt',
            'deficit', 'bankruptcy', 'bankrupt', 'default',

            // Gangguan operasional
            'delay', 'delayed', 'delays', 'disruption', 'disrupted', 'disrupt',
            'shutdown', 'closed', 'closure', 'strike', 'protest', 'protests',
            'congestion', 'blockage', 'blocked',

            // Bencana & risiko
            'disaster', 'storm', 'flood', 'flooding', 'damage', 'damaged',
            'risk', 'risky', 'volatile', 'volatility', 'uncertainty', 'unstable',
            'warning', 'emergency', 'layoffs', 'layoff', 'cut', 'cuts',
        ];

        foreach ($words as $word) {
            NegativeWord::updateOrCreate(['word' => strtolower($word)]);
        }

        $this->command->info(count($words) . ' kata negatif berhasil ditambahkan.');
    }
}