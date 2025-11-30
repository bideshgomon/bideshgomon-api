<?php

/**
 * DEEP RESEARCH: Data Management System Discovery
 * Analyze all enum fields, dropdowns, and standardizable data across the platform
 */

$db = new mysqli('localhost', 'root', '', 'bideshgomondb');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "═══════════════════════════════════════════════════════════════════\n";
echo "   DEEP DATA MANAGEMENT SYSTEM DISCOVERY\n";
echo "   Complete Analysis of Standardizable Fields\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

// CATEGORY 1: EXISTING IN DATABASE (From grep search results)
$existingTables = [
    'countries' => ['Current: 10', 'Should be: 195+', 'Priority: CRITICAL'],
    'currencies' => ['Current: 10', 'Should be: 150+', 'Priority: HIGH'],
    'languages' => ['Current: 8', 'Should be: 100+', 'Priority: HIGH'],
    'language_tests' => ['Current: 8', 'Should be: 15+', 'Priority: MEDIUM'],
    'degrees' => ['Current: 8', 'Should be: 50+', 'Priority: HIGH'],
    'cities' => ['Current: 8', 'Should be: 1000+', 'Priority: HIGH'],
    'skills' => ['Current: 0', 'Should be: 500+', 'Priority: CRITICAL'],
    'document_types' => ['Current: 10', 'Should be: 30+', 'Priority: MEDIUM'],
    'institution_types' => ['Current: 6', 'Should be: 20+', 'Priority: LOW'],
];

