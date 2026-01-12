<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique(); // TXN-YYYYMMDD-XXXX
            $table->foreignId('cohort_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            // Transaction Details
            $table->enum('type', [
                'capital_contribution',
                'capital_refund',
                'revenue_income',
                'operating_expense',
                'admin_fee',
                'distribution_to_member',
                'asset_purchase',
                'asset_sale',
                'insurance_claim',
                'loan_disbursement',
                'loan_repayment',
                'interest_earned',
                'tax_payment',
                'other'
            ]);
            
            $table->bigInteger('amount'); // In cents
            $table->string('currency', 3)->default('ZAR');
            
            // Direction
            $table->enum('direction', ['inflow', 'outflow']);
            
            // Categorization
            $table->string('category')->nullable(); // Maintenance, Legal, Marketing, etc.
            $table->text('description');
            $table->text('notes')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled', 'disputed'])->default('pending');
            
            // Account Information
            $table->string('from_account')->nullable();
            $table->string('to_account')->nullable();
            $table->string('payment_method')->nullable(); // Bank Transfer, Card, Cash, etc.
            $table->string('reference_number')->nullable(); // External reference
            
            // Supporting Documents
            $table->string('receipt_file')->nullable();
            $table->string('invoice_file')->nullable();
            $table->json('supporting_documents')->nullable();
            
            // Reconciliation
            $table->boolean('reconciled')->default(false);
            $table->timestamp('reconciled_at')->nullable();
            $table->foreignId('reconciled_by')->nullable()->constrained('users');
            
            // Approval (for large transactions)
            $table->boolean('requires_approval')->default(false);
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_notes')->nullable();
            
            // Blockchain Integration (optional)
            $table->string('blockchain_hash')->nullable();
            $table->string('blockchain_network')->nullable();
            
            // Metadata
            $table->timestamp('transaction_date');
            $table->string('ip_address')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('cohort_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
