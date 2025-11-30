<?php

/**
 * COMPREHENSIVE PROFILE SECTIONS SCAN
 * Analyze all profile components, controllers, routes, and database for issues
 */

echo "═══════════════════════════════════════════════════════════════════\n";
echo "   PROFILE SECTIONS COMPREHENSIVE SCAN\n";
echo "   Analyzing Loading, Saving, Design, and Validation Issues\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

// Database connection
$db = new mysqli('localhost', 'root', '', 'bideshgomondb');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Define all profile sections
$profileSections = [
    'basic_details' => [
        'table' => 'user_profiles',
        'controller' => 'app/Http/Controllers/Profile/ProfileController.php',
        'vue_component' => 'resources/js/Pages/Profile/Edit.vue',
        'routes' => ['profile.edit', 'profile.update'],
        'key_fields' => ['first_name', 'last_name', 'date_of_birth', 'gender', 'phone', 'email']
    ],
    'passports' => [
        'table' => 'user_passports',
        'controller' => 'app/Http/Controllers/Profile/PassportController.php',
        'vue_component' => 'resources/js/Components/Profile/PassportManagement.vue',
        'routes' => ['profile.passports.index', 'profile.passports.store', 'profile.passports.update', 'profile.passports.destroy'],
        'key_fields' => ['passport_number', 'issue_date', 'expiry_date', 'issuing_country']
    ],
    'visa_history' => [
        'table' => 'user_visa_history',
        'controller' => 'app/Http/Controllers/Profile/VisaHistoryController.php',
        'vue_component' => 'resources/js/Components/Profile/VisaHistoryManagement.vue',
        'routes' => ['profile.visa-history.index', 'profile.visa-history.store'],
        'key_fields' => ['country', 'visa_type', 'issue_date', 'expiry_date']
    ],
    'travel_history' => [
        'table' => 'user_travel_history',
        'controller' => 'app/Http/Controllers/Profile/TravelHistoryController.php',
        'vue_component' => 'resources/js/Components/Profile/TravelHistoryManagement.vue',
        'routes' => ['profile.travel-history.index', 'profile.travel-history.store'],
        'key_fields' => ['country', 'entry_date', 'exit_date', 'purpose']
    ],
    'family_members' => [
        'table' => 'user_family_members',
        'controller' => 'app/Http/Controllers/Profile/FamilyMemberController.php',
        'vue_component' => 'resources/js/Components/Profile/FamilyMemberManagement.vue',
        'routes' => ['profile.family-members.index', 'profile.family-members.store'],
        'key_fields' => ['full_name', 'relationship', 'date_of_birth', 'nationality']
    ],
    'financial_information' => [
        'table' => 'user_financial_information',
        'controller' => 'app/Http/Controllers/Profile/ProfileController.php',
        'vue_component' => 'resources/js/Pages/Profile/Edit.vue',
        'routes' => ['profile.update'],
        'key_fields' => ['annual_income', 'currency', 'source_of_income', 'has_property']
    ],
    'security_information' => [
        'table' => 'user_security_information',
        'controller' => 'app/Http/Controllers/Profile/SecurityInformationController.php',
        'vue_component' => 'resources/js/Components/Profile/SecurityInformationManagement.vue',
        'routes' => ['profile.security-information.index', 'profile.security-information.store'],
        'key_fields' => ['has_criminal_record', 'has_medical_conditions', 'has_military_service']
    ],
    'education' => [
        'table' => 'user_educations',
        'controller' => 'app/Http/Controllers/Profile/EducationController.php',
        'vue_component' => 'resources/js/Components/Profile/EducationManagement.vue',
        'routes' => ['profile.education.index', 'profile.education.store'],
        'key_fields' => ['institution_name', 'degree', 'field_of_study', 'start_date', 'end_date']
    ],
    'work_experience' => [
        'table' => 'user_work_experiences',
        'controller' => 'app/Http/Controllers/Profile/WorkExperienceController.php',
        'vue_component' => 'resources/js/Components/Profile/WorkExperienceManagement.vue',
        'routes' => ['profile.work-experiences.index', 'profile.work-experiences.store'],
        'key_fields' => ['company_name', 'job_title', 'start_date', 'end_date']
    ],
    'languages' => [
        'table' => 'user_languages',
        'controller' => 'app/Http/Controllers/Profile/LanguageController.php',
        'vue_component' => 'resources/js/Components/Profile/LanguageManagement.vue',
        'routes' => ['profile.languages.index', 'profile.languages.store'],
        'key_fields' => ['language', 'proficiency_level', 'test_taken']
    ],
    'documents' => [
        'table' => 'user_documents',
        'controller' => 'app/Http/Controllers/Profile/DocumentController.php',
        'vue_component' => 'resources/js/Components/Profile/DocumentManagement.vue',
        'routes' => ['profile.documents.index', 'profile.documents.store'],
        'key_fields' => ['document_type', 'document_number', 'upload_path']
    ]
];

