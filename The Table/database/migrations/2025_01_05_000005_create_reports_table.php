<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained()->onDelete('cascade');
            $table->foreignId('submitted_by')->constrained('users')->onDelete('restrict');
            
            // Report Details
            $table->enum('type', ['monthly_financial', 'quarterly_operational', 'annual_comprehensive', 'incident', 'exit']);
            $table->string('title');
            $table->integer('period_year');
            $table->integer('period_month')->nullable();
            $table->integer('period_quarter')->nullable();
            
            // Status
            $table->enum('status', ['draft', 'submitted', 'published', 'overdue'])->default('draft');
            $table->date('due_date');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('published_at')->nullable();
            
            // Content
            $table->longText('content'); // Rich text/HTML content
            $table->json('data')->nullable(); // Structured data (financial figures, metrics)
            
            // Financial Summary (for financial reports)
            $table->bigInteger('period_revenue')->nullable();
            $table->bigInteger('period_expenses')->nullable();
            $table->bigInteger('period_net_income')->nullable();
            $table->bigInteger('cumulative_revenue')->nullable();
            $table->bigInteger('cumulative_expenses')->nullable();
            $table->bigInteger('cash_position')->nullable();
            
            // Operational Metrics (for operational reports)
            $table->decimal('utilization_rate', 5, 2)->nullable(); // %
            $table->decimal('occupancy_rate', 5, 2)->nullable(); // %
            $table->integer('maintenance_incidents')->nullable();
            $table->text('asset_condition_notes')->nullable();
            
            // Attachments
            $table->json('attachments')->nullable(); // PDF reports, photos, etc.
            
            // Member Engagement
            $table->integer('views_count')->default(0);
            $table->integer('downloads_count')->default(0);
            
            // Automated Penalties for Late Submission
            $table->integer('days_overdue')->default(0);
            $table->boolean('penalty_applied')->default(false);
            $table->bigInteger('penalty_amount')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('cohort_id');
            $table->index('type');
            $table->index('status');
            $table->index('due_date');
            $table->index(['period_year', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
