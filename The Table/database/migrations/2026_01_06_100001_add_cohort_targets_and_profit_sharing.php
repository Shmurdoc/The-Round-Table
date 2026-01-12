<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add target and profit sharing columns to cohorts
        Schema::table('cohorts', function (Blueprint $table) {
            // Target settings
            $table->bigInteger('target_amount')->default(0)->after('hard_cap'); // Total cohort target in cents
            $table->bigInteger('per_member_target')->default(0)->after('target_amount'); // Expected contribution per member
            $table->integer('target_member_count')->default(0)->after('per_member_target'); // Expected number of members
            
            // Profit sharing settings
            $table->decimal('admin_profit_share', 5, 2)->default(50.00)->after('performance_fee_percent'); // Admin takes 50% by default
            $table->bigInteger('total_profit_generated')->default(0)->after('total_revenue'); // Total profit made
            $table->bigInteger('admin_profit_taken')->default(0)->after('total_profit_generated'); // Admin's share taken
            $table->bigInteger('members_profit_distributed')->default(0)->after('admin_profit_taken'); // Distributed to members
            
            // Special member tracking
            $table->integer('special_member_count')->default(0)->after('member_count'); // Members who exceeded limit
        });

        // Add special member flag and contribution tracking to cohort_members
        Schema::table('cohort_members', function (Blueprint $table) {
            $table->boolean('is_special_member')->default(false)->after('status'); // Exceeded individual limit
            $table->bigInteger('base_contribution_limit')->default(0)->after('capital_committed'); // Their expected limit
            $table->bigInteger('excess_contribution')->default(0)->after('base_contribution_limit'); // Amount over limit
            $table->decimal('profit_share_percentage', 10, 6)->default(0)->after('ownership_percentage'); // Their share of member profits
            $table->bigInteger('total_profit_received')->default(0)->after('total_distributions_received'); // Profit earnings
        });

        // Create daily profit records table
        Schema::create('daily_profits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained()->onDelete('cascade');
            $table->date('profit_date');
            $table->bigInteger('total_profit')->default(0); // Total profit for the day in cents
            $table->bigInteger('admin_share')->default(0); // Admin's portion
            $table->bigInteger('members_share')->default(0); // Members' portion
            $table->decimal('admin_share_percentage', 5, 2); // Rate used
            $table->boolean('distributed')->default(false); // Has been distributed
            $table->timestamp('distributed_at')->nullable();
            $table->foreignId('distributed_by')->nullable()->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['cohort_id', 'profit_date']);
            $table->index(['cohort_id', 'distributed']);
        });

        // Create member profit distributions table
        Schema::create('member_profit_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_profit_id')->constrained('daily_profits')->onDelete('cascade');
            $table->foreignId('cohort_member_id')->constrained('cohort_members')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->bigInteger('amount'); // Amount in cents
            $table->decimal('share_percentage', 10, 6); // Percentage used
            $table->enum('status', ['pending', 'credited', 'withdrawn'])->default('pending');
            $table->timestamp('credited_at')->nullable();
            $table->timestamps();

            $table->index(['cohort_member_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_profit_distributions');
        Schema::dropIfExists('daily_profits');

        Schema::table('cohort_members', function (Blueprint $table) {
            $table->dropColumn([
                'is_special_member',
                'base_contribution_limit',
                'excess_contribution',
                'profit_share_percentage',
                'total_profit_received',
            ]);
        });

        Schema::table('cohorts', function (Blueprint $table) {
            $table->dropColumn([
                'target_amount',
                'per_member_target',
                'target_member_count',
                'admin_profit_share',
                'total_profit_generated',
                'admin_profit_taken',
                'members_profit_distributed',
                'special_member_count',
            ]);
        });
    }
};
