<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cohorts', function (Blueprint $table) {
            $table->id();
            $table->string('cohort_id')->unique(); // CID-YYYYMMDD-XXXX
            $table->foreignId('admin_id')->constrained('users')->onDelete('restrict');
            
            // Basic Information
            $table->string('title');
            $table->text('description');
            $table->enum('cohort_class', ['utilization', 'lease', 'resale', 'hybrid']);
            $table->enum('asset_type', ['real_estate', 'equipment', 'business', 'renewable_energy', 'intellectual_property', 'other']);
            
            // Financial Thresholds (in cents/smallest currency unit)
            $table->bigInteger('minimum_viable_capital'); // R3,000 = 300000
            $table->bigInteger('ideal_target');           // Target amount
            $table->bigInteger('hard_cap');               // Maximum 120-150% of ideal
            $table->bigInteger('current_capital')->default(0); // Funded so far
            
            // Contribution Limits per Member (in cents)
            $table->bigInteger('min_contribution')->default(300000); // R3,000
            $table->bigInteger('max_contribution')->default(10000000); // R100,000
            
            // Timeline
            $table->date('funding_start_date');
            $table->date('funding_end_date');
            $table->date('expected_deployment_date')->nullable();
            $table->date('expected_exit_date');
            $table->integer('duration_months'); // Expected operational period
            
            // Fee Structure (percentages stored as basis points: 10% = 1000)
            $table->integer('setup_fee_percent')->default(0); // e.g., 200 = 2%
            $table->bigInteger('setup_fee_fixed')->default(0); // Fixed amount in cents
            $table->integer('management_fee_percent')->default(0); // Monthly/Quarterly
            $table->bigInteger('management_fee_fixed')->default(0);
            $table->integer('performance_fee_percent')->default(0); // % of profits above hurdle
            $table->integer('exit_fee_percent')->default(0);
            $table->bigInteger('exit_fee_fixed')->default(0);
            
            // Risk & Performance
            $table->enum('risk_level', ['low', 'moderate', 'high'])->default('moderate');
            $table->decimal('projected_annual_return', 5, 2)->nullable(); // e.g., 12.50 = 12.5%
            $table->text('risk_factors')->nullable();
            $table->text('exit_strategy')->nullable();
            
            // Status & Lifecycle
            $table->enum('status', [
                'draft',              // Admin creating
                'pending_approval',   // Submitted for platform review
                'approved',           // Ready for funding
                'funding',            // Accepting capital
                'funding_failed',     // Below MVC
                'validating',         // Post-funding checks
                'deploying',          // Capital deployment phase
                'operational',        // Running operations
                'exiting',            // Exit process started
                'completed',          // Successfully completed
                'cancelled',          // Cancelled before deployment
                'failed'              // Failed during operations
            ])->default('draft');
            
            $table->timestamp('launched_at')->nullable();
            $table->timestamp('deployed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Documents & Media
            $table->json('documents')->nullable(); // Array of document paths
            $table->json('images')->nullable(); // Array of image paths
            $table->string('prospectus_file')->nullable();
            $table->string('featured_image')->nullable();
            
            // Governance
            $table->json('governance_rules')->nullable(); // Custom rules JSON
            $table->boolean('immutable')->default(false); // Rules locked after launch
            
            // Performance Tracking
            $table->bigInteger('total_revenue')->default(0); // Accumulated revenue
            $table->bigInteger('total_expenses')->default(0); // Accumulated expenses
            $table->bigInteger('admin_fees_paid')->default(0);
            $table->decimal('actual_return_percent', 5, 2)->nullable(); // Final ROI
            
            // Admin Performance Bond
            $table->bigInteger('performance_bond_amount')->default(0);
            $table->enum('performance_bond_status', ['pending', 'posted', 'returned', 'forfeited'])->default('pending');
            
            // Insurance
            $table->json('insurance_policies')->nullable();
            
            // Escrow Details
            $table->string('escrow_account_number')->nullable();
            $table->string('escrow_agent')->nullable();
            
            // Bank Accounts
            $table->string('deployment_account')->nullable();
            $table->string('revenue_account')->nullable();
            $table->string('admin_account')->nullable();
            
            // Platform Metadata
            $table->integer('member_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->decimal('average_rating', 3, 2)->nullable();
            $table->boolean('featured')->default(false);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('cohort_id');
            $table->index('admin_id');
            $table->index('status');
            $table->index('cohort_class');
            $table->index('funding_start_date');
            $table->index('funding_end_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cohorts');
    }
};
