<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cohort;
use App\Models\Distribution;
use App\Models\Notification;
use Carbon\Carbon;

class DistributeWeeklyProfits extends Command
{
    protected $signature = 'profits:distribute-weekly 
                            {--force : Force distribution even if not Friday}
                            {--cohort= : Process only specific cohort ID}';

    protected $description = 'Distribute weekly partnership profits every Friday (50% admin, 50% partners)';

    public function handle(): int
    {
        $today = Carbon::now();
        
        // Only run on Fridays unless forced
        if (!$this->option('force') && $today->dayOfWeek !== Carbon::FRIDAY) {
            $this->info('⏭️  Not Friday. Skipping automatic distribution.');
            $this->info('   Next distribution: ' . $today->next(Carbon::FRIDAY)->format('l, F j, Y'));
            return 0;
        }

        $this->info('💰 Starting Weekly Profit Distribution - ' . $today->format('l, F j, Y'));
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Get operational cohorts in production mode
        $query = Cohort::where('status', 'operational')
            ->where('production_mode', true);
        
        if ($cohortId = $this->option('cohort')) {
            $query->where('id', $cohortId);
        }
        
        $cohorts = $query->with(['admin', 'members.user'])->get();

        if ($cohorts->isEmpty()) {
            $this->warn('⚠️  No cohorts found in production mode.');
            return 0;
        }

        $this->info("📊 Processing {$cohorts->count()} partnership(s)...\n");

        $totalDistributed = 0;
        $successCount = 0;
        $failCount = 0;

        foreach ($cohorts as $cohort) {
            try {
                $this->line("🔹 Processing: {$cohort->title}");
                
                // Calculate pending profit (from timeline profit entries this week)
                $weekStart = $today->copy()->startOfWeek();
                $weekEnd = $today->copy()->endOfWeek();
                
                $weeklyProfit = $cohort->timelines()
                    ->where('event_type', 'profit')
                    ->whereBetween('event_date', [$weekStart, $weekEnd])
                    ->sum('profit_amount');
                
                if ($weeklyProfit <= 0) {
                    $this->line("   ⏭️  No profits recorded this week. Skipping.");
                    continue;
                }

                $this->line("   💵 Weekly Profit: R" . number_format($weeklyProfit / 100, 2));

                // Create distribution
                $distribution = Distribution::create([
                    'cohort_id' => $cohort->id,
                    'processed_by' => $cohort->admin_id,
                    'type' => 'weekly_profit',
                    'gross_profit' => (int)$weeklyProfit,
                    'total_amount' => (int)$weeklyProfit,
                    'split_percentage' => 50.00,
                    'description' => "Weekly Profit Distribution - Week of {$weekStart->format('M j')} to {$weekEnd->format('M j, Y')}",
                    'distribution_date' => $today->toDateString(),
                    'status' => 'processing',
                ]);

                // Create payments (50% admin, 50% partners pro-rata)
                $distribution->createPayments();

                $distribution->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'successful_payments' => $distribution->payments()->count(),
                ]);

                $this->line("   ✅ Distributed:");
                $this->line("      • Admin (50%): R" . number_format($distribution->admin_share / 100, 2));
                $this->line("      • Partners (50%): R" . number_format($distribution->partners_share / 100, 2));
                $this->line("      • {$distribution->payments()->count()} payments created");

                // Notify admin
                Notification::create([
                    'user_id' => $cohort->admin_id,
                    'notification_type' => 'distribution_completed',
                    'title' => '💰 Weekly Distribution Completed',
                    'message' => "R" . number_format($weeklyProfit / 100, 2) . " distributed to {$cohort->title} partners.",
                    'cohort_id' => $cohort->id,
                    'action_url' => route('admin.cohorts.show', $cohort),
                ]);

                $totalDistributed += $weeklyProfit;
                $successCount++;

            } catch (\Exception $e) {
                $this->error("   ❌ Error: " . $e->getMessage());
                $failCount++;
                
                // Log error
                \Log::error('Weekly distribution failed for cohort ' . $cohort->id, [
                    'error' => $e->getMessage(),
                    'cohort' => $cohort->title,
                ]);
            }

            $this->newLine();
        }

        // Summary
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info('📈 Distribution Summary:');
        $this->info("   ✓ Successful: {$successCount}");
        if ($failCount > 0) {
            $this->error("   ✗ Failed: {$failCount}");
        }
        $this->info("   💰 Total Distributed: R" . number_format($totalDistributed / 100, 2));
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        return $successCount > 0 ? 0 : 1;
    }
}
