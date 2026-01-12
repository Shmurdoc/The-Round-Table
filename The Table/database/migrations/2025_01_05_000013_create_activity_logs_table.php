<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('cohort_id')->nullable()->constrained()->onDelete('cascade');
            
            // Activity Details
            $table->string('action'); // e.g., 'cohort.created', 'member.joined', 'vote.cast'
            $table->string('description');
            $table->json('properties')->nullable(); // Additional data
            
            // Context
            $table->nullableMorphs('subject'); // The affected model (Cohort, User, Transaction, etc.)
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('cohort_id');
            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
