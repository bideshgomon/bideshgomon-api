<?php
/**
 * Check for Missing Admin Panel Links
 * Compares routes registered in Laravel with links in AdminLayout.vue
 */

// Get all admin routes from Laravel
$routesOutput = shell_exec('php artisan route:list --path=admin --name 2>&1');

// Parse admin routes (looking for GET routes only since those are navigation links)
preg_match_all('/GET\|HEAD\s+admin\/([^\s]+)\s+\.+\s+(admin\.[^\s]+)/', $routesOutput, $matches);

$registeredRoutes = [];
foreach ($matches[2] as $index => $routeName) {
    $path = $matches[1][$index];
    // Only include index routes for navigation (exclude show, edit, create routes)
    if (
        !str_contains($routeName, '.show') && 
        !str_contains($routeName, '.edit') && 
        !str_contains($routeName, '.create') &&
        !str_contains($routeName, '.destroy') &&
        !str_contains($routeName, 'bulk-upload') &&
        !str_contains($routeName, 'export') &&
        !str_contains($routeName, 'template') &&
        !str_contains($routeName, '.rooms') &&
        !str_contains($routeName, '.analytics') &&
        !str_contains($routeName, 'toggle-status')
    ) {
        $registeredRoutes[$routeName] = $path;
    }
}

// Get routes from AdminLayout.vue
$layoutFile = file_get_contents(__DIR__ . '/resources/js/Layouts/AdminLayout.vue');
preg_match_all("/route\('(admin\.[^']+)'\)/", $layoutFile, $layoutMatches);
$usedRoutes = array_unique($layoutMatches[1]);

// Find missing routes
$missingRoutes = [];
foreach ($registeredRoutes as $routeName => $path) {
    if (!in_array($routeName, $usedRoutes)) {
        $missingRoutes[$routeName] = $path;
    }
}

// Sort by category
$categorized = [
    'Core Management' => [],
    'User & Agency Management' => [],
    'Jobs & Applications' => [],
    'Visa & Travel' => [],
    'Financial' => [],
    'Content & Marketing' => [],
    'Services & Modules' => [],
    'Data Management' => [],
    'Tools & Settings' => [],
];

foreach ($missingRoutes as $routeName => $path) {
    // Categorize routes
    if (str_contains($routeName, '.users') || str_contains($routeName, '.agency-')) {
        $categorized['User & Agency Management'][$routeName] = $path;
    } elseif (str_contains($routeName, '.jobs') || str_contains($routeName, '.applications') || str_contains($routeName, '.job-applications')) {
        $categorized['Jobs & Applications'][$routeName] = $path;
    } elseif (str_contains($routeName, '.visa') || str_contains($routeName, '.hotel') || str_contains($routeName, '.flight')) {
        $categorized['Visa & Travel'][$routeName] = $path;
    } elseif (str_contains($routeName, '.wallet') || str_contains($routeName, '.reward') || str_contains($routeName, '.transaction')) {
        $categorized['Financial'][$routeName] = $path;
    } elseif (str_contains($routeName, '.blog') || str_contains($routeName, '.marketing')) {
        $categorized['Content & Marketing'][$routeName] = $path;
    } elseif (str_contains($routeName, '.service-') || str_contains($routeName, '.services')) {
        $categorized['Services & Modules'][$routeName] = $path;
    } elseif (str_contains($routeName, '.data.')) {
        $categorized['Data Management'][$routeName] = $path;
    } elseif (str_contains($routeName, '.dashboard') || str_contains($routeName, '.sitemap')) {
        $categorized['Core Management'][$routeName] = $path;
    } else {
        $categorized['Tools & Settings'][$routeName] = $path;
    }
}

// Output Report
echo "\n╔════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                   ADMIN PANEL MISSING LINKS REPORT                         ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════╝\n";

