<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfitSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_id',
        'daily_profit_rate',
        'min_profit_rate',
        'max_profit_rate',
        'auto_apply',
        'auto_apply_time',
        'is_active',
        'set_by',
        'last_applied_at',
        'notes',
    ];

    protected $casts = [
        'daily_profit_rate' => 'decimal:4',
        'min_profit_rate' => 'decimal:4',
        'max_profit_rate' => 'decimal:4',
        'auto_apply' => 'boolean',
        'is_active' => 'boolean',
        'last_applied_at' => 'datetime',
    ];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function setBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'set_by');
    }

    // Apply profits to all active funds in this cohort
    public function applyDailyProfits(): array
    {
        $results = [];
        
        $funds = CohortFund::where('cohort_id', $this->cohort_id)
            ->where('is_locked', true)
            ->where('status', 'locked')
            ->get();

        foreach ($funds as $fund) {
            $profit = $fund->applyProfit($this->daily_profit_rate);
            $results[] = [
                'fund_id' => $fund->fund_id,
                'user_id' => $fund->user_id,
                'profit' => $profit,
            ];
        }

        $this->last_applied_at = now();
        $this->save();

        return $results;
    }

    // Get global/default profit setting
    public static function getGlobalSetting(): ?self
    {
        return static::whereNull('cohort_id')->where('is_active', true)->first();
    }

    // Get setting for a specific cohort (or fall back to global)
    public static function getForCohort(int $cohortId): ?self
    {
        return static::where('cohort_id', $cohortId)->where('is_active', true)->first()
            ?? static::getGlobalSetting();
    }
}
