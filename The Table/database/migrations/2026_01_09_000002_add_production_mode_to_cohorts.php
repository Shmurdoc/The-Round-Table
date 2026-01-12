<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cohorts', function (Blueprint $table) {
            $table->boolean('production_mode')->default(false)->after('status');
            $table->timestamp('production_activated_at')->nullable()->after('production_mode');
            $table->foreignId('production_activated_by')->nullable()->constrained('users')->after('production_activated_at');
        });
    }

    public function down(): void
    {
        Schema::table('cohorts', function (Blueprint $table) {
            $table->dropForeign(['production_activated_by']);
            $table->dropColumn(['production_mode', 'production_activated_at', 'production_activated_by']);
        });
    }
};
