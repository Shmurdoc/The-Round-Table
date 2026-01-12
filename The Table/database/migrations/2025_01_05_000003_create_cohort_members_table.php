<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohort_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            
            // Capital Contribution
            $table->bigInteger('capital_committed'); // Amount in cents
            $table->bigInteger('capital_paid')->default(0); // Actually transferred
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'partially_refunded'])->default('pending');
            
            // Ownership
            $table->decimal('ownership_percentage', 10, 8); // e.g., 5.12345678%
            $table->integer('shares')->default(0); // Virtual shares for voting
            
            // Status
            $table->enum('status', ['committed', 'active', 'exited', 'removed'])->default('committed');
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('commitment_date');
            $table->timestamp('payment_date')->nullable();
            
            // Distribution Tracking
            $table->bigInteger('total_distributions_received')->default(0);
            $table->bigInteger('final_distribution')->default(0);
            $table->decimal('actual_return_percent', 10, 4)->nullable(); // Individual ROI
            
            // Engagement
            $table->integer('votes_cast')->default(0);
            $table->timestamp('last_activity_at')->nullable();
            
            // Banking for Distributions
            $table->string('distribution_bank_name')->nullable();
            $table->string('distribution_account_number')->nullable();
            $table->string('distribution_account_holder')->nullable();
            
            // Tax Documents
            $table->json('tax_documents')->nullable(); // Array of K-1 or tax forms
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['cohort_id', 'user_id']);
            $table->index('user_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohort_members');
    }
};
