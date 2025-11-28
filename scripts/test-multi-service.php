<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ServiceApplication;
use App\Models\ServiceQuote;
use App\Models\ServiceModule;
use App\Models\TouristVisa;
use App\Models\TranslationRequest;
use App\Models\User;
use App\Models\AgencyCountryAssignment;

echo "=== 21-SERVICE INTEGRATION TEST ===" . PHP_EOL . PHP_EOL;

// Test 1: Check available service modules
echo "📦 Available Service Modules:" . PHP_EOL;
$services = ServiceModule::whereIn('id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 20, 22, 23])->get();
foreach ($services as $service) {
    echo "  • {$service->name} (ID: {$service->id}, Commission: {$service->platform_commission_rate}%)" . PHP_EOL;
}
echo PHP_EOL;

// Test 3: Check service applications by type
echo "📋 Service Applications:" . PHP_EOL;
$applications = ServiceApplication::with(['serviceModule', 'user'])
    ->whereNull('agency_id')
    ->where('status', 'pending')
    ->get()
    ->groupBy('service_module_id');

$visaCount = $applications->get(1)?->count() ?? 0;
$studentVisaCount = $applications->get(2)?->count() ?? 0;
$workVisaCount = $applications->get(3)?->count() ?? 0;
$businessVisaCount = $applications->get(4)?->count() ?? 0;
$medicalVisaCount = $applications->get(5)?->count() ?? 0;
$familyVisaCount = $applications->get(6)?->count() ?? 0;
$transitVisaCount = $applications->get(7)?->count() ?? 0;
$flightCount = $applications->get(8)?->count() ?? 0;
$hotelCount = $applications->get(9)?->count() ?? 0;
$insuranceCount = $applications->get(10)?->count() ?? 0;
$workPermitCount = $applications->get(22)?->count() ?? 0;
$translationCount = $applications->get(23)?->count() ?? 0;
$airportTransferCount = $applications->get(11)?->count() ?? 0;
$carRentalCount = $applications->get(12)?->count() ?? 0;
$tourPackagesCount = $applications->get(13)?->count() ?? 0;
$universityCount = $applications->get(14)?->count() ?? 0;
$counselingCount = $applications->get(15)?->count() ?? 0;
$languageTestCount = $applications->get(16)?->count() ?? 0;
$scholarshipCount = $applications->get(17)?->count() ?? 0;
$jobSearchCount = $applications->get(18)?->count() ?? 0;
$interviewPrepCount = $applications->get(20)?->count() ?? 0;

echo "  • Tourist Visa: {$visaCount} pending applications" . PHP_EOL;
echo "  • Student Visa: {$studentVisaCount} pending applications" . PHP_EOL;
echo "  • Work Visa: {$workVisaCount} pending applications" . PHP_EOL;
echo "  • Business Visa: {$businessVisaCount} pending applications" . PHP_EOL;
echo "  • Medical Visa: {$medicalVisaCount} pending applications" . PHP_EOL;
echo "  • Family Visa: {$familyVisaCount} pending applications" . PHP_EOL;
echo "  • Transit Visa: {$transitVisaCount} pending applications" . PHP_EOL;
echo "  • Flight Booking: {$flightCount} pending applications" . PHP_EOL;
echo "  • Hotel Booking: {$hotelCount} pending applications" . PHP_EOL;
echo "  • Travel Insurance: {$insuranceCount} pending applications" . PHP_EOL;
echo "  • Work Permit: {$workPermitCount} pending applications" . PHP_EOL;
echo "  • Translation: {$translationCount} pending applications" . PHP_EOL;
echo "  • Airport Transfer: {$airportTransferCount} pending applications" . PHP_EOL;
echo "  • Car Rental: {$carRentalCount} pending applications" . PHP_EOL;
echo "  • Tour Packages: {$tourPackagesCount} pending applications" . PHP_EOL;
echo "  • University Application: {$universityCount} pending applications" . PHP_EOL;
echo "  • Course Counseling: {$counselingCount} pending applications" . PHP_EOL;
echo "  • Language Test Prep: {$languageTestCount} pending applications" . PHP_EOL;
echo "  • Scholarship Assistance: {$scholarshipCount} pending applications" . PHP_EOL;
echo "  • Job Search: {$jobSearchCount} pending applications" . PHP_EOL;
echo "  • Interview Prep: {$interviewPrepCount} pending applications" . PHP_EOL;
echo PHP_EOL;

// Test 4: Sample application data
if ($visaCount > 0) {
    $visaApp = $applications->get(1)->first();
    echo "📄 Sample Tourist Visa Application:" . PHP_EOL;
    echo "  Application #: {$visaApp->application_number}" . PHP_EOL;
    echo "  Service: {$visaApp->serviceModule->name}" . PHP_EOL;
    echo "  Commission Rate: {$visaApp->serviceModule->platform_commission_rate}%" . PHP_EOL;
    echo "  Status: {$visaApp->status}" . PHP_EOL;
    echo "  User: {$visaApp->user->name}" . PHP_EOL;
    echo PHP_EOL;
}

if ($translationCount > 0) {
    $translationApp = $applications->get(23)->first();
    echo "📄 Sample Translation Application:" . PHP_EOL;
    echo "  Application #: {$translationApp->application_number}" . PHP_EOL;
    echo "  Service: {$translationApp->serviceModule->name}" . PHP_EOL;
    echo "  Commission Rate: {$translationApp->serviceModule->platform_commission_rate}%" . PHP_EOL;
    echo "  Status: {$translationApp->status}" . PHP_EOL;
    echo "  User: {$translationApp->user->name}" . PHP_EOL;
    
    if (!empty($translationApp->application_data)) {
        echo "  Data: " . PHP_EOL;
        $data = is_string($translationApp->application_data) 
            ? json_decode($translationApp->application_data, true) 
            : $translationApp->application_data;
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                echo "    - {$key}: {$value}" . PHP_EOL;
            }
        }
    }
    echo PHP_EOL;
}

