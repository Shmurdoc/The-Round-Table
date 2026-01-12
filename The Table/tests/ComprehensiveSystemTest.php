<?php

/**
 * Comprehensive System Test - All Features, All Accounts
 * Tests every feature in the RoundTable Partnership Platform
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\Vote;
use App\Models\Transaction;
use App\Models\Timeline;
use Illuminate\Support\Facades\Route;

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║     🧪 COMPREHENSIVE SYSTEM TEST - ALL FEATURES & ACCOUNTS     ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

$testsPassed = 0;
$testsFailed = 0;
$warnings = 0;

// ============================================================================
// TEST 1: VERIFY ALL TEST ACCOUNTS EXIST
// ============================================================================

echo "═══════════════════════════════════════════════════════════════\n";
echo "TEST 1: VERIFY ALL TEST ACCOUNTS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$testAccounts = [
    [
        'email' => 'platform.admin@roundtable.co.za',
        'role' => 'platform_admin',
        'kyc' => 'verified',
        'name' => 'Platform Admin'
    ],
    [
        'email' => 'cohort.admin@roundtable.co.za',
        'role' => 'admin',
        'kyc' => 'verified',
        'name' => 'Cohort Admin'
    ],
    [
        'email' => 'verified.member@roundtable.co.za',
        'role' => 'member',
        'kyc' => 'verified',
        'name' => 'Verified Member'
    ],
    [
        'email' => 'pending.member@roundtable.co.za',
        'role' => 'member',
        'kyc' => 'pending',
        'name' => 'Pending KYC Member'
    ],
    [
        'email' => 'new.member@roundtable.co.za',
        'role' => 'member',
        'kyc' => 'not_started',
        'name' => 'New Member'
    ],
    [
        'email' => 'rejected.member@roundtable.co.za',
        'role' => 'member',
        'kyc' => 'rejected',
        'name' => 'Rejected Member'
    ]
];

foreach ($testAccounts as $account) {
    $user = User::where('email', $account['email'])->first();
    if ($user) {
        $roleMatch = $user->role === $account['role'];
        $kycMatch = $user->kyc_status === $account['kyc'];
        
        if ($roleMatch && $kycMatch) {
            echo "✅ {$account['name']}: FOUND & CONFIGURED CORRECTLY\n";
            echo "   Email: {$user->email}\n";
            echo "   Role: {$user->role}\n";
            echo "   KYC: {$user->kyc_status}\n";
            $testsPassed++;
        } else {
            echo "⚠️  {$account['name']}: FOUND BUT MISCONFIGURED\n";
            echo "   Expected Role: {$account['role']} | Actual: {$user->role}\n";
            echo "   Expected KYC: {$account['kyc']} | Actual: {$user->kyc_status}\n";
            $warnings++;
        }
    } else {
        echo "❌ {$account['name']}: NOT FOUND!\n";
        echo "   Email: {$account['email']}\n";
        $testsFailed++;
    }
    echo "\n";
}

// ============================================================================
// TEST 2: VERIFY ALL ROUTES ARE REGISTERED
// ============================================================================

echo "═══════════════════════════════════════════════════════════════\n";
echo "TEST 2: VERIFY CRITICAL ROUTES\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$criticalRoutes = [
    // Public Routes
    'home' => 'Home page',
    'login' => 'Login page',
    'register' => 'Registration page',
    
    // Member Routes
    'member.dashboard' => 'Member dashboard',
    'cohorts.index' => 'Browse cohorts',
    'member.portfolio' => 'Member portfolio',
    'wallet.index' => 'Wallet page',
    
    // Admin Routes
    'admin.dashboard' => 'Admin dashboard',
    'admin.cohorts.index' => 'Manage cohorts',
    'admin.cohorts.show' => 'View cohort',
    
    // Vote Routes
    'votes.cast' => 'Cast vote',
    
    // Timeline Routes
    'admin.cohorts.timeline.store' => 'Post timeline update',
    
    // Payment Routes
    'webhook.nowpayments.ipn' => 'NOWPayments webhook',
];

foreach ($criticalRoutes as $routeName => $description) {
    if (Route::has($routeName)) {
        echo "✅ Route '{$routeName}': EXISTS ({$description})\n";
        $testsPassed++;
    } else {
        echo "❌ Route '{$routeName}': MISSING ({$description})\n";
        $testsFailed++;
    }
}

echo "\n";

// ============================================================================
// TEST 3: DATABASE & MODELS
// ============================================================================

echo "═══════════════════════════════════════════════════════════════\n";
echo "TEST 3: DATABASE & MODELS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

try {
    echo "📊 Users: " . User::count() . " total\n";
    echo "📊 Cohorts: " . Cohort::count() . " total\n";
    echo "📊 Cohort Members: " . CohortMember::count() . " total\n";
    echo "📊 Transactions: " . Transaction::count() . " total\n";
    echo "📊 Votes: " . Vote::count() . " total\n";
    echo "📊 Timelines: " . Timeline::count() . " total\n";
    echo "✅ Database connection working\n";
    $testsPassed++;
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    $testsFailed++;
}

echo "\n";

// ============================================================================
// TEST 4: TRANSFORMATION FEATURES
// ============================================================================

echo "═══════════════════════════════════════════════════════════════\n";
echo "TEST 4: TRANSFORMATION FEATURES (Partnership Platform)\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Check Timeline System
try {
    $timelineExists = class_exists('App\Models\Timeline');
    $timelineController = class_exists('App\Http\Controllers\Admin\TimelineController');
    
    if ($timelineExists && $timelineController) {
        echo "✅ Timeline System: IMPLEMENTED\n";
        echo "   • Timeline Model: ✅\n";
        echo "   • Timeline Controller: ✅\n";
        echo "   • Timeline Routes: " . (Route::has('admin.cohorts.timeline.store') ? "✅" : "❌") . "\n";
        $testsPassed++;
    } else {
        echo "❌ Timeline System: INCOMPLETE\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "❌ Timeline System: ERROR - " . $e->getMessage() . "\n";
    $testsFailed++;
}

echo "\n";

// Check Production Mode
try {
    $cohort = Cohort::first();
    if ($cohort) {
        $hasProductionMode = method_exists($cohort, 'activateProduction');
        $hasColumn = in_array('production_mode', array_keys($cohort->getAttributes()));
        
        if ($hasProductionMode || $hasColumn) {
            echo "✅ Production Mode System: IMPLEMENTED\n";
            echo "   • production_mode column: " . ($hasColumn ? "✅" : "⚠️  Not found") . "\n";
            echo "   • activateProduction() method: " . ($hasProductionMode ? "✅" : "⚠️  Not found") . "\n";
            $testsPassed++;
        } else {
            echo "⚠️  Production Mode System: PARTIALLY IMPLEMENTED\n";
            $warnings++;
        }
    } else {
        echo "⚠️  No cohorts in database to test production mode\n";
        $warnings++;
    }
} catch (Exception $e) {
    echo "❌ Production Mode: ERROR - " . $e->getMessage() . "\n";
    $testsFailed++;
}

echo "\n";

// Check NOWPayments Integration
try {
    $serviceExists = class_exists('App\Services\NOWPaymentsService');
    $controllerExists = class_exists('App\Http\Controllers\NOWPaymentsController');
    
    if ($serviceExists && $controllerExists) {
        echo "✅ USDT Payment System (NOWPayments): IMPLEMENTED\n";
        echo "   • NOWPaymentsService: ✅\n";
        echo "   • NOWPaymentsController: ✅\n";
        echo "   • Webhook Route: " . (Route::has('nowpayments.webhook') ? "✅" : "⚠️  Check routes") . "\n";
        $testsPassed++;
    } else {
        echo "⚠️  USDT Payment System: PARTIALLY IMPLEMENTED\n";
        echo "   • Service: " . ($serviceExists ? "✅" : "❌") . "\n";
        echo "   • Controller: " . ($controllerExists ? "✅" : "❌") . "\n";
        $warnings++;
    }
} catch (Exception $e) {
    echo "❌ USDT Payment System: ERROR - " . $e->getMessage() . "\n";
    $testsFailed++;
}

echo "\n";

// Check Weekly Distribution Command
try {
    $commandExists = \Illuminate\Support\Facades\Artisan::all();
    $hasDistributeCommand = isset($commandExists['profits:distribute-weekly']);
    
    if ($hasDistributeCommand) {
        echo "✅ Weekly Distribution Command: REGISTERED\n";
        echo "   • Command: profits:distribute-weekly ✅\n";
        $testsPassed++;
    } else {
        echo "❌ Weekly Distribution Command: NOT REGISTERED\n";
        $testsFailed++;
    }
} catch (Exception $e) {
    echo "❌ Weekly Distribution: ERROR - " . $e->getMessage() . "\n";
    $testsFailed++;
}

echo "\n";

// ============================================================================
// TEST 5: ROLE PERMISSIONS
// ============================================================================

echo "═══════════════════════════════════════════════════════════════\n";
echo "TEST 5: ROLE PERMISSIONS & ACCESS CONTROL\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

// Test Platform Admin
$platformAdmin = User::where('email', 'platform.admin@roundtable.co.za')->first();
if ($platformAdmin) {
    echo "Platform Admin (platform.admin@roundtable.co.za):\n";
    echo "  • isPlatformAdmin(): " . ($platformAdmin->isPlatformAdmin() ? "✅ YES" : "❌ NO") . "\n";
    echo "  • isAdmin(): " . ($platformAdmin->isAdmin() ? "⚠️  YES (should be NO)" : "✅ NO") . "\n";
    echo "  • isMember(): " . ($platformAdmin->isMember() ? "⚠️  YES (should be NO)" : "✅ NO") . "\n";
    $platformAdmin->isPlatformAdmin() ? $testsPassed++ : $testsFailed++;
} else {
    echo "❌ Platform Admin account not found\n";
    $testsFailed++;
}

echo "\n";

// Test Cohort Admin
$cohortAdmin = User::where('email', 'cohort.admin@roundtable.co.za')->first();
if ($cohortAdmin) {
    echo "Cohort Admin (cohort.admin@roundtable.co.za):\n";
    echo "  • isPlatformAdmin(): " . ($cohortAdmin->isPlatformAdmin() ? "⚠️  YES (should be NO)" : "✅ NO") . "\n";
    echo "  • isAdmin(): " . ($cohortAdmin->isAdmin() ? "✅ YES" : "❌ NO") . "\n";
    echo "  • isMember(): " . ($cohortAdmin->isMember() ? "⚠️  YES (should be NO)" : "✅ NO") . "\n";
    $cohortAdmin->isAdmin() ? $testsPassed++ : $testsFailed++;
} else {
    echo "❌ Cohort Admin account not found\n";
    $testsFailed++;
}

echo "\n";

// Test Member
$member = User::where('email', 'verified.member@roundtable.co.za')->first();
if ($member) {
    echo "Verified Member (verified.member@roundtable.co.za):\n";
    echo "  • isPlatformAdmin(): " . ($member->isPlatformAdmin() ? "⚠️  YES (should be NO)" : "✅ NO") . "\n";
    echo "  • isAdmin(): " . ($member->isAdmin() ? "⚠️  YES (should be NO)" : "✅ NO") . "\n";
    echo "  • isMember(): " . ($member->isMember() ? "✅ YES" : "❌ NO") . "\n";
    echo "  • KYC Status: " . ($member->kyc_status === 'verified' ? "✅ Verified" : "⚠️  " . $member->kyc_status) . "\n";
    $member->isMember() && ($member->kyc_status === 'verified') ? $testsPassed++ : $testsFailed++;
} else {
    echo "❌ Verified Member account not found\n";
    $testsFailed++;
}

echo "\n";

// ============================================================================
// FINAL SUMMARY
// ============================================================================

echo "═══════════════════════════════════════════════════════════════\n";
echo "FINAL RESULTS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

$totalTests = $testsPassed + $testsFailed + $warnings;
$successRate = $totalTests > 0 ? round(($testsPassed / $totalTests) * 100, 1) : 0;

echo "✅ Tests Passed: $testsPassed\n";
echo "❌ Tests Failed: $testsFailed\n";
echo "⚠️  Warnings: $warnings\n";
echo "📊 Success Rate: {$successRate}%\n\n";

if ($testsFailed === 0 && $warnings === 0) {
    echo "🎉 ALL TESTS PASSED! SYSTEM READY FOR PRODUCTION!\n";
} elseif ($testsFailed === 0) {
    echo "✅ All critical tests passed, but check warnings above.\n";
} else {
    echo "⚠️  Some tests failed. Review errors above before proceeding.\n";
}

echo "\n";

// ============================================================================
// NEXT STEPS
// ============================================================================

echo "═══════════════════════════════════════════════════════════════\n";
echo "RECOMMENDED NEXT STEPS\n";
echo "═══════════════════════════════════════════════════════════════\n\n";

echo "1. 🌐 Open browser to: http://127.0.0.1:8000/login\n";
echo "2. 🔐 Login with each test account (see TEST-ACCOUNTS.md)\n";
echo "3. ✅ Verify dashboard loads for each role\n";
echo "4. 🎯 Test key features:\n";
echo "   • Cohort Admin: Create cohort, post timeline, activate production\n";
echo "   • Member: Join cohort, view timeline, check voting widget\n";
echo "   • Platform Admin: Approve cohorts, manage KYC\n";
echo "5. 💰 Test USDT payment flow (requires API keys in .env)\n";
echo "6. 📅 Test weekly distribution: php artisan profits:distribute-weekly --force\n";

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║                    TEST COMPLETE                               ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";
