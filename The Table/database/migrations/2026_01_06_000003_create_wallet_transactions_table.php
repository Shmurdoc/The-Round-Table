<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cohort_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('cohort_fund_id')->nullable()->constrained()->onDelete('set null');
            $table->string('transaction_id')->unique();
            $table->string('type'); // deposit, withdrawal, transfer_to_cohort, transfer_from_cohort, profit, refund
            $table->bigInteger('amount'); // In cents
            $table->bigInteger('balance_before');
            $table->bigInteger('balance_after');
            $table->string('status')->default('pending'); // pending, processing, completed, failed, cancelled
            $table->string('payment_method')->nullable(); // eft, card, payfast, manual
            $table->string('payment_reference')->nullable();
            $table->text('description')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['wallet_id', 'type']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
