<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cohort;
use App\Models\CohortMember;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        // Create Platform Admin
        $platformAdmin = User::create([
            'first_name' => 'Platform',
            'last_name' => 'Admin',
            'email' => 'admin@roundtable.com',
            'password' => Hash::make('Admin@123'),
            'role' => 'platform_admin',
            'status' => 'active',
            'kyc_status' => 'verified',
            'phone' => '0821234567',
            'email_verified_at' => now(),
        ]);

        // Create Cohort Admin (Jane)
        $cohortAdmin = User::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane@example.com',
            'password' => Hash::make('Password@123'),
            'role' => 'admin',
            'status' => 'active',
            'kyc_status' => 'verified',
            'phone' => '0827654321',
            'email_verified_at' => now(),
            'bank_name' => 'FNB',
            'account_number' => '1234567890',
            'account_holder_name' => 'Jane Smith',
        ]);

        // Create Regular Members
        $members = [];
        for ($i = 1; $i <= 10; $i++) {
            $members[] = User::create([
                'first_name' => "Member{$i}",
                'last_name' => "User{$i}",
                'email' => "member{$i}@example.com",
                'password' => Hash::make('Password@123'),
                'role' => 'member',
                'status' => 'active',
                'kyc_status' => 'verified',
                'phone' => '082' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'email_verified_at' => now(),
            ]);
        }

        // Create Test Cohorts (matching actual database schema)
        $cohort1 = Cohort::create([
            'cohort_id' => 'CID-' . now()->format('Ymd') . '-0001',
            'title' => 'Tech Startup Investment Pool',
            'description' => 'A cohort focused on investing in promising South African tech startups. We pool capital to make strategic investments in high-growth technology companies.',
            'admin_id' => $cohortAdmin->id,
            'cohort_class' => 'utilization',
            'asset_type' => 'business',
            'status' => 'operational',
            'minimum_viable_capital' => 50000000, // R500,000
            'ideal_target' => 100000000, // R1,000,000
            'hard_cap' => 150000000, // R1,500,000
            'current_capital' => 75000000, // R750,000
            'min_contribution' => 300000, // R3,000
            'max_contribution' => 10000000, // R100,000
            'funding_start_date' => now()->subMonths(2),
            'funding_end_date' => now()->subWeeks(2),
            'expected_exit_date' => now()->addYears(2),
            'duration_months' => 24,
            'setup_fee_percent' => 200, // 2%
            'management_fee_percent' => 300, // 3%
            'performance_fee_percent' => 1500, // 15%
            'risk_level' => 'moderate',
            'projected_annual_return' => 15.00,
            'performance_bond_amount' => 2500000, // R25,000
            'performance_bond_status' => 'posted',
            'member_count' => 8,
            'launched_at' => now()->subMonths(1),
        ]);

        $cohort2 = Cohort::create([
            'cohort_id' => 'CID-' . now()->format('Ymd') . '-0002',
            'title' => 'Property Development Fund',
            'description' => 'Real estate investment cohort targeting residential property development in Gauteng. Focus on affordable housing projects with strong ROI potential.',
            'admin_id' => $cohortAdmin->id,
            'cohort_class' => 'utilization',
            'asset_type' => 'real_estate',
            'status' => 'funding',
            'minimum_viable_capital' => 100000000, // R1,000,000
            'ideal_target' => 200000000, // R2,000,000
            'hard_cap' => 300000000, // R3,000,000
            'current_capital' => 45000000, // R450,000
            'min_contribution' => 300000, // R3,000
            'max_contribution' => 10000000, // R100,000
            'funding_start_date' => now()->subWeeks(1),
            'funding_end_date' => now()->addMonths(2),
            'expected_exit_date' => now()->addYears(3),
            'duration_months' => 36,
            'setup_fee_percent' => 200,
            'management_fee_percent' => 250,
            'performance_fee_percent' => 2000, // 20%
            'risk_level' => 'moderate',
            'projected_annual_return' => 18.00,
            'performance_bond_amount' => 5000000, // R50,000
            'performance_bond_status' => 'pending',
            'member_count' => 5,
        ]);

        // Add members to cohort 1
        $investments = [
            $members[0]->id => 10000000, // R100,000
            $members[1]->id => 10000000, // R100,000
            $members[2]->id => 8000000,  // R80,000
            $members[3]->id => 10000000, // R100,000
            $members[4]->id => 7500000,  // R75,000
            $members[5]->id => 10000000, // R100,000
            $members[6]->id => 9500000,  // R95,000
            $members[7]->id => 10000000, // R100,000
        ];

        foreach ($investments as $userId => $amount) {
            CohortMember::create([
                'cohort_id' => $cohort1->id,
                'user_id' => $userId,
                'capital_committed' => $amount,
                'capital_paid' => $amount,
                'ownership_percentage' => ($amount / $cohort1->current_capital) * 100,
                'shares' => $amount / 100000, // 1 share per R1,000
                'joined_at' => now()->subDays(rand(10, 30)),
                'commitment_date' => now()->subDays(rand(30, 60)),
                'payment_date' => now()->subDays(rand(10, 30)),
                'payment_status' => 'paid',
                'status' => 'active',
            ]);

            // Create capital contribution transaction
            Transaction::create([
                'transaction_id' => 'TXN-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'cohort_id' => $cohort1->id,
                'user_id' => $userId,
                'type' => 'capital_contribution',
                'amount' => $amount,
                'status' => 'completed',
                'approved_by' => $cohortAdmin->id,
                'approved_at' => now(),
                'completed_at' => now(),
            ]);
        }

        // Add members to cohort 2
        $investments2 = [
            $members[8]->id => 10000000, // R100,000
            $members[9]->id => 10000000, // R100,000
            $members[0]->id => 5000000,  // R50,000 (also in cohort1)
            $members[1]->id => 10000000, // R100,000
            $members[2]->id => 10000000, // R100,000
        ];

        foreach ($investments2 as $userId => $amount) {
            CohortMember::create([
                'cohort_id' => $cohort2->id,
                'user_id' => $userId,
                'capital_committed' => $amount,
                'capital_paid' => $amount,
                'ownership_percentage' => ($amount / $cohort2->current_capital) * 100,
                'shares' => $amount / 100000,
                'joined_at' => now()->subDays(rand(1, 15)),
                'commitment_date' => now()->subDays(rand(15, 30)),
                'payment_date' => now()->subDays(rand(1, 15)),
                'payment_status' => 'paid',
                'status' => 'active',
            ]);

            Transaction::create([
                'transaction_id' => 'TXN-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                'cohort_id' => $cohort2->id,
                'user_id' => $userId,
                'type' => 'capital_contribution',
                'amount' => $amount,
                'status' => 'completed',
                'approved_by' => $cohortAdmin->id,
                'approved_at' => now(),
                'completed_at' => now(),
            ]);
        }

        // Create some revenue transactions for cohort 1
        Transaction::create([
            'transaction_id' => 'TXN-' . now()->format('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'cohort_id' => $cohort1->id,
            'user_id' => $cohortAdmin->id,
            'type' => 'revenue_income',
            'amount' => 15000000, // R150,000 revenue
            'status' => 'completed',
            'description' => 'Q1 Investment Returns from TechCo Investment',
            'approved_by' => $cohortAdmin->id,
            'approved_at' => now(),
            'completed_at' => now(),
        ]);

        // Create notifications for members
        foreach ($members as $member) {
            Notification::create([
                'user_id' => $member->id,
                'notification_type' => 'welcome',
                'title' => 'Welcome to RoundTable!',
                'message' => 'Your account has been created. Start exploring investment opportunities.',
                'priority' => 'high',
                'is_read' => false,
            ]);
        }

        echo "✅ Test data created successfully!\n\n";
        echo "📊 CREATED:\n";
        echo "- 1 Platform Admin (admin@roundtable.com / Admin@123)\n";
        echo "- 1 Cohort Admin (jane@example.com / Password@123)\n";
        echo "- 10 Members (member1@example.com to member10@example.com / Password@123)\n";
        echo "- 2 Cohorts (Tech Startup Pool: Operational, Property Fund: Funding)\n";
        echo "- 13 Cohort Memberships\n";
        echo "- 14 Transactions\n";
        echo "- 10 Notifications\n\n";
        echo "🔗 Login at: http://127.0.0.1:8000/login\n";
    }
}