// Test 5: Check existing quotes
echo "💰 Existing Quotes:" . PHP_EOL;
$quotes = ServiceQuote::with(['serviceApplication.serviceModule'])
    ->whereHas('serviceApplication', function($q) {
        $q->whereIn('service_module_id', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 20, 22, 23]);
    })
    ->get()
    ->groupBy('serviceApplication.service_module_id');

$visaQuotes = $quotes->get(1)?->count() ?? 0;
$studentVisaQuotes = $quotes->get(2)?->count() ?? 0;
$workVisaQuotes = $quotes->get(3)?->count() ?? 0;
$businessVisaQuotes = $quotes->get(4)?->count() ?? 0;
$medicalVisaQuotes = $quotes->get(5)?->count() ?? 0;
$familyVisaQuotes = $quotes->get(6)?->count() ?? 0;
$transitVisaQuotes = $quotes->get(7)?->count() ?? 0;
$flightQuotes = $quotes->get(8)?->count() ?? 0;
$hotelQuotes = $quotes->get(9)?->count() ?? 0;
$insuranceQuotes = $quotes->get(10)?->count() ?? 0;
$workPermitQuotes = $quotes->get(22)?->count() ?? 0;
$translationQuotes = $quotes->get(23)?->count() ?? 0;
$airportTransferQuotes = $quotes->get(11)?->count() ?? 0;
$carRentalQuotes = $quotes->get(12)?->count() ?? 0;
$tourPackagesQuotes = $quotes->get(13)?->count() ?? 0;
$universityQuotes = $quotes->get(14)?->count() ?? 0;
$counselingQuotes = $quotes->get(15)?->count() ?? 0;
$languageTestQuotes = $quotes->get(16)?->count() ?? 0;
$scholarshipQuotes = $quotes->get(17)?->count() ?? 0;
$jobSearchQuotes = $quotes->get(18)?->count() ?? 0;
$interviewPrepQuotes = $quotes->get(20)?->count() ?? 0;

