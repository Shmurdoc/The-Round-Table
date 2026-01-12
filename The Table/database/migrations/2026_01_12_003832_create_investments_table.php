<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('investment_type')->default('equity');
            $table->decimal('amount', 15, 2);
            $table->decimal('current_value', 15, 2)->nullable();
            $table->string('status')->default('active');
            $table->text('description')->nullable();
            $table->date('investment_date');
            $table->date('maturity_date')->nullable();
            $table->decimal('expected_return', 8, 4)->nullable(); // percentage
            $table->json('metadata')->nullable(); // for additional flexible data
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
            
            $table->index(['cohort_id', 'status']);
            $table->index(['user_id', 'investment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investments');
    }
};
