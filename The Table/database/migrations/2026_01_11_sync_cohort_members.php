<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Sync existing cohort_user pivot table records into cohort_members table
     */
    public function up(): void
    {
        // Get all records from cohort_user pivot table
        $pivotRecords = DB::table('cohort_user')->get();

        foreach ($pivotRecords as $pivot) {
            // Check if record already exists in cohort_members
            $exists = DB::table('cohort_members')
                ->where('cohort_id', $pivot->cohort_id)
                ->where('user_id', $pivot->user_id)
                ->exists();

            if (!$exists) {
                // Create cohort_member record
                DB::table('cohort_members')->insert([
                    'cohort_id' => $pivot->cohort_id,
                    'user_id' => $pivot->user_id,
                    'capital_paid' => $pivot->contribution_amount ?? 0,
                    'status' => $pivot->status ?? 'active',
                    'joined_at' => $pivot->joined_at ?? now(),
                    'created_at' => $pivot->created_at ?? now(),
                    'updated_at' => $pivot->updated_at ?? now(),
                ]);
            }
        }

        // Log the result
        echo "Synced " . $pivotRecords->count() . " records from cohort_user to cohort_members\n";
    }

    /**
     * Reverse the migration
     */
    public function down(): void
    {
        // We don't want to delete synced records on rollback
        echo "Rollback not supported for this migration\n";
    }
};
