<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Cohort;
use App\Models\CohortMember;

echo "=== CHECKING MADOC STATUS ===\n\n";

// Check for Madoc user
$madoc = User::where('first_name', 'like', '%Madoc%')
    ->orWhere('last_name', 'like', '%Mhlongo%')
    ->orWhere('email', 'like', '%madoc%')
    ->first();

if ($madoc) {
    echo "✓ Madoc FOUND in users table:\n";
    echo "  ID: {$madoc->id}\n";
    echo "  Name: {$madoc->first_name} {$madoc->last_name}\n";
    echo "  Email: {$madoc->email}\n";
    echo "  KYC Status: {$madoc->kyc_status}\n";
    echo "  Role: {$madoc->role}\n\n";
    
    // Check if Madoc is in cohort_members
    $membership = CohortMember::where('user_id', $madoc->id)->with('cohort')->get();
    
    if ($membership->count() > 0) {
        echo "✓ Madoc IS in cohort_members table:\n";
        foreach ($membership as $m) {
            echo "  Cohort: {$m->cohort->title} (ID: {$m->cohort_id})\n";
            echo "  Capital Paid: R" . number_format($m->capital_paid / 100, 2) . "\n";
            echo "  Status: {$m->status}\n";
            echo "  Joined: {$m->joined_at}\n\n";
        }
    } else {
        echo "✗ Madoc NOT in cohort_members table\n\n";
    }
    
    // Check pivot table
    $pivotRecords = \DB::table('cohort_user')->where('user_id', $madoc->id)->get();
    if ($pivotRecords->count() > 0) {
        echo "✓ Madoc IS in cohort_user pivot table:\n";
        foreach ($pivotRecords as $p) {
            $cohort = Cohort::find($p->cohort_id);
            echo "  Cohort: {$cohort->title} (ID: {$p->cohort_id})\n";
            echo "  Contribution: R" . number_format($p->contribution_amount / 100, 2) . "\n";
            echo "  Status: {$p->status}\n";
            echo "  Joined: {$p->joined_at}\n\n";
        }
    } else {
        echo "✗ Madoc NOT in cohort_user pivot table\n\n";
    }
    
} else {
    echo "✗ Madoc NOT FOUND in users table\n";
    echo "  Please create/register Madoc Mhlongo account first\n\n";
}

// Show all cohorts
echo "=== ALL COHORTS ===\n";
$cohorts = Cohort::all();
foreach ($cohorts as $cohort) {
    echo "ID: {$cohort->id} - {$cohort->title} (Status: {$cohort->status})\n";
}

echo "\n=== ALL MEMBERS IN COHORT_MEMBERS ===\n";
$members = CohortMember::with('user', 'cohort')->get();
foreach ($members as $member) {
    echo "User: {$member->user->first_name} {$member->user->last_name} → Cohort: {$member->cohort->title}\n";
}

if ($members->count() === 0) {
    echo "No members found\n";
}