// CATEGORY 2: MISSING TABLES (Found in migrations as ENUM)
$missingTables = [
    // Personal Information
    'genders' => [
        'values' => ['Male', 'Female', 'Other', 'Prefer not to say'],
        'used_in' => ['user_profiles', 'user_family_members', 'user_passports', 'job_postings'],
        'priority' => 'MEDIUM'
    ],
    'marital_statuses' => [
        'values' => ['Single', 'Married', 'Divorced', 'Widowed', 'Separated'],
        'used_in' => ['user_profiles', 'user_family_members'],
        'priority' => 'HIGH'
    ],
    'blood_groups' => [
        'values' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
        'used_in' => ['user_profiles'],
        'priority' => 'MEDIUM'
    ],
    
    // Relationship & Family
    'relationship_types' => [
        'values' => ['Spouse', 'Father', 'Mother', 'Son', 'Daughter', 'Brother', 'Sister', 'Grandfather', 'Grandmother', 'Uncle', 'Aunt', 'Cousin', 'Guardian', 'Other'],
        'used_in' => ['user_family_members', 'user_financial_information.sponsor_relationship'],
        'priority' => 'HIGH'
    ],
    'immigration_statuses' => [
        'values' => ['Citizen', 'Permanent Resident', 'Work Permit', 'Student Visa', 'Temporary Visitor', 'Refugee', 'Asylum Seeker', 'Undocumented'],
        'used_in' => ['user_family_members'],
        'priority' => 'MEDIUM'
    ],
    
    // Education
    'education_levels' => [
        'values' => ['No Formal Education', 'Primary', 'Secondary', 'Higher Secondary', 'Diploma', 'Bachelor', 'Master', 'Doctorate', 'Post-Doctorate'],
        'used_in' => ['user_family_members.education_level', 'degrees.level'],
        'priority' => 'HIGH'
    ],
    'institution_types' => [
        'values' => ['University', 'College', 'School', 'Technical Institute', 'Vocational School', 'Language Center', 'Training Institute', 'Online Institution'],
        'used_in' => ['user_educations', 'agency_resources'],
        'priority' => 'MEDIUM'
    ],
    
    // Employment
    'employment_types' => [
        'values' => ['Full-time', 'Part-time', 'Contract', 'Freelance', 'Internship', 'Temporary', 'Self-employed'],
        'used_in' => ['user_work_experiences', 'job_postings'],
        'priority' => 'HIGH'
    ],
    'job_categories' => [
        'values' => ['IT & Technology', 'Healthcare', 'Education', 'Construction', 'Hospitality', 'Finance & Banking', 'Engineering', 'Sales & Marketing', 'Manufacturing', 'Agriculture', 'Transportation', 'Retail', 'Other'],
        'used_in' => ['job_postings', 'user_work_experiences'],
        'priority' => 'HIGH'
    ],
    'professions' => [
        'values' => ['Doctor', 'Engineer', 'Teacher', 'Nurse', 'Accountant', 'Software Developer', 'Chef', 'Driver', 'Electrician', 'Plumber', 'Carpenter', 'Mechanic', 'Sales Person', 'Manager', 'Student', 'Retired', 'Unemployed', 'Other'],
        'used_in' => ['service_applications', 'visa_requirements', 'tourist_visas'],
        'priority' => 'CRITICAL'
    ],
    'experience_levels' => [
        'values' => ['Entry Level', 'Junior (1-2 years)', 'Mid-level (3-5 years)', 'Senior (5-10 years)', 'Expert (10+ years)'],
        'used_in' => ['job_postings'],
        'priority' => 'MEDIUM'
    ],
    'salary_periods' => [
        'values' => ['Hourly', 'Daily', 'Weekly', 'Monthly', 'Yearly'],
        'used_in' => ['job_postings', 'user_work_experiences'],
        'priority' => 'LOW'
    ],
    
    // Visa & Travel
    'visa_types' => [
        'values' => ['Tourist', 'Business', 'Student', 'Work', 'Family/Spouse', 'Transit', 'Medical', 'Diplomatic', 'Refugee', 'Permanent Residence', 'Other'],
        'used_in' => ['user_visa_history', 'visa_applications', 'tourist_visas', 'student_visas', 'work_visas'],
        'priority' => 'CRITICAL'
    ],
    'visa_categories' => [
        'values' => ['Single Entry', 'Multiple Entry', 'On Arrival', 'E-Visa', 'Visa Free'],
        'used_in' => ['user_visa_history'],
        'priority' => 'HIGH'
    ],
    'travel_purposes' => [
        'values' => ['Tourism', 'Business', 'Education', 'Employment', 'Family Visit', 'Medical Treatment', 'Conference', 'Research', 'Transit', 'Other'],
        'used_in' => ['user_travel_history', 'flight_requests'],
        'priority' => 'HIGH'
    ],
    'accommodation_types' => [
        'values' => ['Hotel', 'Hostel', 'Apartment', 'Guest House', 'Home Stay', 'Friends/Family', 'Airbnb', 'Resort', 'Camping', 'Other'],
        'used_in' => ['user_travel_history', 'hajj_umrahs'],
        'priority' => 'MEDIUM'
    ],
    'passport_types' => [
        'values' => ['Regular', 'Diplomatic', 'Official', 'Service', 'Emergency', 'Temporary'],
        'used_in' => ['user_passports'],
        'priority' => 'LOW'
    ],
    
    // Financial
    'income_sources' => [
        'values' => ['Employment', 'Business', 'Investment', 'Rental Income', 'Pension', 'Family Support', 'Government Benefits', 'Savings', 'Other'],
        'used_in' => ['user_financial_information'],
        'priority' => 'HIGH'
    ],
    'property_ownership_types' => [
        'values' => ['Owned Outright', 'Mortgaged', 'Rented', 'Family Owned', 'Leased', 'Government Housing', 'No Property'],
        'used_in' => ['user_financial_information'],
        'priority' => 'MEDIUM'
    ],
    'bank_names' => [
        'values' => ['Bangladesh: Sonali Bank, Janata Bank, Agrani Bank, Rupali Bank, BASIC Bank, Bangladesh Development Bank, Dutch-Bangla Bank, BRAC Bank, City Bank, Eastern Bank, IFIC Bank, Mutual Trust Bank, Prime Bank, Standard Bank, United Commercial Bank, etc.'],
        'used_in' => ['user_profiles', 'user_financial_information'],
        'priority' => 'HIGH'
    ],
    
    // Documents
    'document_types' => [
        'values' => ['Passport', 'NID/National ID', 'Birth Certificate', 'Marriage Certificate', 'Divorce Certificate', 'Death Certificate', 'Educational Certificate', 'Degree', 'Transcript', 'Experience Letter', 'Payslip', 'Bank Statement', 'Tax Return', 'Property Document', 'Medical Report', 'Police Clearance', 'Recommendation Letter', 'Invitation Letter', 'Sponsorship Letter', 'Insurance Document', 'Flight Ticket', 'Hotel Booking', 'Other'],
        'used_in' => ['user_documents', 'visa_documents', 'translation_requests', 'attestations'],
        'priority' => 'HIGH'
    ],
    'certification_types' => [
        'values' => ['Standard', 'Certified Translation', 'Notarized', 'Apostille', 'MOFA Attestation', 'Embassy Attestation', 'HRD Attestation', 'MEA Attestation', 'Chamber of Commerce'],
        'used_in' => ['translation_requests', 'attestations'],
        'priority' => 'MEDIUM'
    ],
    
    // Language
    'proficiency_levels' => [
        'values' => ['Beginner (A1)', 'Elementary (A2)', 'Intermediate (B1)', 'Upper Intermediate (B2)', 'Advanced (C1)', 'Proficient (C2)', 'Native'],
        'used_in' => ['user_languages', 'user_skill'],
        'priority' => 'MEDIUM'
    ],
    
    // Service Management
    'processing_types' => [
        'values' => ['Standard (15-30 days)', 'Express (7-14 days)', 'Urgent (1-5 days)'],
        'used_in' => ['visa_applications', 'translation_requests', 'job_applications'],
        'priority' => 'MEDIUM'
    ],
    'priority_levels' => [
        'values' => ['Normal', 'High', 'Urgent', 'Critical'],
        'used_in' => ['translation_requests', 'support_tickets'],
        'priority' => 'LOW'
    ],
    'appointment_types' => [
        'values' => ['Consultation', 'Document Review', 'Interview Preparation', 'Biometrics', 'Document Submission', 'Medical Exam', 'Office Visit'],
        'used_in' => ['appointments', 'visa_appointments'],
        'priority' => 'MEDIUM'
    ],
    
    // Bangladesh Specific
    'bd_divisions' => [
        'values' => ['Dhaka', 'Chittagong', 'Rajshahi', 'Khulna', 'Barisal', 'Sylhet', 'Rangpur', 'Mymensingh'],
        'used_in' => ['user_profiles.present_division', 'user_profiles.permanent_division'],
        'priority' => 'CRITICAL'
    ],
    'bd_districts' => [
        'values' => ['64 Districts - Dhaka, Gazipur, Narayanganj, Chittagong, Cox\'s Bazar, Sylhet, Rajshahi, etc.'],
        'used_in' => ['user_profiles.present_district', 'user_profiles.permanent_district'],
        'priority' => 'HIGH'
    ],
    
    // Status Fields (Standardized)
    'application_statuses' => [
        'values' => ['Draft', 'Pending', 'Submitted', 'Under Review', 'Documents Requested', 'Interview Scheduled', 'Processing', 'Approved', 'Rejected', 'Cancelled', 'Completed', 'Expired'],
        'used_in' => ['visa_applications', 'job_applications', 'service_applications', 'translation_requests'],
        'priority' => 'HIGH'
    ],
    'payment_methods' => [
        'values' => ['Wallet', 'bKash', 'Nagad', 'Rocket', 'Upay', 'Credit/Debit Card', 'Bank Transfer', 'Cash', 'PayPal', 'Stripe'],
        'used_in' => ['payment_transactions', 'service_bookings', 'wallet_transactions'],
        'priority' => 'HIGH'
    ],
    
    // Flight & Hotel
    'flight_classes' => [
        'values' => ['Economy', 'Premium Economy', 'Business', 'First Class'],
        'used_in' => ['flight_requests', 'flight_searches'],
        'priority' => 'MEDIUM'
    ],
    'trip_types' => [
        'values' => ['One Way', 'Round Trip', 'Multi-City'],
        'used_in' => ['flight_requests', 'flight_bookings'],
        'priority' => 'MEDIUM'
    ],
    'time_preferences' => [
        'values' => ['Morning (6AM-12PM)', 'Afternoon (12PM-6PM)', 'Evening (6PM-10PM)', 'Night (10PM-6AM)', 'Flexible'],
        'used_in' => ['flight_requests'],
        'priority' => 'LOW'
    ],
    
    // Hajj & Umrah
    'hajj_package_types' => [
        'values' => ['Hajj Package', 'Umrah Package', 'Hajj + Umrah Combined'],
        'used_in' => ['hajj_umrahs'],
        'priority' => 'MEDIUM'
    ],
    'meal_plans' => [
        'values' => ['Without Meals', 'Breakfast Only', 'Half Board (Breakfast + Dinner)', 'Full Board (All Meals)'],
        'used_in' => ['hajj_umrahs', 'hotel_bookings'],
        'priority' => 'LOW'
    ],
    
    // CV/Resume
    'cv_template_categories' => [
        'values' => ['Professional', 'Modern', 'Creative', 'ATS-Friendly', 'Executive', 'Academic', 'Technical'],
        'used_in' => ['cv_templates'],
        'priority' => 'LOW'
    ],
];

