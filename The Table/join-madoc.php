<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Cohort;
use App\Services\CohortService;
use Illuminate\Support\Facades\DB;

echo "=== MAKING MADOC JOIN COHORT 3 ===\n\n";

$madoc = User::find(1);
$cohort = Cohort::find(3);

if (!$madoc || !$cohort) {
    echo "Error: Madoc or Cohort not found\n";
    exit;
}

echo "User: {$madoc->first_name} {$madoc->last_name}\n";
echo "Cohort: {$cohort->title}\n";
echo "KYC Status: {$madoc->kyc_status}\n\n";

// Check if already a member
$isMember = $cohort->users()->where('user_id', $madoc->id)->exists();
if ($isMember) {
    echo "✗ Madoc is already a member of this cohort\n";
    exit;
}

// Get wallet
$wallet = $madoc->getOrCreateWallet();
echo "Wallet Balance: R" . number_format($wallet->balance / 100, 2) . "\n\n";

// Contribution amount (minimum)
$contributionAmount = $cohort->min_contribution; // R3,000 in cents
echo "Contributing: R" . number_format($contributionAmount / 100, 2) . "\n\n";

try {
    DB::beginTransaction();
    
    // Deduct from wallet
    $wallet->balance -= $contributionAmount;
    $wallet->save();
    
    echo "✓ Deducted from wallet\n";
    
    // Create wallet transaction
    $wallet->transactions()->create([
        'user_id' => $madoc->id,
        'transaction_id' => 'INV-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(8)),
        'type' => 'investment',
        'amount' => -$contributionAmount,
        'balance_before' => $wallet->balance + $contributionAmount,
        'balance_after' => $wallet->balance,
        'status' => 'completed',
        'description' => 'Investment in ' . $cohort->title,
    ]);
    
    echo "✓ Created wallet transaction\n";
    
    // Add to cohort_user pivot table
    $cohort->users()->attach($madoc->id, [
        'contribution_amount' => $contributionAmount,
        'joined_at' => now(),
        'status' => 'active',
    ]);
    
    echo "✓ Added to cohort_user pivot table\n";
    
    // Create CohortMember record
    \App\Models\CohortMember::create([
        'cohort_id' => $cohort->id,
        'user_id' => $madoc->id,
        'capital_committed' => $contributionAmount,
        'capital_paid' => $contributionAmount,
        'ownership_percentage' => 0, // Will be calculated when cohort activates
        'status' => 'active',
        'joined_at' => now(),
        'commitment_date' => now(),
        'payment_date' => now(),
    ]);
    
    echo "✓ Created CohortMember record\n";
    
    // Update cohort stats
    $cohort->increment('current_capital', $contributionAmount);
    $cohort->increment('member_count');
    
    echo "✓ Updated cohort stats\n";
    
    // Create transaction record
    \App\Models\Transaction::create([
        'user_id' => $madoc->id,
        'cohort_id' => $cohort->id,
        'type' => 'capital_contribution',
        'amount' => $contributionAmount,
        'status' => 'completed',
        'description' => "Contribution to {$cohort->title}",
        'transaction_id' => 'TXN-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 8)),
        'transaction_date' => now(),
    ]);
    
    echo "✓ Created transaction record\n";
    
    DB::commit();
    
    echo "\n✅ SUCCESS! Madoc has joined {$cohort->title}\n";
    echo "New Wallet Balance: R" . number_format($wallet->balance / 100, 2) . "\n";
    echo "Cohort Capital: R" . number_format($cohort->fresh()->current_capital / 100, 2) . "\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n✗ ERROR: " . $e->getMessage() . "\n";
}
