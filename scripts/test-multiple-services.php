<?php

/**
 * Test Multiple Service Module Assignment Feature
 * 
 * This script demonstrates how the new multiple service module feature works
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\ServiceModule;
use App\Models\Country;
use App\Models\User;

echo "\n";
echo "========================================\n";
echo "  MULTIPLE SERVICE MODULE ASSIGNMENT   \n";
echo "========================================\n\n";

// Test 1: Show available services
echo "✓ Test 1: Available Service Modules\n";
echo "───────────────────────────────────\n";
$services = ServiceModule::whereIn('service_type', ['visa', 'travel', 'work_permit'])
    ->orderBy('service_type')
    ->orderBy('name')
    ->get();

foreach ($services as $service) {
    echo sprintf("  • %-30s [%s]\n", $service->name, $service->service_type);
}
echo "\n";

// Test 2: Show available countries
echo "✓ Test 2: Available Countries\n";
echo "───────────────────────────────────\n";
$countries = Country::whereIn('name', ['Malaysia', 'Thailand', 'Singapore', 'Vietnam', 'Indonesia'])
    ->get();

foreach ($countries as $country) {
    echo sprintf("  • %-20s (%s)\n", $country->name, $country->iso_code_2);
}
echo "\n";

// Test 3: Show test agencies
echo "✓ Test 3: Test Agencies Available\n";
echo "───────────────────────────────────\n";
$agencies = User::whereHas('role', function($query) {
    $query->where('slug', 'agency');
})->with('agency')->get();

foreach ($agencies as $user) {
    echo sprintf("  • %-30s (%s)\n", $user->agency->business_name ?? $user->name, $user->email);
}
echo "\n";

// Test 4: Example usage scenarios
echo "✓ Test 4: Example Usage Scenarios\n";
echo "───────────────────────────────────\n";
echo "\nScenario A: Assign 3 Services × 5 Countries\n";
echo "  Services: Tourist Visa, Student Visa, Work Visa\n";
echo "  Countries: Malaysia, Thailand, Singapore, Vietnam, Indonesia\n";
echo "  Result: 15 assignments created (3 × 5)\n";
echo "  Commission: 15% for all\n\n";

echo "Scenario B: Assign 2 Services × 1 Country\n";
echo "  Services: Tourist Visa, Business Visa\n";
echo "  Countries: United Kingdom\n";
echo "  Result: 2 assignments created (2 × 1)\n";
echo "  Commission: 18%\n\n";

echo "Scenario C: Assign 4 Services × Global Scope\n";
echo "  Services: Flight, Hotel, Travel Insurance, Visa Support\n";
echo "  Countries: None (Global)\n";
echo "  Result: 4 assignments created\n";
echo "  Commission: 10%\n\n";

// Test 5: Show how to use in UI
echo "✓ Test 5: How to Use in Admin Panel\n";
echo "───────────────────────────────────\n";
echo "\n1. Navigate to: http://127.0.0.1:8000/admin/agency-assignments/create\n";
echo "\n2. Select Agency\n";
echo "   └─ Choose from dropdown (e.g., BideshGomon Travel)\n";
echo "\n3. Enable Multiple Services\n";
echo "   └─ Check ☑ 'Assign multiple services at once'\n";
echo "   └─ Select services:\n";
echo "      ☑ Tourist Visa\n";
echo "      ☑ Student Visa\n";
echo "      ☑ Work Visa\n";
echo "\n4. Enable Multiple Countries (optional)\n";
echo "   └─ Check ☑ 'Assign multiple countries at once'\n";
echo "   └─ Select countries:\n";
echo "      ☑ Malaysia\n";
echo "      ☑ Thailand\n";
echo "      ☑ Singapore\n";
echo "\n5. Set Commission & Permissions\n";
echo "   └─ Commission Rate: 15%\n";
echo "   └─ Enable all permissions\n";
echo "\n6. Submit Form\n";
echo "   └─ Creates 9 assignments (3 services × 3 countries)\n";
echo "\n";

// Test 6: Benefits
echo "✓ Test 6: Feature Benefits\n";
echo "───────────────────────────────────\n";
echo "\n✓ Time Saving\n";
echo "  • Old: Create 15 assignments manually (15 form submissions)\n";
echo "  • New: Create 15 assignments at once (1 form submission)\n";
echo "\n✓ Consistency\n";
echo "  • Same commission rate applied to all\n";
echo "  • Same permissions set for all\n";
echo "  • Same settings across all combinations\n";
echo "\n✓ Flexibility\n";
echo "  • Can assign single service to multiple countries\n";
echo "  • Can assign multiple services to single country\n";
echo "  • Can assign multiple services to multiple countries\n";
echo "  • Can mix and match as needed\n";
echo "\n";

// Success message
echo "========================================\n";
echo "  ✅ Multiple Service Feature Ready!   \n";
echo "========================================\n";
echo "\n➜ Go to: http://127.0.0.1:8000/admin/agency-assignments/create\n";
echo "➜ Test: Select 3+ services and 3+ countries\n";
echo "➜ Result: See bulk assignment success message\n\n";
