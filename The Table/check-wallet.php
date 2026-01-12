<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Cohort;

echo "=== CHECKING MADOC'S WALLET ===\n\n";

$madoc = User::find(1);
if (!$madoc) {
    echo "Madoc not found\n";
    exit;
}

$wallet = $madoc->getOrCreateWallet();

echo "Madoc's Wallet:\n";
echo "  Balance: R" . number_format($wallet->balance / 100, 2) . "\n";
echo "  User ID: {$wallet->user_id}\n\n";

// Check cohort 3
$cohort = Cohort::find(3);
if ($cohort) {
    echo "Cohort 'test1' (ID: 3):\n";
    echo "  Min Contribution: R" . number_format($cohort->min_contribution / 100, 2) . "\n";
    echo "  Max Contribution: R" . number_format($cohort->max_contribution / 100, 2) . "\n";
    echo "  Current Capital: R" . number_format($cohort->current_capital / 100, 2) . "\n";
    echo "  Hard Cap: R" . number_format($cohort->hard_cap / 100, 2) . "\n";
    echo "  Available Space: R" . number_format(($cohort->hard_cap - $cohort->current_capital) / 100, 2) . "\n\n";
    
    // Check if Madoc can afford minimum
    if ($wallet->balance >= $cohort->min_contribution) {
        echo "✓ Madoc CAN afford minimum contribution\n";
    } else {
        echo "✗ Madoc CANNOT afford minimum contribution\n";
        echo "  Needs: R" . number_format(($cohort->min_contribution - $wallet->balance) / 100, 2) . " more\n";
    }
}
