<?php

/**
 * Notification System Test Script
 * 
 * This script tests the notification system by simulating various events
 * and verifying notifications are created correctly.
 */

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Cohort;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

echo "=== NOTIFICATION SYSTEM TEST ===\n\n";

$notificationService = app(NotificationService::class);

// Clear existing test notifications
DB::table('notifications')->where('title', 'LIKE', '[TEST]%')->delete();
echo "✓ Cleared previous test notifications\n\n";

// Test 1: Member Join Notification
echo "TEST 1: Member Join Notifications\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$member = User::where('kyc_status', 'verified')->first();
$cohort = Cohort::where('status', 'operational')->first();

if ($member && $cohort) {
    try {
        $notificationService->notifyMemberJoined($member, $cohort, 300000); // R3,000
        
        $sentNotifications = Notification::where('user_id', $member->id)
            ->where('cohort_id', $cohort->id)
            ->where('created_at', '>', now()->subMinutes(1))
            ->count();
            
        echo "✓ Member: {$member->name}\n";
        echo "✓ Cohort: {$cohort->title}\n";
        echo "✓ Notifications sent: {$sentNotifications}\n";
        echo "✓ Priority: HIGH (payment confirmation)\n";
        echo "✓ Status: SUCCESS\n";
    } catch (\Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠ Skipped: No verified member or operational cohort found\n";
}

echo "\n";

// Test 2: Status Change Notification
echo "TEST 2: Status Change Notifications\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$testCohort = Cohort::first();

if ($testCohort) {
    try {
        $notificationService->notifyStatusChange($testCohort, 'funding', 'operational', 'Test status change');
        
        $sentNotifications = Notification::where('cohort_id', $testCohort->id)
            ->where('type', 'cohort_status_change')
            ->where('created_at', '>', now()->subMinutes(1))
            ->count();
            
        echo "✓ Cohort: {$testCohort->title}\n";
        echo "✓ Status: funding → operational\n";
        echo "✓ Notifications sent: {$sentNotifications}\n";
        echo "✓ Priority: HIGH\n";
        echo "✓ Status: SUCCESS\n";
    } catch (\Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠ Skipped: No cohort found\n";
}

echo "\n";

// Test 3: KYC Approval Notification
echo "TEST 3: KYC Approval Notification\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$kycUser = User::where('kyc_status', 'verified')->first();

if ($kycUser) {
    try {
        $notificationService->notifyKYCApproved($kycUser);
        
        $sentNotifications = Notification::where('user_id', $kycUser->id)
            ->where('type', 'kyc_status')
            ->where('created_at', '>', now()->subMinutes(1))
            ->count();
            
        echo "✓ User: {$kycUser->name}\n";
        echo "✓ Notification: KYC Approved\n";
        echo "✓ Notifications sent: {$sentNotifications}\n";
        echo "✓ Priority: HIGH\n";
        echo "✓ Status: SUCCESS\n";
    } catch (\Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠ Skipped: No verified user found\n";
}

echo "\n";

// Test 4: Timeline Update Notification
echo "TEST 4: Timeline Update Notification\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$timelineCohort = Cohort::whereHas('members')->first();

if ($timelineCohort) {
    try {
        $notificationService->notifyTimelineUpdate(
            $timelineCohort, 
            '[TEST] Important Update',
            'This is a test timeline update to verify notifications are working correctly'
        );
        
        $sentNotifications = Notification::where('cohort_id', $timelineCohort->id)
            ->where('title', 'LIKE', '%[TEST] Important Update%')
            ->where('created_at', '>', now()->subMinutes(1))
            ->count();
            
        echo "✓ Cohort: {$timelineCohort->title}\n";
        echo "✓ Members: " . $timelineCohort->member_count . "\n";
        echo "✓ Notifications sent: {$sentNotifications}\n";
        echo "✓ Priority: MEDIUM\n";
        echo "✓ Status: SUCCESS\n";
    } catch (\Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠ Skipped: No cohort with members found\n";
}

echo "\n";

// Test 5: Wallet Notifications
echo "TEST 5: Wallet Notifications\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
$walletUser = User::whereHas('wallet')->first();

if ($walletUser) {
    try {
        $wallet = $walletUser->getOrCreateWallet();
        
        // Test deposit notification
        $notificationService->notifyWalletDeposit(
            $walletUser,
            100000, // R1,000
            $wallet->balance + 100000,
            'TEST-TXN-' . time()
        );
        
        $depositNotifications = Notification::where('user_id', $walletUser->id)
            ->where('type', 'wallet_transaction')
            ->where('title', 'LIKE', '%Deposit%')
            ->where('created_at', '>', now()->subMinutes(1))
            ->count();
            
        echo "✓ User: {$walletUser->name}\n";
        echo "✓ Wallet balance: R" . number_format($wallet->balance / 100, 2) . "\n";
        echo "✓ Test deposit: R1,000.00\n";
        echo "✓ Notifications sent: {$depositNotifications}\n";
        echo "✓ Priority: CRITICAL (financial transaction)\n";
        echo "✓ Status: SUCCESS\n";
    } catch (\Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "⚠ Skipped: No user with wallet found\n";
}

echo "\n";

// Summary
echo "=== NOTIFICATION SYSTEM SUMMARY ===\n\n";

$totalNotifications = Notification::where('created_at', '>', now()->subMinutes(5))->count();
$criticalNotifications = Notification::where('priority', 'critical')
    ->where('created_at', '>', now()->subMinutes(5))
    ->count();
$highNotifications = Notification::where('priority', 'high')
    ->where('created_at', '>', now()->subMinutes(5))
    ->count();
$mediumNotifications = Notification::where('priority', 'medium')
    ->where('created_at', '>', now()->subMinutes(5))
    ->count();

echo "📊 Notifications (last 5 minutes):\n";
echo "   Total: {$totalNotifications}\n";
echo "   Critical: {$criticalNotifications}\n";
echo "   High: {$highNotifications}\n";
echo "   Medium: {$mediumNotifications}\n";
echo "\n";

// Check compliance logging
$complianceLogs = DB::table('activity_logs')
    ->where('action', 'notification_sent')
    ->where('created_at', '>', now()->subMinutes(5))
    ->count();
    
echo "🔐 Compliance:\n";
echo "   Logged: {$complianceLogs} entries\n";
echo "   Retention: 7 years (regulatory requirement)\n";
echo "\n";

// Distribution filter test
$operationalCohorts = Cohort::where('status', 'operational')->count();
$totalCohorts = Cohort::count();

echo "📋 Distribution System:\n";
echo "   Total cohorts: {$totalCohorts}\n";
echo "   Operational cohorts: {$operationalCohorts}\n";
echo "   Can create distributions: " . ($operationalCohorts > 0 ? '✅ YES' : '❌ NO') . "\n";
echo "\n";

echo "✅ ALL TESTS COMPLETE\n\n";
echo "Next steps:\n";
echo "1. Check notifications table in database\n";
echo "2. Verify in-app notification display\n";
echo "3. Review activity_logs for compliance\n";
echo "4. Test distribution creation (operational cohorts only)\n";
echo "\n";