echo "PHASE 1: DATABASE TABLES VERIFICATION\n";
echo "───────────────────────────────────────────────────────────────────\n\n";

$databaseIssues = [];
foreach ($profileSections as $section => $config) {
    $table = $config['table'];
    
    // Check if table exists
    $result = $db->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows == 0) {
        $databaseIssues[] = "❌ Table '$table' does NOT exist";
        continue;
    }
    
    // Get record count
    $countResult = $db->query("SELECT COUNT(*) as count FROM $table");
    $count = $countResult->fetch_assoc()['count'];
    
    // Get column info
    $columnsResult = $db->query("DESCRIBE $table");
    $columns = [];
    while ($col = $columnsResult->fetch_assoc()) {
        $columns[] = $col['Field'];
    }
    
    echo "✅ $table: $count records, " . count($columns) . " columns\n";
    
    // Check for user_id column
    if (!in_array('user_id', $columns)) {
        $databaseIssues[] = "⚠️  Table '$table' missing 'user_id' column";
    }
    
    // Check for key fields
    foreach ($config['key_fields'] as $field) {
        if (!in_array($field, $columns)) {
            $databaseIssues[] = "⚠️  Table '$table' missing key field '$field'";
        }
    }
}

echo "\n";
if (count($databaseIssues) > 0) {
    echo "DATABASE ISSUES FOUND:\n";
    foreach ($databaseIssues as $issue) {
        echo "  $issue\n";
    }
} else {
    echo "✅ All database tables verified successfully\n";
}

echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "PHASE 2: CONTROLLER FILES VERIFICATION\n";
echo "───────────────────────────────────────────────────────────────────\n\n";

$controllerIssues = [];
foreach ($profileSections as $section => $config) {
    $controllerPath = $config['controller'];
    
    if (!file_exists($controllerPath)) {
        $controllerIssues[] = "❌ Controller missing: $controllerPath";
        echo "❌ $section: Controller NOT FOUND\n";
        continue;
    }
    
    $controllerContent = file_get_contents($controllerPath);
    
    // Check for essential methods
    $hasIndex = strpos($controllerContent, 'function index()') !== false;
    $hasStore = strpos($controllerContent, 'function store(') !== false;
    $hasUpdate = strpos($controllerContent, 'function update(') !== false;
    $hasDestroy = strpos($controllerContent, 'function destroy(') !== false;
    
    // Check for validation
    $hasValidation = strpos($controllerContent, '->validate(') !== false || 
                     strpos($controllerContent, 'Validator::make') !== false;
    
    // Check for authorization
    $hasAuthCheck = strpos($controllerContent, 'auth()->id()') !== false ||
                    strpos($controllerContent, '$request->user()') !== false;
    
    // Check for DB transactions
    $hasTransaction = strpos($controllerContent, 'DB::transaction') !== false;
    
    $methods = [];
    if ($hasIndex) $methods[] = 'index';
    if ($hasStore) $methods[] = 'store';
    if ($hasUpdate) $methods[] = 'update';
    if ($hasDestroy) $methods[] = 'destroy';
    
    echo "✅ $section: " . implode(', ', $methods);
    
    $flags = [];
    if (!$hasValidation) {
        $flags[] = '⚠️  NO VALIDATION';
        $controllerIssues[] = "$section controller missing validation";
    }
    if (!$hasAuthCheck) {
        $flags[] = '⚠️  NO AUTH CHECK';
        $controllerIssues[] = "$section controller missing authorization";
    }
    if ($hasStore && !$hasTransaction) {
        $flags[] = '⚠️  NO TRANSACTION';
        $controllerIssues[] = "$section controller missing DB transaction";
    }
    
    if (count($flags) > 0) {
        echo " [" . implode(', ', $flags) . "]";
    }
    
    echo "\n";
}

echo "\n";
if (count($controllerIssues) > 0) {
    echo "CONTROLLER ISSUES FOUND:\n";
    foreach ($controllerIssues as $issue) {
        echo "  • $issue\n";
    }
} else {
    echo "✅ All controllers verified successfully\n";
}

echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "PHASE 3: VUE COMPONENTS VERIFICATION\n";
echo "───────────────────────────────────────────────────────────────────\n\n";

$vueIssues = [];
foreach ($profileSections as $section => $config) {
    $vuePath = $config['vue_component'];
    
    if (!file_exists($vuePath)) {
        $vueIssues[] = "❌ Vue component missing: $vuePath";
        echo "❌ $section: Component NOT FOUND\n";
        continue;
    }
    
    $vueContent = file_get_contents($vuePath);
    $fileSize = strlen($vueContent);
    
    // Check for essential Vue patterns
    $hasUseForm = strpos($vueContent, 'useForm') !== false;
    $hasDefineProps = strpos($vueContent, 'defineProps') !== false;
    $hasVModel = strpos($vueContent, 'v-model') !== false;
    $hasSubmit = strpos($vueContent, 'form.post') !== false || 
                 strpos($vueContent, 'form.put') !== false ||
                 strpos($vueContent, 'form.patch') !== false;
    
    // Check for error handling
    $hasErrorDisplay = strpos($vueContent, 'form.errors') !== false;
    
    // Check for success messages
    $hasSuccessMessage = strpos($vueContent, 'flash.success') !== false ||
                         strpos($vueContent, 'onSuccess') !== false;
    
    // Check for loading states
    $hasLoadingState = strpos($vueContent, 'form.processing') !== false ||
                       strpos($vueContent, 'loading') !== false;
    
    // Check for empty states
    $hasEmptyState = strpos($vueContent, 'No items') !== false ||
                     strpos($vueContent, 'empty') !== false;
    
    // Count forms
    $formCount = substr_count($vueContent, '<form');
    
    echo "✅ $section: " . number_format($fileSize) . " bytes, $formCount form(s)";
    
    $flags = [];
    if (!$hasUseForm) {
        $flags[] = '⚠️  NO useForm';
        $vueIssues[] = "$section component not using Inertia useForm()";
    }
    if (!$hasErrorDisplay) {
        $flags[] = '⚠️  NO ERROR DISPLAY';
        $vueIssues[] = "$section component missing error display";
    }
    if (!$hasLoadingState) {
        $flags[] = '⚠️  NO LOADING STATE';
        $vueIssues[] = "$section component missing loading state";
    }
    
    if (count($flags) > 0) {
        echo " [" . implode(', ', $flags) . "]";
    }
    
    echo "\n";
}

echo "\n";
if (count($vueIssues) > 0) {
    echo "VUE COMPONENT ISSUES FOUND:\n";
    foreach ($vueIssues as $issue) {
        echo "  • $issue\n";
    }
} else {
    echo "✅ All Vue components verified successfully\n";
}

echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "PHASE 4: ROUTES VERIFICATION\n";
echo "───────────────────────────────────────────────────────────────────\n\n";

$routesFile = 'routes/web.php';
if (file_exists($routesFile)) {
    $routesContent = file_get_contents($routesFile);
    
    $routeIssues = [];
    foreach ($profileSections as $section => $config) {
        echo "Checking routes for $section:\n";
        
        $missingRoutes = [];
        foreach ($config['routes'] as $route) {
            $routePattern = "->name('$route')";
            if (strpos($routesContent, $routePattern) === false) {
                $missingRoutes[] = $route;
                $routeIssues[] = "Missing route: $route";
            } else {
                echo "  ✅ $route\n";
            }
        }
        
        if (count($missingRoutes) > 0) {
            foreach ($missingRoutes as $missing) {
                echo "  ❌ $missing\n";
            }
        }
        echo "\n";
    }
    
    if (count($routeIssues) > 0) {
        echo "ROUTE ISSUES FOUND:\n";
        foreach ($routeIssues as $issue) {
            echo "  • $issue\n";
        }
    } else {
        echo "✅ All routes verified successfully\n";
    }
} else {
    echo "❌ Routes file not found: $routesFile\n";
}

echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "PHASE 5: DESIGN & COLOR ANALYSIS\n";
echo "───────────────────────────────────────────────────────────────────\n\n";

$colorPatterns = [
    'inconsistent_colors' => [],
    'non_standard_colors' => [],
];

