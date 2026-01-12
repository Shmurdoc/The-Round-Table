<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kyc_id_number')->nullable()->after('id_number');
            $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_verified_at');
            $table->text('kyc_rejection_reason')->nullable()->after('kyc_submitted_at');
            $table->string('kyc_id_document_front')->nullable()->after('id_document');
            $table->string('kyc_id_document_back')->nullable()->after('kyc_id_document_front');
            $table->string('kyc_proof_of_residence')->nullable()->after('proof_of_address');
            $table->string('branch_code')->nullable()->after('bank_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'kyc_id_number',
                'kyc_submitted_at',
                'kyc_rejection_reason',
                'kyc_id_document_front',
                'kyc_id_document_back',
                'kyc_proof_of_residence',
                'branch_code',
            ]);
        });
    }
};
