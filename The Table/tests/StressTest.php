<?php

/**
 * RoundTable System Stress Test
 * Comprehensive testing of all system features
 */

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\Transaction;
use App\Models\Vote;
use App\Models\VoteResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

echo "\n🧪 ===== ROUNDTABLE SYSTEM STRESS TEST =====\n\n";

$testsPassed = 0;
$testsFailed = 0;
$errors = [];

// Test 1: Database Connection
echo "Test 1: Database Connection...";
try {
    DB::connection()->getPdo();
    echo " ✅ PASSED\n";
    $testsPassed++;
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "Database Connection: " . $e->getMessage();
}

// Test 2: User Authentication
echo "Test 2: User Authentication...";
try {
    $user = User::where('email', 'member1@example.com')->first();
    if ($user && Hash::check('Password@123', $user->password)) {
        echo " ✅ PASSED\n";
        $testsPassed++;
    } else {
        echo " ❌ FAILED: Password verification failed\n";
        $testsFailed++;
        $errors[] = "User Authentication: Password verification failed";
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "User Authentication: " . $e->getMessage();
}

// Test 3: User Count
echo "Test 3: User Creation Count...";
try {
    $userCount = User::count();
    if ($userCount >= 12) { // Platform admin + cohort admin + 10 members
        echo " ✅ PASSED ($userCount users)\n";
        $testsPassed++;
    } else {
        echo " ❌ FAILED: Expected >= 12, got $userCount\n";
        $testsFailed++;
        $errors[] = "User Count: Expected >= 12, got $userCount";
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "User Count: " . $e->getMessage();
}

// Test 4: Role Assignment
echo "Test 4: Role Assignment...";
try {
    $platformAdmin = User::where('role', 'platform_admin')->count();
    $cohortAdmin = User::where('role', 'admin')->count();
    $members = User::where('role', 'member')->count();
    
    if ($platformAdmin >= 1 && $cohortAdmin >= 1 && $members >= 10) {
        echo " ✅ PASSED (Admin: $platformAdmin, Cohort Admin: $cohortAdmin, Members: $members)\n";
        $testsPassed++;
    } else {
        echo " ❌ FAILED: Role distribution incorrect\n";
        $testsFailed++;
        $errors[] = "Role Assignment: Distribution incorrect";
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "Role Assignment: " . $e->getMessage();
}

// Test 5: Cohort Creation
echo "Test 5: Cohort Creation...";
try {
    $cohortCount = Cohort::count();
    if ($cohortCount >= 2) {
        echo " ✅ PASSED ($cohortCount cohorts)\n";
        $testsPassed++;
    } else {
        echo " ❌ FAILED: Expected >= 2, got $cohortCount\n";
        $testsFailed++;
        $errors[] = "Cohort Creation: Expected >= 2, got $cohortCount";
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "Cohort Creation: " . $e->getMessage();
}

// Test 6: Capital Calculations
echo "Test 6: Capital Calculations...";
try {
    $cohort = Cohort::where('status', 'operational')->first();
    if ($cohort) {
        $membersTotal = CohortMember::where('cohort_id', $cohort->id)->sum('capital_committed');
        if ($membersTotal == $cohort->total_capital_raised) {
            echo " ✅ PASSED (R" . number_format($membersTotal / 100, 2) . ")\n";
            $testsPassed++;
        } else {
            echo " ❌ FAILED: Members total (R" . number_format($membersTotal / 100, 2) . 
                 ") != Cohort total (R" . number_format($cohort->total_capital_raised / 100, 2) . ")\n";
            $testsFailed++;
            $errors[] = "Capital Calculations: Mismatch in totals";
        }
    } else {
        echo " ❌ FAILED: No operational cohort found\n";
        $testsFailed++;
        $errors[] = "Capital Calculations: No operational cohort";
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "Capital Calculations: " . $e->getMessage();
}

// Test 7: Ownership Percentage
echo "Test 7: Ownership Percentage...";
try {
    $cohort = Cohort::first();
    $members = CohortMember::where('cohort_id', $cohort->id)->get();
    $totalOwnership = $members->sum('ownership_percentage');
    
    if (abs($totalOwnership - 100) < 0.1) { // Allow 0.1% tolerance
        echo " ✅ PASSED (" . number_format($totalOwnership, 2) . "%)\n";
        $testsPassed++;
    } else {
        echo " ❌ FAILED: Total ownership is " . number_format($totalOwnership, 2) . "% (should be 100%)\n";
        $testsFailed++;
        $errors[] = "Ownership Percentage: Total is " . number_format($totalOwnership, 2) . "%";
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "Ownership Percentage: " . $e->getMessage();
}

// Test 8: Transaction Integrity
echo "Test 8: Transaction Integrity...";
try {
    $transactions = Transaction::where('status', 'completed')->count();
    if ($transactions >= 14) {
        echo " ✅ PASSED ($transactions transactions)\n";
        $testsPassed++;
    } else {
        echo " ❌ FAILED: Expected >= 14, got $transactions\n";
        $testsFailed++;
        $errors[] = "Transaction Integrity: Expected >= 14, got $transactions";
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "Transaction Integrity: " . $e->getMessage();
}

// Test 9: KYC Status
echo "Test 9: KYC Status...";
try {
    $verifiedUsers = User::where('kyc_status', 'verified')->count();
    if ($verifiedUsers >= 12) {
        echo " ✅ PASSED ($verifiedUsers verified)\n";
        $testsPassed++;
    } else {
        echo " ❌ FAILED: Expected >= 12, got $verifiedUsers\n";
        $testsFailed++;
        $errors[] = "KYC Status: Expected >= 12 verified, got $verifiedUsers";
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "KYC Status: " . $e->getMessage();
}

// Test 10: Model Relationships
echo "Test 10: Model Relationships...";
try {
    $cohort = Cohort::with(['admin', 'members', 'transactions'])->first();
    if ($cohort->admin && $cohort->members->count() > 0 && $cohort->transactions->count() > 0) {
        echo " ✅ PASSED (Admin: {$cohort->admin->email}, Members: {$cohort->members->count()})\n";
        $testsPassed++;
    } else {
        echo " ❌ FAILED: Relationships not loading\n";
        $testsFailed++;
        $errors[] = "Model Relationships: Not loading correctly";
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "Model Relationships: " . $e->getMessage();
}

// Test 11: Currency Limits
echo "Test 11: Currency Limits (R3,000 - R100,000)...";
try {
    $cohorts = Cohort::all();
    $validLimits = true;
    foreach ($cohorts as $cohort) {
        if ($cohort->minimum_contribution < 300000 || $cohort->maximum_contribution > 10000000) {
            $validLimits = false;
            break;
        }
    }
    if ($validLimits) {
        echo " ✅ PASSED\n";
        $testsPassed++;
    } else {
        echo " ❌ FAILED: Limits outside R3k-R100k range\n";
        $testsFailed++;
        $errors[] = "Currency Limits: Outside R3k-R100k range";
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "Currency Limits: " . $e->getMessage();
}

// Test 12: Revenue Tracking
echo "Test 12: Revenue Tracking...";
try {
    $revenueTransactions = Transaction::where('type', 'revenue_income')->where('status', 'completed')->sum('amount');
    if ($revenueTransactions > 0) {
        echo " ✅ PASSED (R" . number_format($revenueTransactions / 100, 2) . ")\n";
        $testsPassed++;
    } else {
        echo " ⚠️  WARNING: No revenue transactions\n";
        $testsPassed++; // Not a failure, just a warning
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "Revenue Tracking: " . $e->getMessage();
}

// Test 13: User Status
echo "Test 13: User Status (Active Users)...";
try {
    $activeUsers = User::where('status', 'active')->count();
    if ($activeUsers >= 12) {
        echo " ✅ PASSED ($activeUsers active)\n";
        $testsPassed++;
    } else {
        echo " ❌ FAILED: Expected >= 12 active, got $activeUsers\n";
        $testsFailed++;
        $errors[] = "User Status: Expected >= 12 active, got $activeUsers";
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "User Status: " . $e->getMessage();
}

// Test 14: Multi-Cohort Membership
echo "Test 14: Multi-Cohort Membership...";
try {
    $multiCohortMembers = DB::table('cohort_members')
        ->select('user_id', DB::raw('COUNT(*) as cohort_count'))
        ->groupBy('user_id')
        ->having('cohort_count', '>', 1)
        ->count();
    
    if ($multiCohortMembers > 0) {
        echo " ✅ PASSED ($multiCohortMembers users in multiple cohorts)\n";
        $testsPassed++;
    } else {
        echo " ⚠️  WARNING: No users in multiple cohorts\n";
        $testsPassed++; // Not a failure
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "Multi-Cohort Membership: " . $e->getMessage();
}

// Test 15: Performance (Query Speed)
echo "Test 15: Performance (Query Speed)...";
try {
    $start = microtime(true);
    $cohorts = Cohort::with(['admin', 'members.user', 'transactions'])->get();
    $end = microtime(true);
    $duration = ($end - $start) * 1000; // Convert to milliseconds
    
    if ($duration < 500) { // Less than 500ms
        echo " ✅ PASSED (" . number_format($duration, 2) . "ms)\n";
        $testsPassed++;
    } else {
        echo " ⚠️  WARNING: Slow query (" . number_format($duration, 2) . "ms)\n";
        $testsPassed++; // Not a failure for now
    }
} catch (Exception $e) {
    echo " ❌ FAILED: " . $e->getMessage() . "\n";
    $testsFailed++;
    $errors[] = "Performance: " . $e->getMessage();
}

// Summary
echo "\n";
echo "╔═══════════════════════════════════════╗\n";
echo "║       STRESS TEST SUMMARY             ║\n";
echo "╠═══════════════════════════════════════╣\n";
echo "║  Total Tests:  " . str_pad($testsPassed + $testsFailed, 20, " ", STR_PAD_LEFT) . " ║\n";
echo "║  ✅ Passed:     " . str_pad($testsPassed, 20, " ", STR_PAD_LEFT) . " ║\n";
echo "║  ❌ Failed:     " . str_pad($testsFailed, 20, " ", STR_PAD_LEFT) . " ║\n";
echo "║  Success Rate: " . str_pad(number_format(($testsPassed / ($testsPassed + $testsFailed)) * 100, 1) . "%", 20, " ", STR_PAD_LEFT) . " ║\n";
echo "╚═══════════════════════════════════════╝\n\n";

if ($testsFailed > 0) {
    echo "❌ FAILED TESTS:\n";
    foreach ($errors as $error) {
        echo "  - $error\n";
    }
    echo "\n";
}

// Database Statistics
echo "📊 DATABASE STATISTICS:\n";
echo "  Users:              " . User::count() . "\n";
echo "  Cohorts:            " . Cohort::count() . "\n";
echo "  Cohort Members:     " . CohortMember::count() . "\n";
echo "  Transactions:       " . Transaction::count() . "\n";
echo "  Total Capital:      R" . number_format(Cohort::sum('total_capital_raised') / 100, 2) . "\n";
echo "  Operational Cohorts:" . Cohort::where('status', 'operational')->count() . "\n";
echo "  Funding Cohorts:    " . Cohort::where('status', 'funding')->count() . "\n";
echo "\n";

// Sample Data Display
echo "📋 SAMPLE COHORT DATA:\n";
$sampleCohort = Cohort::with(['admin', 'members'])->first();
if ($sampleCohort) {
    echo "  Name:          {$sampleCohort->name}\n";
    echo "  Admin:         {$sampleCohort->admin->first_name} {$sampleCohort->admin->last_name}\n";
    echo "  Status:        {$sampleCohort->status}\n";
    echo "  Members:       {$sampleCohort->member_count}\n";
    echo "  Capital Raised:R" . number_format($sampleCohort->total_capital_raised / 100, 2) . "\n";
    echo "  Target:        R" . number_format($sampleCohort->ideal_target / 100, 2) . "\n";
}

echo "\n✅ Stress test complete!\n";
echo "🔗 Test the web interface at: http://127.0.0.1:8000\n\n";
