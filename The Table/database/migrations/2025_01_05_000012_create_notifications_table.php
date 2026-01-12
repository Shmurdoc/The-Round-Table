<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cohort_id')->nullable()->constrained()->onDelete('cascade');
            
            // Notification Details
            $table->string('title');
            $table->text('message');
            $table->enum('type', [
                'cohort_launched',
                'funding_complete',
                'report_published',
                'vote_started',
                'distribution_processed',
                'admin_message',
                'system_alert',
                'kyc_status',
                'payment_received',
                'payment_failed',
                'cohort_status_change',
                'document_uploaded',
                'other'
            ]);
            
            // Action Link
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            
            // Status
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            
            // Channels
            $table->boolean('sent_email')->default(false);
            $table->boolean('sent_sms')->default(false);
            $table->boolean('sent_push')->default(false);
            
            // Priority
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('cohort_id');
            $table->index('type');
            $table->index('read');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
