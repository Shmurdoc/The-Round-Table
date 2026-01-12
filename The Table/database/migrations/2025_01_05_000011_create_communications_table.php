<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('restrict');
            
            // Communication Details
            $table->enum('type', ['announcement', 'message', 'question', 'answer', 'alert', 'system']);
            $table->string('subject')->nullable();
            $table->longText('content');
            
            // Recipients
            $table->enum('recipient_type', ['all_members', 'specific_cohort', 'specific_user', 'platform_admins']);
            $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('cascade');
            
            // Threading (for Q&A)
            $table->foreignId('parent_id')->nullable()->constrained('communications')->onDelete('cascade');
            $table->boolean('is_answer')->default(false);
            $table->boolean('is_public')->default(true); // Public Q&A vs private message
            
            // Status
            $table->enum('status', ['draft', 'sent', 'read', 'archived'])->default('sent');
            $table->timestamp('read_at')->nullable();
            
            // Attachments
            $table->json('attachments')->nullable();
            
            // Importance
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Engagement
            $table->integer('views_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('cohort_id');
            $table->index('sender_id');
            $table->index('recipient_id');
            $table->index('type');
            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
