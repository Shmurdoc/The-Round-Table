<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds crypto wallet fields for USDT withdrawals and distributions
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Crypto wallet details for receiving USDT payments
            $table->string('crypto_wallet_address')->nullable()->after('branch_code');
            $table->enum('crypto_network', ['TRC20', 'BEP20', 'ERC20'])->nullable()->after('crypto_wallet_address');
            $table->timestamp('crypto_wallet_verified_at')->nullable()->after('crypto_network');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'crypto_wallet_address',
                'crypto_network',
                'crypto_wallet_verified_at'
            ]);
        });
    }
};
