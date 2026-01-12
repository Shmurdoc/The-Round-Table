<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('distributions', function (Blueprint $table) {
            $table->id();
            $table->string('distribution_id')->unique(); // DIST-YYYYMMDD-XXXX
            $table->foreignId('cohort_id')->constrained()->onDelete('cascade');
            $table->foreignId('processed_by')->nullable()->constrained('users');
            
            // Distribution Details
            $table->enum('type', ['periodic', 'final', 'partial_refund', 'emergency']);
            $table->bigInteger('total_amount'); // Total being distributed
            $table->text('description');
            
            // Period (if periodic)
            $table->integer('period_year')->nullable();
            $table->integer('period_month')->nullable();
            $table->integer('period_quarter')->nullable();
            
            // Status
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->timestamp('scheduled_date')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // Recipient Count
            $table->integer('total_recipients')->default(0);
            $table->integer('successful_payments')->default(0);
            $table->integer('failed_payments')->default(0);
            
            // Banking Details
            $table->string('batch_reference')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('cohort_id');
            $table->index('type');
            $table->index('status');
            $table->index('scheduled_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('distributions');
    }
};
