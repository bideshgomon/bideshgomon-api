<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ServiceApplication;
use App\Models\ServiceModule;
use App\Models\ServiceQuote;
use App\Models\User;
use App\Models\AgencyCountryAssignment;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

echo "=== CREATING MULTI-SERVICE TEST DATA ===" . PHP_EOL . PHP_EOL;

DB::beginTransaction();

try {
    // Get or create test user
    $user = User::where('email', 'test@example.com')->first();
    if (!$user) {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
        ]);
        echo "✅ Created test user: {$user->name}" . PHP_EOL;
    } else {
        echo "✅ Using existing user: {$user->name}" . PHP_EOL;
    }

    // Get service modules
    $visaService = ServiceModule::find(1);
    $translationService = ServiceModule::find(23);

    echo "✅ Using services: {$visaService->name}, {$translationService->name}" . PHP_EOL . PHP_EOL;

    // Create Tourist Visa Applications
    echo "📋 Creating Tourist Visa Applications..." . PHP_EOL;
    for ($i = 1; $i <= 3; $i++) {
        $app = ServiceApplication::create([
            'user_id' => $user->id,
            'service_module_id' => $visaService->id,
            'status' => 'pending',
            'application_data' => [
                'destination_country' => $i == 1 ? 'USA' : ($i == 2 ? 'UK' : 'Canada'),
                'travel_date' => now()->addDays(30 + $i)->format('Y-m-d'),
                'duration_days' => 30,
                'purpose' => 'tourism',
            ],
        ]);
        echo "  • Created application: {$app->application_number}" . PHP_EOL;
    }

    // Create Translation Applications
    echo PHP_EOL . "📋 Creating Translation Applications..." . PHP_EOL;
    for ($i = 1; $i <= 2; $i++) {
        $app = ServiceApplication::create([
            'user_id' => $user->id,
            'service_module_id' => $translationService->id,
            'status' => 'pending',
            'application_data' => [
                'source_language' => 'English',
                'target_language' => $i == 1 ? 'Bengali' : 'Arabic',
                'document_type' => $i == 1 ? 'legal' : 'educational',
                'page_count' => rand(5, 20),
                'urgency' => $i == 1 ? 'normal' : 'urgent',
            ],
        ]);
        echo "  • Created application: {$app->application_number}" . PHP_EOL;
    }

    // Create some agency country assignments (simulating agencies)
    echo PHP_EOL . "🏢 Creating Agency Assignments..." . PHP_EOL;
    
    // Create agency assignments for testing
    $usa = Country::where('name', 'United States')->first() ?? Country::where('code', 'US')->first();
    $uk = Country::where('name', 'United Kingdom')->first() ?? Country::where('code', 'GB')->first();
    
    if ($usa) {
        for ($agencyId = 1; $agencyId <= 2; $agencyId++) {
            try {
                AgencyCountryAssignment::firstOrCreate([
                    'agency_id' => $agencyId,
                    'country_id' => $usa->id,
                ]);
                echo "  • Agency #{$agencyId} → {$usa->name}" . PHP_EOL;
            } catch (\Exception $e) {
                echo "  ⚠️ Could not create assignment for Agency #{$agencyId}" . PHP_EOL;
            }
        }
    }

    // Create sample quotes from different agencies
    echo PHP_EOL . "💰 Creating Sample Quotes..." . PHP_EOL;
    
    $visaApps = ServiceApplication::where('service_module_id', 1)->where('status', 'pending')->get();
    $translationApps = ServiceApplication::where('service_module_id', 23)->where('status', 'pending')->get();

    // Quotes for tourist visas
    foreach ($visaApps->take(2) as $index => $app) {
        $agencyId = $index + 1;
        $quote = ServiceQuote::create([
            'service_application_id' => $app->id,
            'agency_id' => $agencyId,
            'quoted_amount' => rand(400, 600),
            'service_fee' => rand(50, 100),
            'processing_time_days' => rand(5, 15),
            'status' => 'pending',
            'valid_until' => now()->addDays(3),
            'special_notes' => 'Professional visa processing service',
        ]);
        
        // Calculate commissions
        $commission = round($quote->quoted_amount * ($visaService->platform_commission_rate / 100), 2);
        $earnings = $quote->quoted_amount - $commission;
        
        $quote->update([
            'platform_commission' => $commission,
            'agency_earnings' => $earnings,
        ]);
        
        echo "  • Visa Quote: \${$quote->quoted_amount} by Agency #{$agencyId} (Commission: \${$commission})" . PHP_EOL;
    }

    // Quotes for translations
    foreach ($translationApps as $index => $app) {
        $agencyId = $index + 1;
        $quote = ServiceQuote::create([
            'service_application_id' => $app->id,
            'agency_id' => $agencyId,
            'quoted_amount' => rand(100, 300),
            'service_fee' => rand(20, 50),
            'processing_time_days' => rand(2, 7),
            'status' => 'pending',
            'valid_until' => now()->addDays(5),
            'special_notes' => 'Certified translation service',
        ]);
        
        // Calculate commissions
        $commission = round($quote->quoted_amount * ($translationService->platform_commission_rate / 100), 2);
        $earnings = $quote->quoted_amount - $commission;
        
        $quote->update([
            'platform_commission' => $commission,
            'agency_earnings' => $earnings,
        ]);
        
        echo "  • Translation Quote: \${$quote->quoted_amount} by Agency #{$agencyId} (Commission: \${$commission})" . PHP_EOL;
    }

    // Accept one quote from each service type to show mixed portfolio
    echo PHP_EOL . "✅ Accepting Sample Quotes..." . PHP_EOL;
    
    $acceptedVisaQuote = ServiceQuote::where('service_application_id', $visaApps->first()->id)->first();
    if ($acceptedVisaQuote) {
        $acceptedVisaQuote->accept();
        echo "  • Accepted Tourist Visa quote: \${$acceptedVisaQuote->quoted_amount}" . PHP_EOL;
    }
    
    $acceptedTranslationQuote = ServiceQuote::where('service_application_id', $translationApps->first()->id)->first();
    if ($acceptedTranslationQuote) {
        $acceptedTranslationQuote->accept();
        echo "  • Accepted Translation quote: \${$acceptedTranslationQuote->quoted_amount}" . PHP_EOL;
    }

    DB::commit();
    
    echo PHP_EOL . "✅ TEST DATA CREATED SUCCESSFULLY!" . PHP_EOL;
    echo PHP_EOL . "Run: php test-multi-service.php to see the results" . PHP_EOL;

} catch (\Exception $e) {
    DB::rollBack();
    echo PHP_EOL . "❌ Error: " . $e->getMessage() . PHP_EOL;
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . PHP_EOL;
}
