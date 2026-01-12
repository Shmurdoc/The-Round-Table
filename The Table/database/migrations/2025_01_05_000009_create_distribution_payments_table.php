<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distribution_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_id')->constrained()->onDelete('cascade');
            $table->foreignId('cohort_member_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            
            // Payment Details
            $table->bigInteger('amount'); // In cents
            $table->string('currency', 3)->default('ZAR');
            
            // Banking
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_holder')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_reference')->nullable();
            $table->text('failure_reason')->nullable();
            
            // Timing
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Reconciliation
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
            
            $table->index('distribution_id');
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distribution_payments');
    }
};