echo "📊 SUMMARY OF FINDINGS:\n";
echo "───────────────────────────────────────────────────────────────────\n\n";

echo "EXISTING TABLES (Underpopulated):\n";
foreach ($existingTables as $table => $info) {
    printf("  %-25s  %s  →  %s  [%s]\n", 
        strtoupper($table), 
        $info[0], 
        $info[1], 
        $info[2]
    );
}

echo "\n\nMISSING TABLES (Need Creation):\n";
$criticalCount = 0;
$highCount = 0;
$mediumCount = 0;
$lowCount = 0;

foreach ($missingTables as $table => $data) {
    $priority = $data['priority'];
    $valueCount = is_array($data['values']) ? count($data['values']) : substr_count($data['values'], ',') + 1;
    $usedInCount = is_array($data['used_in']) ? count($data['used_in']) : 1;
    
    printf("  %-30s  %2d values  →  %2d tables  [%s]\n",
        strtoupper($table),
        $valueCount,
        $usedInCount,
        $priority
    );
    
    if ($priority == 'CRITICAL') $criticalCount++;
    elseif ($priority == 'HIGH') $highCount++;
    elseif ($priority == 'MEDIUM') $mediumCount++;
    else $lowCount++;
}

echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "   STATISTICS\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

printf("Existing Tables (Needs Data)    : %2d tables\n", count($existingTables));
printf("Missing Tables (Needs Creation) : %2d tables\n\n", count($missingTables));

