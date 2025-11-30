<?php

/**
 * Verify User Profile Database Schema
 * Compare actual database columns with expected counts from models
 */

// Database connection
$db = new mysqli('localhost', 'root', '', 'bideshgomondb');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Expected counts from models (fillable fields + system fields)
$expectedCounts = [
    'user_profiles' => 75,
    'user_educations' => 20,
    'user_work_experiences' => 31,
    'user_languages' => 29,
    'user_passports' => 28,
    'user_visa_history' => 29,
    'user_travel_history' => 28,
    'user_family_members' => 33,
    'user_financial_information' => 57,
    'user_security_information' => 65,
    'user_phone_numbers' => 10,
];

echo "═══════════════════════════════════════════════════════════════════\n";
echo "   DATABASE SCHEMA VERIFICATION\n";
echo "   Comparing actual vs expected column counts\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

$totalExpected = 0;
$totalActual = 0;
$issues = [];

foreach ($expectedCounts as $table => $expectedCount) {
    $totalExpected += $expectedCount;
    
    // Get actual column count
    $result = $db->query("SELECT COUNT(*) as count FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'bideshgomondb' AND TABLE_NAME = '$table'");
    
    if ($result) {
        $row = $result->fetch_assoc();
        $actualCount = $row['count'];
        $totalActual += $actualCount;
        
        $status = $actualCount == $expectedCount ? '✓' : '✗';
        $diff = $actualCount - $expectedCount;
        $diffStr = $diff > 0 ? "+$diff" : "$diff";
        
        if ($actualCount != $expectedCount) {
            $issues[] = [
                'table' => $table,
                'expected' => $expectedCount,
                'actual' => $actualCount,
                'diff' => $diff
            ];
        }
        
        printf("%-30s : %3d columns (expected: %3d) %s %s\n", 
            strtoupper(str_replace('_', ' ', $table)),
            $actualCount,
            $expectedCount,
            $status,
            $actualCount != $expectedCount ? "[$diffStr]" : ""
        );
    } else {
        echo "ERROR: Could not query $table\n";
    }
}

echo "\n───────────────────────────────────────────────────────────────────\n";
printf("TOTAL EXPECTED                 : %d columns\n", $totalExpected);
printf("TOTAL ACTUAL                   : %d columns\n", $totalActual);
printf("DIFFERENCE                     : %+d columns\n", $totalActual - $totalExpected);
echo "───────────────────────────────────────────────────────────────────\n\n";

if (count($issues) > 0) {
    echo "═══════════════════════════════════════════════════════════════════\n";
    echo "   DISCREPANCIES FOUND\n";
    echo "═══════════════════════════════════════════════════════════════════\n\n";
    
    foreach ($issues as $issue) {
        echo "Table: {$issue['table']}\n";
        echo "  Expected: {$issue['expected']} columns\n";
        echo "  Actual:   {$issue['actual']} columns\n";
        echo "  Diff:     " . ($issue['diff'] > 0 ? "+{$issue['diff']}" : $issue['diff']) . " columns\n";
        
        // Get missing or extra columns
        if ($issue['diff'] != 0) {
            echo "  \n  Columns in database:\n";
            $result = $db->query("SELECT COLUMN_NAME, DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'bideshgomondb' AND TABLE_NAME = '{$issue['table']}' ORDER BY ORDINAL_POSITION");
            while ($row = $result->fetch_assoc()) {
                echo "    - {$row['COLUMN_NAME']} ({$row['DATA_TYPE']})\n";
            }
        }
        echo "\n";
    }
} else {
    echo "✓ All tables match expected schema!\n";
    echo "✓ Total: {$totalActual} columns across " . count($expectedCounts) . " tables\n";
}

// Additional verification: Check for any user_ tables not in our list
echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "   ADDITIONAL USER TABLES\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

$result = $db->query("SELECT TABLE_NAME, (SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'bideshgomondb' AND COLUMNS.TABLE_NAME = TABLES.TABLE_NAME) as column_count FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'bideshgomondb' AND TABLE_NAME LIKE 'user_%' ORDER BY TABLE_NAME");

$knownTables = array_keys($expectedCounts);
$additionalTables = [];

while ($row = $result->fetch_assoc()) {
    if (!in_array($row['TABLE_NAME'], $knownTables) && $row['TABLE_NAME'] != 'users') {
        $additionalTables[] = $row;
        printf("%-30s : %3d columns (not in profile system)\n", 
            strtoupper(str_replace('_', ' ', $row['TABLE_NAME'])),
            $row['column_count']
        );
    }
}

if (count($additionalTables) == 0) {
    echo "✓ No additional user tables found outside profile system\n";
}

echo "\n═══════════════════════════════════════════════════════════════════\n\n";

$db->close();
