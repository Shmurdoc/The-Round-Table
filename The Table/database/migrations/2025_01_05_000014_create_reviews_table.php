<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Review Details
            $table->integer('rating'); // 1-5 stars
            $table->text('review_text')->nullable();
            
            // Specific Ratings
            $table->integer('transparency_rating')->nullable(); // 1-5
            $table->integer('communication_rating')->nullable(); // 1-5
            $table->integer('execution_rating')->nullable(); // 1-5
            $table->integer('returns_rating')->nullable(); // 1-5
            
            // Status
            $table->enum('status', ['pending', 'published', 'flagged', 'removed'])->default('pending');
            $table->timestamp('published_at')->nullable();
            
            // Verification
            $table->boolean('verified_participant')->default(false); // Only members can review
            
            // Engagement
            $table->integer('helpful_count')->default(0);
            $table->integer('not_helpful_count')->default(0);
            
            // Admin Response
            $table->text('admin_response')->nullable();
            $table->timestamp('admin_responded_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['cohort_id', 'user_id']);
            $table->index('cohort_id');
            $table->index('rating');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
