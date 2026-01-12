<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('restrict');
            
            // Document Details
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', [
                'prospectus',
                'contract',
                'invoice',
                'receipt',
                'report',
                'insurance',
                'legal',
                'tax',
                'kyc',
                'bank_statement',
                'photo',
                'other'
            ]);
            
            // File Information
            $table->string('filename');
            $table->string('original_filename');
            $table->string('file_path');
            $table->string('mime_type');
            $table->bigInteger('file_size'); // In bytes
            $table->string('file_hash')->nullable(); // For integrity verification
            
            // Access Control
            $table->enum('visibility', ['public', 'members_only', 'admin_only', 'private'])->default('members_only');
            
            // Status
            $table->enum('status', ['active', 'archived', 'deleted'])->default('active');
            
            // Metadata
            $table->integer('download_count')->default(0);
            $table->timestamp('last_accessed_at')->nullable();
            
            // Blockchain (optional)
            $table->string('blockchain_hash')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('cohort_id');
            $table->index('user_id');
            $table->index('type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
