<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('restrict');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('event_date');
            $table->enum('event_type', [
                'milestone',
                'progress', 
                'profit',
                'update',
                'meeting',
                'achievement',
                'alert'
            ])->default('update');
            
            $table->boolean('is_business_day')->default(true);
            $table->boolean('is_visible_to_members')->default(true);
            
            // Financial data if profit update
            $table->bigInteger('profit_amount')->nullable()->comment('Amount in cents');
            $table->string('proof_document')->nullable()->comment('File path to proof');
            
            // Metadata
            $table->json('metadata')->nullable();
            
            $table->timestamps();
            
            $table->index(['cohort_id', 'event_date']);
            $table->index(['event_type', 'is_visible_to_members']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timelines');
    }
};
