<?php

namespace Database\Seeders;

use App\Models\TranslationRequest;
use Illuminate\Database\Seeder;

class TranslationServiceSeeder extends Seeder
{
    public function run(): void
    {
        $requests = [
            ['lang' => 'English to Bengali', 'src' => 'en', 'tgt' => 'bn', 'type' => 'legal', 'cert' => 'certified', 'pages' => 5, 'price' => 300],
            ['lang' => 'Bengali to English', 'src' => 'bn', 'tgt' => 'en', 'type' => 'academic', 'cert' => 'notarized', 'pages' => 10, 'price' => 250],
            ['lang' => 'English to Arabic', 'src' => 'en', 'tgt' => 'ar', 'type' => 'business', 'cert' => 'standard', 'pages' => 3, 'price' => 350],
            ['lang' => 'Bengali to Arabic', 'src' => 'bn', 'tgt' => 'ar', 'type' => 'certificate', 'cert' => 'certified', 'pages' => 2, 'price' => 400],
            ['lang' => 'English to Spanish', 'src' => 'en', 'tgt' => 'es', 'type' => 'medical', 'cert' => 'certified', 'pages' => 8, 'price' => 280],
        ];

        foreach ($requests as $req) {
            $certFee = match($req['cert']) {
                'notarized' => 2000,
                'certified' => 1000,
                default => 0
            };

            TranslationRequest::create([
                'user_id' => 1,
                'source_language' => $req['src'],
                'target_language' => $req['tgt'],
                'document_type' => $req['type'],
                'certification_type' => $req['cert'],
                'page_count' => $req['pages'],
                'word_count' => $req['pages'] * 250,
                'urgency' => 'standard',
                'delivery_days' => 5,
                'required_by' => now()->addDays(7),
                'price_per_page' => $req['price'],
                'certification_fee' => $certFee,
                'urgency_fee' => 0,
                'total_amount' => ($req['price'] * $req['pages']) + $certFee,
                'status' => ['submitted', 'in_progress', 'completed'][rand(0, 2)],
                'payment_status' => 'paid',
                'paid_at' => now()->subDays(rand(1, 10)),
                'submitted_at' => now()->subDays(rand(1, 10)),
                'ip_address' => '127.0.0.1',
            ]);
        }

        echo "✓ Created 5 translation requests\n";
    }
}