echo "  • Tourist Visa Quotes: {$visaQuotes}" . PHP_EOL;
echo "  • Student Visa Quotes: {$studentVisaQuotes}" . PHP_EOL;
echo "  • Work Visa Quotes: {$workVisaQuotes}" . PHP_EOL;
echo "  • Business Visa Quotes: {$businessVisaQuotes}" . PHP_EOL;
echo "  • Medical Visa Quotes: {$medicalVisaQuotes}" . PHP_EOL;
echo "  • Family Visa Quotes: {$familyVisaQuotes}" . PHP_EOL;
echo "  • Transit Visa Quotes: {$transitVisaQuotes}" . PHP_EOL;
echo "  • Flight Booking Quotes: {$flightQuotes}" . PHP_EOL;
echo "  • Hotel Booking Quotes: {$hotelQuotes}" . PHP_EOL;
echo "  • Travel Insurance Quotes: {$insuranceQuotes}" . PHP_EOL;
echo "  • Work Permit Quotes: {$workPermitQuotes}" . PHP_EOL;
echo "  • Translation Quotes: {$translationQuotes}" . PHP_EOL;
echo "  • Airport Transfer Quotes: {$airportTransferQuotes}" . PHP_EOL;
echo "  • Car Rental Quotes: {$carRentalQuotes}" . PHP_EOL;
echo "  • Tour Packages Quotes: {$tourPackagesQuotes}" . PHP_EOL;
echo "  • University Application Quotes: {$universityQuotes}" . PHP_EOL;
echo "  • Course Counseling Quotes: {$counselingQuotes}" . PHP_EOL;
echo "  • Language Test Prep Quotes: {$languageTestQuotes}" . PHP_EOL;
echo "  • Scholarship Assistance Quotes: {$scholarshipQuotes}" . PHP_EOL;
echo "  • Job Search Quotes: {$jobSearchQuotes}" . PHP_EOL;
echo "  • Interview Prep Quotes: {$interviewPrepQuotes}" . PHP_EOL;

if ($visaQuotes > 0) {
    $sample = $quotes->get(1)->first();
    echo "    Sample: \${$sample->quoted_amount} by Agency #{$sample->agency_id} ({$sample->status})" . PHP_EOL;
}

if ($translationQuotes > 0) {
    $sample = $quotes->get(23)->first();
    echo "    Sample: \${$sample->quoted_amount} by Agency #{$sample->agency_id} ({$sample->status})" . PHP_EOL;
}
echo PHP_EOL;

// Test 6: Check agency assignments
echo "🎯 Agency Country Assignments:" . PHP_EOL;
$assignments = AgencyCountryAssignment::with('country')->limit(5)->get();
if ($assignments->count() > 0) {
    echo "  Sample Assignments:" . PHP_EOL;
    foreach ($assignments as $assignment) {
        echo "    • Agency ID {$assignment->agency_id} → {$assignment->country->name}" . PHP_EOL;
    }
    
    // Check if agencies can quote on both service types
    $firstAgencyId = $assignments->first()->agency_id;
    $visaApps = ServiceApplication::where('service_module_id', 1)->whereNull('agency_id')->count();
    $translationApps = ServiceApplication::where('service_module_id', 23)->whereNull('agency_id')->count();
    
    echo PHP_EOL;
    echo "  Agency #{$firstAgencyId} can potentially quote on:" . PHP_EOL;
    echo "    • {$visaApps} Tourist Visa applications" . PHP_EOL;
    echo "    • {$translationApps} Translation applications" . PHP_EOL;
} else {
    echo "  ⚠️ No agency assignments found" . PHP_EOL;
}
echo PHP_EOL;

// Test 7: Multi-service statistics
echo "📊 Multi-Service Statistics:" . PHP_EOL;
$totalApps = ServiceApplication::count();
$pendingApps = ServiceApplication::where('status', 'pending')->count();
$assignedApps = ServiceApplication::whereNotNull('agency_id')->count();
$totalQuotes = ServiceQuote::count();
$acceptedQuotes = ServiceQuote::where('status', 'accepted')->count();

echo "  • Total Applications: {$totalApps}" . PHP_EOL;
echo "  • Pending Applications: {$pendingApps}" . PHP_EOL;
echo "  • Assigned Applications: {$assignedApps}" . PHP_EOL;
echo "  • Total Quotes: {$totalQuotes}" . PHP_EOL;
echo "  • Accepted Quotes: {$acceptedQuotes}" . PHP_EOL;
echo PHP_EOL;

// Test 8: Platform revenue projection
if ($acceptedQuotes > 0) {
    echo "💵 Platform Revenue:" . PHP_EOL;
    $acceptedQuotesList = ServiceQuote::where('status', 'accepted')
        ->with('serviceApplication.serviceModule')
        ->get();
    
    $totalRevenue = 0;
    $totalAgencyEarnings = 0;
    
    foreach ($acceptedQuotesList as $quote) {
        $totalRevenue += $quote->platform_commission ?? 0;
        $totalAgencyEarnings += $quote->agency_earnings ?? 0;
    }
    
    echo "  • Platform Commission: \${$totalRevenue}" . PHP_EOL;
    echo "  • Agency Earnings: \${$totalAgencyEarnings}" . PHP_EOL;
    echo "  • Total Transaction Value: \$" . ($totalRevenue + $totalAgencyEarnings) . PHP_EOL;
    echo PHP_EOL;
}

echo "✅ Test Complete!" . PHP_EOL;
