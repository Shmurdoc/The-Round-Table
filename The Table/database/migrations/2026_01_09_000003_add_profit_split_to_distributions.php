<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('distributions', function (Blueprint $table) {
            $table->bigInteger('admin_share')->default(0)->after('total_amount')->comment('Admin 50% operational share in cents');
            $table->bigInteger('partners_share')->default(0)->after('admin_share')->comment('Partners 50% share in cents');
            $table->decimal('split_percentage', 5, 2)->default(50.00)->after('partners_share')->comment('Admin split percentage');
        });
    }

    public function down(): void
    {
        Schema::table('distributions', function (Blueprint $table) {
            $table->dropColumn(['admin_share', 'partners_share', 'split_percentage']);
        });
    }
};
