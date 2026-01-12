<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['member', 'admin', 'platform_admin'])->default('member');
            $table->enum('status', ['pending', 'active', 'suspended', 'banned'])->default('pending');
            
            // KYC/AML Fields
            $table->string('id_number')->nullable();
            $table->string('id_document')->nullable(); // File path
            $table->string('proof_of_address')->nullable(); // File path
            $table->enum('kyc_status', ['not_started', 'pending', 'verified', 'rejected'])->default('not_started');
            $table->text('kyc_notes')->nullable();
            $table->timestamp('kyc_verified_at')->nullable();
            
            // Banking Details
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('bank_code')->nullable();
            
            // Profile
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();
            $table->string('country')->default('ZA');
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();
            
            // Security
            $table->boolean('two_factor_enabled')->default(false);
            $table->string('two_factor_secret')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('email');
            $table->index('role');
            $table->index('status');
            $table->index('kyc_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
