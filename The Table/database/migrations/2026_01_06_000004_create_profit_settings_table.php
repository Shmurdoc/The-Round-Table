<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profit_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cohort_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('daily_profit_rate', 8, 4)->default(0); // Daily profit percentage
            $table->decimal('min_profit_rate', 8, 4)->default(0);
            $table->decimal('max_profit_rate', 8, 4)->default(10);
            $table->boolean('auto_apply')->default(false); // Auto-apply daily profits
            $table->time('auto_apply_time')->default('00:00:00'); // Time to apply profits
            $table->boolean('is_active')->default(true);
            $table->foreignId('set_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_applied_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profit_settings');
    }
};
