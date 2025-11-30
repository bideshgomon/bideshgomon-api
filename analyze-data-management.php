<?php

/**
 * Analyze Data Management System
 * Check which profile fields can be auto-filled from lookup tables
 */

// Database connection
$db = new mysqli('localhost', 'root', '', 'bideshgomondb');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "═══════════════════════════════════════════════════════════════════\n";
echo "   DATA MANAGEMENT SYSTEM ANALYSIS\n";
echo "   Auto-fillable fields from lookup tables\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

// Define lookup tables and their usage in profile fields
$lookupTables = [
    'countries' => [
        'description' => 'Countries database',
        'fields' => ['id', 'name', 'iso_code_2', 'iso_code_3', 'phone_code', 'capital', 'currency_code', 'nationality'],
        'used_in_profile_fields' => [
            'user_profiles' => ['nationality'],
            'user_educations' => ['country'],
            'user_work_experiences' => ['country'],
            'user_passports' => ['issuing_country', 'nationality'],
            'user_visa_history' => ['country'],
            'user_travel_history' => ['country_visited'],
            'user_family_members' => ['country_of_residence', 'nationality'],
            'user_financial_information' => ['sponsor_country', 'tax_id_country'],
        ],
        'profile_fields_count' => 0
    ],
    
    'currencies' => [
        'description' => 'Currency codes and symbols',
        'fields' => ['id', 'code', 'name', 'symbol'],
        'used_in_profile_fields' => [
            'user_work_experiences' => ['currency'],
            'user_visa_history' => ['currency'],
            'user_family_members' => ['income_currency'],
            'user_financial_information' => ['currency', 'balance_currency', 'property_value_currency', 'sponsor_income_currency', 'funds_currency'],
        ],
        'profile_fields_count' => 0
    ],
    
    'languages' => [
        'description' => 'World languages',
        'fields' => ['id', 'name', 'code', 'native_name'],
        'used_in_profile_fields' => [
            'user_languages' => ['language_id', 'language'],
            'user_educations' => ['language_of_instruction'],
        ],
        'profile_fields_count' => 0
    ],
    
    'language_tests' => [
        'description' => 'Language proficiency tests (IELTS, TOEFL, etc.)',
        'fields' => ['id', 'name', 'language_id', 'max_score'],
        'used_in_profile_fields' => [
            'user_languages' => ['language_test_id', 'test_taken'],
        ],
        'profile_fields_count' => 0
    ],
    
    'degrees' => [
        'description' => 'Academic degrees and qualifications',
        'fields' => ['id', 'name', 'level'],
        'used_in_profile_fields' => [
            'user_educations' => ['degree'],
        ],
        'profile_fields_count' => 0
    ],
    
    'cities' => [
        'description' => 'Major cities worldwide',
        'fields' => ['id', 'name', 'country_id', 'state_province'],
        'used_in_profile_fields' => [
            'user_profiles' => ['present_city', 'permanent_city'],
            'user_educations' => ['city'],
            'user_work_experiences' => ['city'],
            'user_travel_history' => ['city_visited'],
            'user_family_members' => ['city'],
        ],
        'profile_fields_count' => 0
    ],
    
    'skills' => [
        'description' => 'Professional skills database',
        'fields' => ['id', 'name', 'category_id'],
        'used_in_profile_fields' => [
            'user_skill' => ['skill_id'], // Junction table
        ],
        'profile_fields_count' => 0
    ],
    
    'document_types' => [
        'description' => 'Document categories for uploads',
        'fields' => ['id', 'name', 'slug', 'description', 'required_for'],
        'used_in_profile_fields' => [
            'user_documents' => ['document_type_id'],
        ],
        'profile_fields_count' => 0
    ],
    
    'relationship_types' => [
        'description' => 'Family relationship types',
        'fields' => ['id', 'name', 'slug', 'description'],
        'used_in_profile_fields' => [
            'user_family_members' => ['relationship'],
        ],
        'profile_fields_count' => 0
    ],
    
    'bank_names' => [
        'description' => 'Banks in Bangladesh',
        'fields' => ['id', 'name', 'swift_code', 'type'],
        'used_in_profile_fields' => [
            'user_profiles' => ['bank_name'],
            'user_financial_information' => ['primary_bank_name'],
        ],
        'profile_fields_count' => 0
    ],
    
    'institution_types' => [
        'description' => 'Types of educational institutions',
        'fields' => ['id', 'name', 'description'],
        'used_in_profile_fields' => [
            'user_educations' => ['institution_type (if implemented)'],
        ],
        'profile_fields_count' => 0
    ],
];

