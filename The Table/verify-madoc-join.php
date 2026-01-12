<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Cohort;
use App\Models\CohortMember;
use Illuminate\Support\Facades\DB;

echo "=== VERIFICATION: MADOC'S JOIN STATUS ===\n\n";

$madoc = User::find(1);
$cohort = Cohort::find(3);

echo "User: {$madoc->name} (ID: {$madoc->id})\n";
echo "Cohort: {$cohort->title} (ID: {$cohort->id})\n\n";

// Check pivot table
$pivotRecord = DB::table('cohort_user')
    ->where('user_id', $madoc->id)
    ->where('cohort_id', $cohort->id)
    ->first();

echo "📋 Pivot Table (cohort_user):\n";
if ($pivotRecord) {
    echo "   ✓ Record exists\n";
    echo "   - Contribution: R" . number_format($pivotRecord->contribution_amount / 100, 2) . "\n";
    echo "   - Status: {$pivotRecord->status}\n";
    echo "   - Joined: {$pivotRecord->joined_at}\n";
} else {
    echo "   ✗ No record found\n";
}

echo "\n";

// Check cohort_members table
$memberRecord = CohortMember::where('user_id', $madoc->id)
    ->where('cohort_id', $cohort->id)
    ->first();

echo "👥 Cohort Members Table:\n";
if ($memberRecord) {
    echo "   ✓ Record exists\n";
    echo "   - Capital Committed: R" . number_format($memberRecord->capital_committed / 100, 2) . "\n";
    echo "   - Capital Paid: R" . number_format($memberRecord->capital_paid / 100, 2) . "\n";
    echo "   - Ownership: {$memberRecord->ownership_percentage}%\n";
    echo "   - Status: {$memberRecord->status}\n";
    echo "   - Joined: {$memberRecord->joined_at}\n";
} else {
    echo "   ✗ No record found\n";
}

echo "\n";

// Check wallet
$wallet = $madoc->getOrCreateWallet();
echo "💰 Wallet Status:\n";
echo "   Balance: R" . number_format($wallet->balance / 100, 2) . "\n";

echo "\n";

// Check cohort stats
$cohort = $cohort->fresh();
echo "🎯 Cohort Stats:\n";
echo "   Current Capital: R" . number_format($cohort->current_capital / 100, 2) . "\n";
echo "   Member Count: {$cohort->member_count}\n";
echo "   Status: {$cohort->status}\n";

echo "\n";

// Check transactions
$transactions = DB::table('transactions')
    ->where('user_id', $madoc->id)
    ->where('cohort_id', $cohort->id)
    ->get();

echo "💳 Transaction Records:\n";
if ($transactions->count() > 0) {
    foreach ($transactions as $txn) {
        echo "   ✓ {$txn->type}: R" . number_format($txn->amount / 100, 2) . " ({$txn->status})\n";
    }
} else {
    echo "   ✗ No transactions found\n";
}

echo "\n✅ VERIFICATION COMPLETE\n";
