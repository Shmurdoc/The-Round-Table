<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('currency', 10); // USDT, BTC, ETH, etc.
            $table->string('network', 20); // TRC20, ERC20, BSC, etc.
            $table->string('address')->unique();
            $table->decimal('balance', 20, 8)->default(0);
            $table->enum('status', ['active', 'suspended', 'closed'])->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'currency', 'network']);
            $table->index(['currency', 'network']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('currency', 10)->nullable()->after('amount');
            $table->decimal('crypto_amount', 20, 8)->nullable()->after('currency');
            $table->string('crypto_network', 20)->nullable()->after('crypto_amount');
            $table->string('crypto_tx_hash', 100)->nullable()->after('reference');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->boolean('auto_convert_crypto')->default(true)->after('balance');
            $table->integer('crypto_tier')->default(1)->after('tier');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['auto_convert_crypto', 'crypto_tier']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['currency', 'crypto_amount', 'crypto_network', 'crypto_tx_hash']);
        });

        Schema::dropIfExists('crypto_wallets');
    }
};
