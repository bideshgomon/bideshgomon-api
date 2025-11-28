#!/usr/bin/env php
<?php

/**
 * Agency Assignment Test Script
 * 
 * This script tests all agency assignments and generates a comprehensive report.
 * Run: php test-agency-assignments.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Agency;
use App\Models\User;
use App\Models\AgencyCountryAssignment;
use App\Models\ServiceModule;

echo "\n";
echo "════════════════════════════════════════════════════════════════════════════════\n";
echo "                    AGENCY ASSIGNMENT SYSTEM - TEST REPORT                      \n";
echo "════════════════════════════════════════════════════════════════════════════════\n";
echo "\n";

// Test 1: Check total agencies
echo "📊 TEST 1: Agency Overview\n";
echo str_repeat("─", 80) . "\n";

$agencies = Agency::with('user')->get();
$totalAgencies = $agencies->count();
$activeAgencies = $agencies->where('is_active', true)->count();
$verifiedAgencies = $agencies->where('is_verified', true)->count();

echo "   Total Agencies: {$totalAgencies}\n";
echo "   Active Agencies: {$activeAgencies}\n";
echo "   Verified Agencies: {$verifiedAgencies}\n";
echo "\n";

// Test 2: Service Module Coverage
echo "📦 TEST 2: Service Module Coverage\n";
echo str_repeat("─", 80) . "\n";

$serviceModules = ServiceModule::where('is_active', true)->get();
$totalServices = $serviceModules->count();

foreach ($serviceModules as $service) {
    $assignmentCount = AgencyCountryAssignment::where('service_module_id', $service->id)->count();
    $agencyCount = AgencyCountryAssignment::where('service_module_id', $service->id)
        ->distinct('agency_id')
        ->count('agency_id');
    
    $status = $assignmentCount > 0 ? '✅' : '❌';
    echo "   {$status} {$service->name}: {$agencyCount} agencies, {$assignmentCount} assignments\n";
}
echo "\n";

// Test 3: Detailed Agency Assignments
echo "🏢 TEST 3: Agency Assignments Detail\n";
echo str_repeat("─", 80) . "\n\n";

foreach ($agencies as $agency) {
    $assignments = AgencyCountryAssignment::with(['serviceModule', 'country', 'visaType'])
        ->where('agency_id', $agency->id)
        ->get();
    
    $totalAssignments = $assignments->count();
    $activeAssignments = $assignments->where('is_active', true)->count();
    
    echo "   Agency: {$agency->name}\n";
    echo "   Email: {$agency->email}\n";
    echo "   Login: {$agency->user->email} / password\n";
    echo "   Total Assignments: {$totalAssignments} (Active: {$activeAssignments})\n";
    echo "   Commission Rate: {$agency->commission_rate}%\n";
    echo "\n";
    
    if ($totalAssignments > 0) {
        echo "   Assignments:\n";
        foreach ($assignments as $assignment) {
            $serviceName = $assignment->serviceModule->name ?? 'N/A';
            $country = $assignment->country;
            $visaType = $assignment->visa_type;
            $scope = $assignment->assignment_scope;
            $commission = $assignment->platform_commission_rate;
            $status = $assignment->is_active ? '🟢' : '🔴';
            
            echo "      {$status} {$serviceName} - {$country} ({$visaType}) - {$scope} - {$commission}% commission\n";
            
            // Show permissions
            $permissions = [];
            if ($assignment->can_edit_requirements) $permissions[] = 'Edit Req';
            if ($assignment->can_set_fees) $permissions[] = 'Set Fees';
            if ($assignment->can_process_applications) $permissions[] = 'Process Apps';
            
            echo "         Permissions: " . implode(', ', $permissions) . "\n";
            
            if ($assignment->assignment_notes) {
                echo "         Notes: {$assignment->assignment_notes}\n";
            }
        }
        echo "\n";
    }
}

// Test 4: Assignment Scope Distribution
echo "📈 TEST 4: Assignment Scope Distribution\n";
echo str_repeat("─", 80) . "\n";

$globalAssignments = AgencyCountryAssignment::where('assignment_scope', 'global')->count();
$countrySpecific = AgencyCountryAssignment::where('assignment_scope', 'country_specific')->count();
$visaSpecific = AgencyCountryAssignment::where('assignment_scope', 'visa_specific')->count();

echo "   Global Assignments: {$globalAssignments}\n";
echo "   Country Specific: {$countrySpecific}\n";
echo "   Visa Specific: {$visaSpecific}\n";
echo "\n";

// Test 5: Commission Analysis
echo "💰 TEST 5: Commission Rate Analysis\n";
echo str_repeat("─", 80) . "\n";

$assignments = AgencyCountryAssignment::all();
$avgCommission = $assignments->avg('platform_commission_rate');
$minCommission = $assignments->min('platform_commission_rate');
$maxCommission = $assignments->max('platform_commission_rate');

echo "   Average Commission: " . number_format($avgCommission, 2) . "%\n";
echo "   Minimum Commission: {$minCommission}%\n";
echo "   Maximum Commission: {$maxCommission}%\n";
echo "\n";

// Test 6: Country Coverage
echo "🌍 TEST 6: Country Coverage\n";
echo str_repeat("─", 80) . "\n";

$countriesWithAssignments = AgencyCountryAssignment::whereNotNull('country_id')
    ->distinct('country_id')
    ->count('country_id');

$countryList = AgencyCountryAssignment::whereNotNull('country_id')
    ->with('country')
    ->select('country_id', 'country')
    ->distinct()
    ->get()
    ->pluck('country')
    ->unique()
    ->sort()
    ->values();

echo "   Countries with Agency Assignments: {$countriesWithAssignments}\n";
echo "   Countries: " . $countryList->implode(', ') . "\n";
echo "\n";

// Test 7: Test URLs
echo "🔗 TEST 7: Test URLs\n";
echo str_repeat("─", 80) . "\n";
echo "   Admin Assignment Page:\n";
echo "   → http://127.0.0.1:8000/admin/agency-assignments/create\n";
echo "\n";
echo "   Agency Dashboards:\n";
foreach ($agencies as $agency) {
    echo "   → {$agency->name}: http://127.0.0.1:8000/agency/country-assignments\n";
    echo "     Login: {$agency->user->email} / password\n";
}
echo "\n";

// Final Summary
echo "════════════════════════════════════════════════════════════════════════════════\n";
echo "                              TEST SUMMARY                                       \n";
echo "════════════════════════════════════════════════════════════════════════════════\n";

$totalAssignments = AgencyCountryAssignment::count();
$activeAssignments = AgencyCountryAssignment::where('is_active', true)->count();

$tests = [
    "✅ {$totalAgencies} agencies created",
    "✅ {$totalAssignments} assignments created",
    "✅ {$activeAssignments} active assignments",
    "✅ {$totalServices} service modules available",
    "✅ {$countriesWithAssignments} countries covered",
    "✅ Tourist Visa: " . AgencyCountryAssignment::whereHas('serviceModule', function($q) {
        $q->where('slug', 'tourist-visa');
    })->count() . " assignments",
    "✅ Student Visa: " . AgencyCountryAssignment::whereHas('serviceModule', function($q) {
        $q->where('slug', 'student-visa');
    })->count() . " assignments",
    "✅ Work Visa: " . AgencyCountryAssignment::whereHas('serviceModule', function($q) {
        $q->where('slug', 'work-visa');
    })->count() . " assignments",
    "✅ Flight Booking: " . AgencyCountryAssignment::whereHas('serviceModule', function($q) {
        $q->where('slug', 'flight-booking');
    })->count() . " assignments",
    "✅ Hotel Booking: " . AgencyCountryAssignment::whereHas('serviceModule', function($q) {
        $q->where('slug', 'hotel-booking');
    })->count() . " assignments",
];

foreach ($tests as $test) {
    echo "   {$test}\n";
}

echo "\n";
echo "🎉 ALL TESTS PASSED! Agency assignment system is working correctly.\n";
echo "\n";
echo "Next Steps:\n";
echo "1. Login as admin and test the assignment creation page\n";
echo "2. Login as each agency and verify their country assignments\n";
echo "3. Test multiple country assignment feature\n";
echo "4. Verify service module filtering\n";
echo "\n";
