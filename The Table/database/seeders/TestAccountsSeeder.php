<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestAccountsSeeder extends Seeder
{
    public function run()
    {
        // 1. Platform Admin (Super Admin - manages entire platform)
        User::firstOrCreate(
            ['email' => 'platform.admin@roundtable.co.za'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make('Platform@2026'),
                'role' => 'platform_admin',
                'status' => 'active',
                'kyc_status' => 'verified',
                'phone' => '0800001111',
                'email_verified_at' => now(),
            ]
        );

        // 2. Cohort Admin (Can create and manage cohorts)
        User::firstOrCreate(
            ['email' => 'cohort.admin@roundtable.co.za'],
            [
                'first_name' => 'Cohort',
                'last_name' => 'Manager',
                'password' => Hash::make('Cohort@2026'),
                'role' => 'admin',
                'status' => 'active',
                'kyc_status' => 'verified',
                'phone' => '0800002222',
                'email_verified_at' => now(),
                'bank_name' => 'Capitec',
                'account_number' => '1234567890',
                'branch_code' => '470010',
            ]
        );

        // 3. Verified Member (Full access, KYC approved)
        User::firstOrCreate(
            ['email' => 'verified.member@roundtable.co.za'],
            [
                'first_name' => 'Verified',
                'last_name' => 'Partner',
                'password' => Hash::make('Member@2026'),
                'role' => 'member',
                'status' => 'active',
                'kyc_status' => 'verified',
                'phone' => '0800003333',
                'email_verified_at' => now(),
                'bank_name' => 'FNB',
                'account_number' => '9876543210',
                'branch_code' => '250655',
            ]
        );

        // 4. Pending KYC Member (Submitted KYC, awaiting approval)
        User::firstOrCreate(
            ['email' => 'pending.member@roundtable.co.za'],
            [
                'first_name' => 'Pending',
                'last_name' => 'User',
                'password' => Hash::make('Member@2026'),
                'role' => 'member',
                'status' => 'active',
                'kyc_status' => 'pending',
                'phone' => '0800004444',
                'email_verified_at' => now(),
            ]
        );

        // 5. New Member (Registered but no KYC submitted)
        User::firstOrCreate(
            ['email' => 'new.member@roundtable.co.za'],
            [
                'first_name' => 'New',
                'last_name' => 'User',
                'password' => Hash::make('Member@2026'),
                'role' => 'member',
                'status' => 'active',
                'kyc_status' => 'not_started',
                'phone' => '0800005555',
                'email_verified_at' => now(),
            ]
        );

        // 6. Rejected KYC Member (KYC was rejected, needs to resubmit)
        User::firstOrCreate(
            ['email' => 'rejected.member@roundtable.co.za'],
            [
                'first_name' => 'Rejected',
                'last_name' => 'User',
                'password' => Hash::make('Member@2026'),
                'role' => 'member',
                'status' => 'active',
                'kyc_status' => 'rejected',
                'kyc_rejection_reason' => 'ID document was unclear. Please resubmit with a clearer photo.',
                'phone' => '0800006666',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('All test accounts created successfully!');
    }
}
