<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DEEP SCAN: PROFILE SECTIONS ===\n\n";

// Get test user
$user = App\Models\User::where('email', 'test@test.com')->first();
if (!$user) {
    echo "❌ Test user not found\n";
    exit(1);
}

echo "Testing with user: {$user->name} ({$user->email})\n\n";

// Section 1: Basic Information
echo "1️⃣ BASIC INFORMATION\n";
echo "   Database Check:\n";
$profile = $user->userProfile;
if ($profile) {
    echo "   ✅ user_profiles record exists\n";
    echo "      - first_name: " . ($profile->first_name ?? 'NULL') . "\n";
    echo "      - middle_name: " . ($profile->middle_name ?? 'NULL') . "\n";
    echo "      - last_name: " . ($profile->last_name ?? 'NULL') . "\n";
    echo "      - name_as_per_passport: " . ($profile->name_as_per_passport ?? 'NULL') . "\n";
} else {
    echo "   ❌ user_profiles record missing\n";
}
echo "   - email: {$user->email}\n";

// Section 2: Profile Details
echo "\n2️⃣ PROFILE DETAILS\n";
if ($profile) {
    $fields = ['date_of_birth', 'gender', 'nationality', 'marital_status', 'present_address_line', 
               'present_city', 'present_postal_code', 'present_country', 
               'permanent_address_line', 'permanent_city', 'permanent_postal_code', 'permanent_country'];
    foreach ($fields as $field) {
        $value = $profile->$field ?? 'NULL';
        $status = $value !== 'NULL' && $value !== '' ? '✅' : '❌';
        echo "   {$status} {$field}: {$value}\n";
    }
}

// Section 3: Phone Numbers
echo "\n3️⃣ PHONE NUMBERS\n";
$phones = DB::table('user_phone_numbers')->where('user_id', $user->id)->get();
echo "   Count: " . $phones->count() . "\n";
foreach ($phones as $phone) {
    echo "   - {$phone->phone_number} (Primary: " . ($phone->is_primary ? 'Yes' : 'No') . ")\n";
}

// Section 4: Education
echo "\n4️⃣ EDUCATION & QUALIFICATIONS\n";
$educations = DB::table('user_educations')->where('user_id', $user->id)->get();
echo "   Count: " . $educations->count() . "\n";
foreach ($educations as $edu) {
    echo "   - {$edu->degree_name} from {$edu->institution_name}\n";
}

// Section 5: Work Experience
echo "\n5️⃣ WORK EXPERIENCE\n";
$work = DB::table('user_work_experiences')->where('user_id', $user->id)->get();
echo "   Count: " . $work->count() . "\n";
foreach ($work as $w) {
    echo "   - {$w->job_title} at {$w->company_name}\n";
}

// Section 6: Skills
echo "\n6️⃣ SKILLS & EXPERTISE\n";
$skills = DB::table('user_skill')->where('user_id', $user->id)->get();
echo "   Count: " . $skills->count() . "\n";

// Section 7: Travel History
echo "\n7️⃣ TRAVEL HISTORY\n";
$travel = DB::table('user_travel_history')->where('user_id', $user->id)->get();
echo "   Count: " . $travel->count() . "\n";

// Section 8: Family Members
echo "\n8️⃣ FAMILY INFORMATION\n";
$family = DB::table('user_family_members')->where('user_id', $user->id)->get();
echo "   Count: " . $family->count() . "\n";

// Section 9: Financial Information
echo "\n9️⃣ FINANCIAL INFORMATION\n";
if ($profile) {
    $financialFields = ['monthly_income_bdt', 'annual_income_bdt', 'employer_name', 
                        'bank_name', 'bank_balance_bdt', 'other_assets_bdt'];
    foreach ($financialFields as $field) {
        $value = $profile->$field ?? 'NULL';
        $status = $value !== 'NULL' && $value !== '' ? '✅' : '❌';
        echo "   {$status} {$field}: {$value}\n";
    }
}

// Section 10: Languages
echo "\n🔟 LANGUAGE PROFICIENCY\n";
$languages = DB::table('user_languages')->where('user_id', $user->id)->get();
echo "   Count: " . $languages->count() . "\n";

// Section 11: Security Information
echo "\n1️⃣1️⃣ BACKGROUND & SECURITY\n";
$security = DB::table('user_security_information')->where('user_id', $user->id)->get();
echo "   Count: " . $security->count() . "\n";

// Section 12: Passports
echo "\n1️⃣2️⃣ PASSPORT INFORMATION\n";
$passports = DB::table('user_passports')->where('user_id', $user->id)->get();
echo "   Count: " . $passports->count() . "\n";
foreach ($passports as $p) {
    echo "   - {$p->passport_number} (Primary: " . ($p->is_primary ? 'Yes' : 'No') . ")\n";
}

// Calculate overall completion
echo "\n\n=== COMPLETION CALCULATION ===\n";
$completion = $user->calculateProfileCompletion();
echo "Backend Score: {$completion}%\n\n";

// Check for missing tables
echo "=== DATABASE TABLE CHECK ===\n";
$requiredTables = [
    'users', 'user_profiles', 'user_phone_numbers', 'user_educations', 
    'user_work_experiences', 'user_skill', 'skills', 'user_travel_history',
    'user_family_members', 'user_languages', 'languages', 'language_tests',
    'user_security_information', 'user_passports', 'user_visa_history',
    'countries', 'cities', 'wallets', 'wallet_transactions'
];

$existingTables = DB::select("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'bideshgomondb'");
$tableNames = array_map(fn($t) => $t->TABLE_NAME, $existingTables);

foreach ($requiredTables as $table) {
    if (in_array($table, $tableNames)) {
        echo "✅ {$table}\n";
    } else {
        echo "❌ {$table} - MISSING\n";
    }
}

// Check UserProfile model fillable fields
echo "\n\n=== USER_PROFILES SCHEMA CHECK ===\n";
$columns = DB::select("DESCRIBE user_profiles");
foreach ($columns as $col) {
    echo "   {$col->Field} ({$col->Type}) - " . ($col->Null === 'YES' ? 'Nullable' : 'Required') . "\n";
}

echo "\n✅ Deep scan complete!\n";