// Get counts from database
echo "📊 LOOKUP TABLES STATUS:\n";
echo "───────────────────────────────────────────────────────────────────\n\n";

$totalRecords = 0;
$totalTables = 0;
$totalProfileFields = 0;

foreach ($lookupTables as $table => $info) {
    // Check if table exists
    $result = $db->query("SHOW TABLES LIKE '$table'");
    
    if ($result && $result->num_rows > 0) {
        // Get count
        $countResult = $db->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $countResult ? $countResult->fetch_assoc()['count'] : 0;
        
        // Count profile fields
        $fieldsCount = 0;
        foreach ($info['used_in_profile_fields'] as $profileTable => $fields) {
            $fieldsCount += count($fields);
        }
        
        $lookupTables[$table]['profile_fields_count'] = $fieldsCount;
        $totalProfileFields += $fieldsCount;
        $totalRecords += $count;
        $totalTables++;
        
        $status = $count > 0 ? '✓' : '✗';
        printf("%-25s : %4d records  %s  → %2d profile fields\n", 
            strtoupper($table), $count, $status, $fieldsCount);
        printf("   %s\n\n", $info['description']);
    } else {
        printf("%-25s : TABLE NOT FOUND ✗\n\n", strtoupper($table));
    }
}

echo "───────────────────────────────────────────────────────────────────\n";
printf("TOTAL LOOKUP TABLES           : %d tables\n", $totalTables);
printf("TOTAL RECORDS                 : %d records\n", $totalRecords);
printf("TOTAL PROFILE FIELDS AFFECTED : %d fields\n", $totalProfileFields);
echo "───────────────────────────────────────────────────────────────────\n\n";

// Detailed breakdown
echo "═══════════════════════════════════════════════════════════════════\n";
echo "   PROFILE FIELDS AUTO-FILLABLE BY TABLE\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

foreach ($lookupTables as $table => $info) {
    if (isset($info['used_in_profile_fields']) && count($info['used_in_profile_fields']) > 0) {
        echo strtoupper($table) . ":\n";
        foreach ($info['used_in_profile_fields'] as $profileTable => $fields) {
            echo "  → $profileTable: " . implode(', ', $fields) . "\n";
        }
        echo "\n";
    }
}

// Calculate impact
echo "═══════════════════════════════════════════════════════════════════\n";
echo "   IMPACT ANALYSIS\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

echo "Out of 327 total fillable fields:\n\n";
printf("Fields with dropdown/autocomplete : ~%d fields (%.1f%%)\n", 
    $totalProfileFields, 
    ($totalProfileFields / 327) * 100
);
printf("Fields requiring manual input     : ~%d fields (%.1f%%)\n", 
    327 - $totalProfileFields, 
    ((327 - $totalProfileFields) / 327) * 100
);

echo "\n✓ Data management system provides standardized options\n";
echo "✓ Reduces typos and inconsistencies\n";
echo "✓ Enables better filtering and reporting\n";
echo "✓ Improves data quality for visa applications\n";

// Check which tables need more data
echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "   RECOMMENDATIONS\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

foreach ($lookupTables as $table => $info) {
    $result = $db->query("SHOW TABLES LIKE '$table'");
    if ($result && $result->num_rows > 0) {
        $countResult = $db->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $countResult ? $countResult->fetch_assoc()['count'] : 0;
        
        if ($count < 10) {
            echo "⚠️  $table: Only $count records - needs expansion\n";
        } elseif ($count >= 100) {
            echo "✅ $table: $count records - well populated\n";
        }
    }
}

echo "\n═══════════════════════════════════════════════════════════════════\n\n";

$db->close();
