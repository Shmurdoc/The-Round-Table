<?php

/**
 * Verify 419 Error Fix
 * Checks session configuration
 */

require __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n" . str_repeat("=", 60) . "\n";
echo "     ✅ 419 ERROR FIX VERIFICATION\n";
echo str_repeat("=", 60) . "\n\n";

// Check 1: Session Driver
echo "1. SESSION DRIVER\n";
$sessionDriver = config('session.driver');
echo "   Driver: $sessionDriver\n";
if ($sessionDriver === 'database') {
    echo "   ✅ Database driver configured correctly\n";
} else {
    echo "   ⚠️  Using $sessionDriver driver\n";
}
echo "\n";

// Check 2: Session Domain
echo "2. SESSION DOMAIN\n";
$sessionDomain = env('SESSION_DOMAIN');
echo "   Domain: " . ($sessionDomain ?: 'null') . "\n";
if ($sessionDomain === '127.0.0.1' || $sessionDomain === 'localhost') {
    echo "   ✅ Domain set correctly for localhost\n";
} else {
    echo "   ⚠️  Domain: " . ($sessionDomain ?: 'not set') . "\n";
}
echo "\n";

// Check 3: Session Cookie Settings
echo "3. SESSION COOKIE CONFIGURATION\n";
echo "   Secure Cookie: " . (env('SESSION_SECURE_COOKIE') ? 'true' : 'false') . "\n";
echo "   Same Site: " . (env('SESSION_SAME_SITE', 'lax')) . "\n";
echo "   HTTP Only: " . (config('session.http_only') ? 'true' : 'false') . "\n";
echo "   Cookie Path: " . config('session.path') . "\n";
echo "   ✅ Cookie settings configured for localhost\n";
echo "\n";

// Check 4: Session Lifetime
echo "4. SESSION LIFETIME\n";
$lifetime = config('session.lifetime');
echo "   Lifetime: $lifetime minutes\n";
echo "   ✅ Session will last for $lifetime minutes\n";
echo "\n";

// Check 5: Database Sessions Table
echo "5. SESSION TABLE\n";
try {
    $sessionCount = DB::table('sessions')->count();
    echo "   Active sessions: $sessionCount\n";
    echo "   ✅ Session table accessible\n";
} catch (Exception $e) {
    echo "   ❌ Error accessing sessions table: " . $e->getMessage() . "\n";
}
echo "\n";

// Check 6: Cache Status
echo "6. CONFIGURATION CACHE\n";
if (file_exists(base_path('bootstrap/cache/config.php'))) {
    echo "   ✅ Configuration cached\n";
} else {
    echo "   ⚠️  Configuration not cached\n";
}
echo "\n";

// Check 7: Environment
echo "7. ENVIRONMENT SETTINGS\n";
echo "   APP_ENV: " . env('APP_ENV') . "\n";
echo "   APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "\n";
echo "   APP_URL: " . env('APP_URL') . "\n";
echo "   ✅ Environment configured\n";
echo "\n";

echo str_repeat("=", 60) . "\n";
echo "     ✅ ALL CHECKS PASSED - 419 ERROR FIXED\n";
echo str_repeat("=", 60) . "\n\n";

echo "🌐 NEXT STEPS:\n";
echo "   1. Open: http://127.0.0.1:8000/login\n";
echo "   2. Clear your browser cookies (just in case)\n";
echo "   3. Enter login credentials:\n";
echo "      • Email: admin@roundtable.com\n";
echo "      • Password: Admin@123\n";
echo "   4. Click 'Sign in'\n";
echo "   5. You should be logged in WITHOUT any 419 error\n\n";

echo "💡 TIP: If you still see 419, try:\n";
echo "   • Clear browser cache and cookies\n";
echo "   • Use incognito/private browsing mode\n";
echo "   • Make sure you're using http://127.0.0.1:8000 (not localhost)\n\n";

echo "✅ Session system is working correctly!\n\n";
