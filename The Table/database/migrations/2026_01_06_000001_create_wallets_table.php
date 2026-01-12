<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('wallet_id')->unique(); // e.g., RGL001887
            $table->bigInteger('balance')->default(0); // In cents
            $table->bigInteger('locked_balance')->default(0); // Funds locked in cohorts
            $table->bigInteger('total_deposited')->default(0);
            $table->bigInteger('total_withdrawn')->default(0);
            $table->bigInteger('total_earnings')->default(0);
            $table->string('currency')->default('ZAR');
            $table->string('status')->default('active'); // active, suspended, frozen
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
