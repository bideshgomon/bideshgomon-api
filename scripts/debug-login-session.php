<?php

/**
 * Login Session Debug Script
 * Tests authentication and session persistence
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

echo "\n=== LOGIN SESSION DEBUG ===\n\n";

// Check sessions table
echo "📊 Checking sessions table...\n";
try {
    $sessionCount = DB::table('sessions')->count();
    echo "✅ Sessions table exists. Current sessions: {$sessionCount}\n\n";
} catch (\Exception $e) {
    echo "❌ Error accessing sessions table: " . $e->getMessage() . "\n\n";
}

// Check users
echo "👥 Checking users...\n";
$userCount = User::count();
echo "Total users: {$userCount}\n";

if ($userCount === 0) {
    echo "⚠️  No users found. Creating test user...\n";
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@bideshgomon.com',
        'password' => Hash::make('password'),
        'role_id' => 2, // User role
    ]);
    echo "✅ Test user created: {$user->email}\n";
} else {
    $user = User::first();
    echo "✅ Using existing user: {$user->email}\n";
}

echo "\n";

// Test authentication
echo "🔐 Testing authentication...\n";
$credentials = [
    'email' => $user->email,
    'password' => 'password',
];

try {
    if (Auth::attempt($credentials)) {
        echo "✅ Auth::attempt() successful!\n";
        echo "   User ID: " . Auth::id() . "\n";
        echo "   User Name: " . Auth::user()->name . "\n";
        
        // Check session
        echo "\n📦 Session status:\n";
        echo "   Session driver: " . config('session.driver') . "\n";
        echo "   Session lifetime: " . config('session.lifetime') . " minutes\n";
        echo "   Session cookie: " . config('session.cookie') . "\n";
        echo "   Session path: " . config('session.path') . "\n";
        echo "   Session domain: " . (config('session.domain') ?? 'null') . "\n";
        echo "   Session secure: " . (config('session.secure') ? 'true' : 'false') . "\n";
        echo "   Session same_site: " . config('session.same_site') . "\n";
        
        // Check if session is stored
        $sessionId = session()->getId();
        echo "\n   Current session ID: {$sessionId}\n";
        
        if (config('session.driver') === 'database') {
            $dbSession = DB::table('sessions')->where('id', $sessionId)->first();
            if ($dbSession) {
                echo "   ✅ Session found in database\n";
                echo "   Last activity: " . date('Y-m-d H:i:s', $dbSession->last_activity) . "\n";
            } else {
                echo "   ❌ Session NOT found in database\n";
            }
        }
        
        // Logout
        Auth::logout();
        echo "\n✅ Logout successful\n";
        
    } else {
        echo "❌ Auth::attempt() failed!\n";
        echo "   Credentials are incorrect or user doesn't exist\n";
    }
} catch (\Exception $e) {
    echo "❌ Authentication error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n";

// Check middleware configuration
echo "⚙️  Middleware configuration:\n";
echo "   EncryptCookies: " . (class_exists(\App\Http\Middleware\EncryptCookies::class) ? '✅' : '❌') . "\n";
echo "   StartSession: " . (class_exists(\Illuminate\Session\Middleware\StartSession::class) ? '✅' : '❌') . "\n";

echo "\n";

// Check Inertia middleware
echo "🔄 Inertia configuration:\n";
if (class_exists(\App\Http\Middleware\HandleInertiaRequests::class)) {
    echo "   ✅ HandleInertiaRequests middleware exists\n";
    
    // Test share method
    try {
        $middleware = new \App\Http\Middleware\HandleInertiaRequests();
        echo "   ✅ Middleware can be instantiated\n";
    } catch (\Exception $e) {
        echo "   ❌ Middleware instantiation error: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ HandleInertiaRequests middleware not found\n";
}

echo "\n";

// Recommendations
echo "💡 RECOMMENDATIONS:\n";
echo "\n";

echo "1. Session Configuration:\n";
if (config('session.driver') !== 'database') {
    echo "   ⚠️  Consider using 'database' driver for better debugging\n";
}
if (config('session.domain') !== null) {
    echo "   ⚠️  SESSION_DOMAIN is set. For localhost, it should be 'null'\n";
}
if (config('session.secure') === true && request()->getScheme() !== 'https') {
    echo "   ⚠️  SESSION_SECURE_COOKIE is true but not using HTTPS\n";
}

echo "\n2. Frontend Integration:\n";
echo "   • Ensure Inertia form posts to correct route\n";
echo "   • Check browser console for CSRF token errors\n";
echo "   • Verify cookies are being set (check DevTools > Application > Cookies)\n";
echo "   • Test with browser DevTools Network tab\n";

echo "\n3. Laravel Configuration:\n";
echo "   • Run: php artisan config:clear\n";
echo "   • Run: php artisan cache:clear\n";
echo "   • Run: php artisan route:clear\n";
echo "   • Check APP_URL matches your development URL\n";

echo "\n4. Inertia/Vue:\n";
echo "   • Ensure @inertiajs/vue3 is latest version\n";
echo "   • Check form.post(route('login')) is correct\n";
echo "   • Verify CSRF token is included in requests\n";
echo "   • Test with: console.log(usePage().props.auth.user)\n";

echo "\n=== DEBUG COMPLETE ===\n\n";