echo "\n📊 SUMMARY:\n";
echo "   Total Registered Routes: " . count($registeredRoutes) . "\n";
echo "   Routes in AdminLayout: " . count($usedRoutes) . "\n";
echo "   Missing Links: " . count($missingRoutes) . "\n\n";

$totalMissing = 0;
foreach ($categorized as $category => $routes) {
    if (empty($routes)) continue;
    
    $totalMissing += count($routes);
    echo "\n" . str_repeat("═", 80) . "\n";
    echo "📁 $category (" . count($routes) . " missing)\n";
    echo str_repeat("═", 80) . "\n";
    
    foreach ($routes as $routeName => $path) {
        // Generate suggested menu item
        $displayName = str_replace(['admin.', '-'], ['', ' '], $routeName);
        $displayName = ucwords($displayName);
        $displayName = str_replace(['.index', '.'], ['', ' › '], $displayName);
        
        echo "  ❌ $routeName\n";
        echo "     Path: /admin/$path\n";
        echo "     Suggested Label: \"$displayName\"\n\n";
    }
}

echo "\n" . str_repeat("═", 80) . "\n";
echo "💡 RECOMMENDATIONS:\n";
echo str_repeat("═", 80) . "\n";

if ($totalMissing > 0) {
    echo "\n✅ PRIORITY LINKS TO ADD:\n\n";
    
    // High priority items
    $highPriority = [
        'admin.document-categories.index' => 'Document Categories Management',
        'admin.master-documents.index' => 'Master Documents Library',
        'admin.document-assignments.index' => 'Country Document Assignments',
        'admin.agency-resources.index' => 'Agency Resources',
        'admin.sitemap' => 'Admin Sitemap (All Routes)',
    ];
    
    foreach ($highPriority as $route => $description) {
        if (isset($missingRoutes[$route])) {
            echo "  🔥 $route\n";
            echo "     Purpose: $description\n";
            echo "     Path: /admin/{$missingRoutes[$route]}\n\n";
        }
    }
    
    echo "\n📝 NEXT STEPS:\n";
    echo "  1. Review each missing route to determine if it needs a navigation link\n";
    echo "  2. Some routes may be accessed via context (e.g., nested resources)\n";
    echo "  3. Add navigation items to AdminLayout.vue for important routes\n";
    echo "  4. Consider grouping similar routes under collapsible sections\n";
    echo "  5. Update the navigation sections array in the layout component\n\n";
} else {
    echo "\n✅ All important routes are linked in the admin navigation!\n\n";
}

echo "\n" . str_repeat("═", 80) . "\n";
echo "📄 REPORT GENERATED: " . date('Y-m-d H:i:s') . "\n";
echo str_repeat("═", 80) . "\n\n";

// Generate suggested Vue code for missing links
if ($totalMissing > 0) {
    echo "\n" . str_repeat("═", 80) . "\n";
    echo "📋 SUGGESTED VUE CODE SNIPPETS\n";
    echo str_repeat("═", 80) . "\n\n";
    
    $priorityRoutes = array_filter($missingRoutes, function($routeName) {
        return in_array($routeName, [
            'admin.document-categories.index',
            'admin.master-documents.index', 
            'admin.document-assignments.index',
            'admin.agency-resources.index',
            'admin.sitemap',
        ]);
    }, ARRAY_FILTER_USE_KEY);
    
    foreach ($priorityRoutes as $routeName => $path) {
        $displayName = str_replace(['admin.', '-', '.index'], ['', ' ', ''], $routeName);
        $displayName = ucwords($displayName);
        
        echo "  {\n";
        echo "    name: '$displayName',\n";
        echo "    href: route('$routeName'),\n";
        echo "    icon: FolderIcon, // Choose appropriate icon\n";
        echo "    current: route().current('$routeName'),\n";
        echo "    section: 'data', // Choose appropriate section\n";
        echo "  },\n\n";
    }
}

echo "\n✨ Analysis Complete!\n\n";