printf("By Priority:\n");
printf("  CRITICAL  : %2d tables (Must have immediately)\n", $criticalCount);
printf("  HIGH      : %2d tables (Important for quality)\n", $highCount);
printf("  MEDIUM    : %2d tables (Nice to have)\n", $mediumCount);
printf("  LOW       : %2d tables (Optional)\n\n", $lowCount);

$totalTables = count($existingTables) + count($missingTables);
printf("TOTAL DATA MANAGEMENT TABLES    : %2d tables\n", $totalTables);

// Calculate impact on profile fields
$totalImpactedFields = 34; // From previous analysis
$additionalFields = 0;

foreach ($missingTables as $table => $data) {
    if (isset($data['used_in'])) {
        $additionalFields += is_array($data['used_in']) ? count($data['used_in']) : 1;
    }
}

$totalAutofillableFields = $totalImpactedFields + $additionalFields;
$percentage = ($totalAutofillableFields / 327) * 100;

echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "   IMPACT ON USER PROFILE FIELDS\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

printf("Currently Auto-fillable         : %3d fields ( %.1f%%)\n", $totalImpactedFields, ($totalImpactedFields/327)*100);
printf("With All Tables Implemented     : %3d fields ( %.1f%%)\n", $totalAutofillableFields, $percentage);
printf("Additional Coverage             : %3d fields ( %.1f%%)\n\n", $additionalFields, ($additionalFields/327)*100);

echo "═══════════════════════════════════════════════════════════════════\n";
echo "   IMPLEMENTATION PRIORITIES\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

echo "PHASE 1 - CRITICAL (Implement First):\n";
foreach ($missingTables as $table => $data) {
    if ($data['priority'] == 'CRITICAL') {
        echo "  • $table\n";
        if (is_array($data['used_in'])) {
            echo "    Used in: " . implode(', ', $data['used_in']) . "\n";
        }
    }
}

echo "\nPHASE 2 - HIGH (Important):\n";
foreach ($missingTables as $table => $data) {
    if ($data['priority'] == 'HIGH') {
        echo "  • $table\n";
    }
}

echo "\nPHASE 3 - MEDIUM (Enhance Quality):\n";
foreach ($missingTables as $table => $data) {
    if ($data['priority'] == 'MEDIUM') {
        echo "  • $table\n";
    }
}

echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "   RECOMMENDED ACTIONS\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

echo "1. IMMEDIATE:\n";
echo "   • Populate existing tables (countries, currencies, languages, degrees)\n";
echo "   • Create CRITICAL tables (professions, visa_types, bd_divisions, skills)\n";
echo "   • Run comprehensive seeder for all lookup tables\n\n";

echo "2. SHORT TERM:\n";
echo "   • Create HIGH priority tables (relationship_types, marital_statuses, etc.)\n";
echo "   • Build admin interface for data management\n";
echo "   • Add validation rules using lookup tables\n\n";

echo "3. LONG TERM:\n";
echo "   • Create MEDIUM and LOW priority tables\n";
echo "   • Implement caching for frequently accessed data\n";
echo "   • Add multilingual support (Bengali translations)\n\n";

echo "═══════════════════════════════════════════════════════════════════\n\n";

$db->close();
