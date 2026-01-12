<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            
            // Vote Details
            $table->string('title');
            $table->text('description');
            $table->enum('type', [
                'admin_replacement',
                'governance_change',
                'exit_early',
                'extraordinary_expense',
                'timeline_extension',
                'general'
            ]);
            
            // Voting Configuration
            $table->integer('threshold_percentage')->default(75); // % needed to pass (75 = supermajority)
            $table->boolean('weighted_by_capital')->default(true); // Capital-weighted vs. one-member-one-vote
            
            // Timeline
            $table->timestamp('voting_starts_at');
            $table->timestamp('voting_ends_at');
            
            // Status
            $table->enum('status', ['draft', 'active', 'passed', 'rejected', 'cancelled'])->default('draft');
            
            // Results
            $table->integer('total_votes_cast')->default(0);
            $table->bigInteger('total_capital_voted')->default(0); // If weighted
            $table->integer('votes_for')->default(0);
            $table->integer('votes_against')->default(0);
            $table->integer('votes_abstain')->default(0);
            $table->bigInteger('capital_for')->default(0);
            $table->bigInteger('capital_against')->default(0);
            $table->bigInteger('capital_abstain')->default(0);
            
            // Outcome
            $table->boolean('passed')->nullable();
            $table->decimal('participation_rate', 5, 2)->nullable(); // % of members who voted
            $table->decimal('approval_rate', 5, 2)->nullable(); // % of yes votes
            $table->timestamp('finalized_at')->nullable();
            
            // Execution
            $table->boolean('executed')->default(false);
            $table->timestamp('executed_at')->nullable();
            $table->text('execution_notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('cohort_id');
            $table->index('type');
            $table->index('status');
            $table->index('voting_ends_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
