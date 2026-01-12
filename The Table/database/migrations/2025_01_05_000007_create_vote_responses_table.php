<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vote_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vote_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cohort_member_id')->constrained()->onDelete('cascade');
            
            // Response
            $table->enum('choice', ['for', 'against', 'abstain']);
            $table->text('comment')->nullable();
            
            // Weight (for capital-weighted votes)
            $table->bigInteger('capital_weight'); // Member's capital contribution
            $table->integer('vote_weight')->default(1); // 1 for one-member-one-vote
            
            // Metadata
            $table->timestamp('voted_at');
            $table->string('ip_address')->nullable();
            
            $table->timestamps();
            
            $table->unique(['vote_id', 'user_id']);
            $table->index('vote_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vote_responses');
    }
};
