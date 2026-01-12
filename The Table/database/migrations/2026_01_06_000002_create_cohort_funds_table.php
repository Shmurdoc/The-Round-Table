<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohort_funds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cohort_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->string('fund_id')->unique(); // Unique fund reference
            $table->bigInteger('principal_amount'); // Original amount invested (cents)
            $table->bigInteger('current_value'); // Current value with earnings (cents)
            $table->bigInteger('total_earnings')->default(0); // Accumulated earnings (cents)
            $table->decimal('profit_rate', 8, 4)->default(0); // Daily profit rate (%)
            $table->string('status')->default('pending'); // pending, active, locked, matured, withdrawn
            $table->boolean('is_locked')->default(false);
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('auto_lock_at')->nullable(); // Auto-lock after 24 hours
            $table->timestamp('maturity_date')->nullable(); // When funds can be withdrawn
            $table->timestamp('last_profit_applied_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'cohort_id']);
            $table->index(['status', 'is_locked']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohort_funds');
    }
};
