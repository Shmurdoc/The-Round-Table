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
        Schema::create('cohort_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cohort_id')->constrained()->onDelete('cascade');
            $table->decimal('contribution_amount', 15, 2)->default(0);
            $table->timestamp('joined_at')->nullable();
            $table->enum('status', ['pending', 'active', 'inactive', 'suspended'])->default('pending');
            $table->timestamps();
            
            // Prevent duplicate memberships
            $table->unique(['user_id', 'cohort_id']);
            
            // Indexes for performance
            $table->index('user_id');
            $table->index('cohort_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cohort_user');
    }
};