foreach ($profileSections as $section => $config) {
    $vuePath = $config['vue_component'];
    if (!file_exists($vuePath)) continue;
    
    $vueContent = file_get_contents($vuePath);
    
    // Check for inline colors (bad practice)
    if (preg_match_all('/(bg-\w+-\d+|text-\w+-\d+|border-\w+-\d+)/', $vueContent, $matches)) {
        $colors = array_unique($matches[0]);
        
        // Check for non-standard colors
        $nonStandard = [];
        foreach ($colors as $color) {
            // Check for random numbered colors (not 50, 100, 200, etc.)
            if (preg_match('/-(\d+)$/', $color, $num)) {
                $shade = $num[1];
                $validShades = [50, 100, 200, 300, 400, 500, 600, 700, 800, 900];
                if (!in_array($shade, $validShades)) {
                    $nonStandard[] = $color;
                }
            }
            
            // Check for weird colors (pink, purple, fuchsia, etc. in non-brand areas)
            if (preg_match('/(pink|purple|fuchsia|rose|violet)-/', $color)) {
                $nonStandard[] = $color;
            }
        }
        
        if (count($nonStandard) > 0) {
            $colorPatterns['non_standard_colors'][$section] = $nonStandard;
        }
    }
}

if (count($colorPatterns['non_standard_colors']) > 0) {
    echo "⚠️  NON-STANDARD COLORS FOUND:\n";
    foreach ($colorPatterns['non_standard_colors'] as $section => $colors) {
        echo "  $section: " . implode(', ', array_slice($colors, 0, 5));
        if (count($colors) > 5) echo " ..." . (count($colors) - 5) . " more";
        echo "\n";
    }
} else {
    echo "✅ No color issues detected\n";
}

echo "\n═══════════════════════════════════════════════════════════════════\n";
echo "SUMMARY & RECOMMENDATIONS\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

$totalIssues = count($databaseIssues) + count($controllerIssues) + count($vueIssues) + count($routeIssues ?? []);

echo "TOTAL ISSUES FOUND: $totalIssues\n\n";

echo "CRITICAL ISSUES:\n";
$criticalCount = 0;
if (count($databaseIssues) > 0) {
    $criticalCount += count(array_filter($databaseIssues, fn($i) => strpos($i, '❌') !== false));
}
if (count($controllerIssues) > 0) {
    $criticalCount += count(array_filter($controllerIssues, fn($i) => strpos($i, 'missing') !== false));
}
if (count($vueIssues) > 0) {
    $criticalCount += count(array_filter($vueIssues, fn($i) => strpos($i, 'missing') !== false));
}
echo "  • Missing files/tables: $criticalCount\n";
echo "  • Missing validation: " . count(array_filter($controllerIssues, fn($i) => strpos($i, 'validation') !== false)) . "\n";
echo "  • Missing auth checks: " . count(array_filter($controllerIssues, fn($i) => strpos($i, 'authorization') !== false)) . "\n\n";

echo "WARNING ISSUES:\n";
echo "  • Missing error displays: " . count(array_filter($vueIssues, fn($i) => strpos($i, 'error display') !== false)) . "\n";
echo "  • Missing loading states: " . count(array_filter($vueIssues, fn($i) => strpos($i, 'loading state') !== false)) . "\n";
echo "  • Non-standard colors: " . count($colorPatterns['non_standard_colors']) . " components\n\n";

echo "═══════════════════════════════════════════════════════════════════\n";
echo "RECOMMENDED ACTIONS:\n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

echo "1. IMMEDIATE (Critical):\n";
echo "   • Fix all missing controllers/components\n";
echo "   • Add validation to all controllers\n";
echo "   • Add authorization checks (user_id === auth()->id())\n";
echo "   • Wrap all save operations in DB::transaction()\n\n";

echo "2. HIGH PRIORITY:\n";
echo "   • Add error display to all forms\n";
echo "   • Add loading states (form.processing)\n";
echo "   • Add success/error messages\n";
echo "   • Fix all missing routes\n\n";

echo "3. DESIGN STANDARDIZATION:\n";
echo "   • Replace non-standard colors with design system\n";
echo "   • Use consistent spacing (p-4, p-6, p-8)\n";
echo "   • Standardize button styles\n";
echo "   • Use consistent card/form layouts\n\n";

echo "4. DATA MANAGEMENT (Admin CRUD):\n";
echo "   • Create AdminDataManagementController\n";
echo "   • Build CRUD interface for all 45 lookup tables\n";
echo "   • Add bulk import/export functionality\n";
echo "   • Implement proper color scheme (blue/gray, not pink/purple)\n\n";

echo "═══════════════════════════════════════════════════════════════════\n\n";

$db->close();
