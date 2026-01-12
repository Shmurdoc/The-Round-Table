<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CohortFund extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'cohort_id',
        'wallet_id',
        'fund_id',
        'principal_amount',
        'current_value',
        'total_earnings',
        'profit_rate',
        'status',
        'is_locked',
        'locked_at',
        'auto_lock_at',
        'maturity_date',
        'last_profit_applied_at',
        'notes',
    ];

    protected $casts = [
        'principal_amount' => 'integer',
        'current_value' => 'integer',
        'total_earnings' => 'integer',
        'profit_rate' => 'decimal:4',
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'auto_lock_at' => 'datetime',
        'maturity_date' => 'datetime',
        'last_profit_applied_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($fund) {
            if (empty($fund->fund_id)) {
                $fund->fund_id = 'FUND-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    // Check if fund should be auto-locked
    public function shouldAutoLock(): bool
    {
        return !$this->is_locked 
            && $this->auto_lock_at 
            && now()->gte($this->auto_lock_at);
    }

    // Lock the fund
    public function lock(): self
    {
        $this->is_locked = true;
        $this->locked_at = now();
        $this->status = 'locked';
        $this->save();
        return $this;
    }

    // Unlock the fund (admin only or after maturity)
    public function unlock(): self
    {
        $this->is_locked = false;
        $this->status = 'matured';
        $this->save();
        return $this;
    }

    // Check if fund can be withdrawn
    public function canWithdraw(): bool
    {
        // Can withdraw if not locked, or if matured
        if (!$this->is_locked) {
            return true;
        }
        
        // Check if maturity date has passed
        if ($this->maturity_date && now()->gte($this->maturity_date)) {
            return true;
        }

        return false;
    }

    // Apply daily profit
    public function applyProfit(float $rate = null): int
    {
        $profitRate = $rate ?? $this->profit_rate;
        
        if ($profitRate <= 0) {
            return 0;
        }

        $dailyProfit = (int) round($this->current_value * ($profitRate / 100));
        
        $this->current_value += $dailyProfit;
        $this->total_earnings += $dailyProfit;
        $this->last_profit_applied_at = now();
        $this->save();

        // Update wallet earnings
        $this->wallet->total_earnings += $dailyProfit;
        $this->wallet->save();

        return $dailyProfit;
    }

    // Withdraw fund back to wallet
    public function withdrawToWallet(): bool
    {
        if (!$this->canWithdraw()) {
            return false;
        }

        $wallet = $this->wallet;
        $amount = $this->current_value;

        // Add back to wallet
        $wallet->balance += $amount;
        $wallet->last_activity_at = now();
        $wallet->save();

        // Create transaction
        $wallet->transactions()->create([
            'user_id' => $this->user_id,
            'cohort_id' => $this->cohort_id,
            'cohort_fund_id' => $this->id,
            'transaction_id' => 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(8)),
            'type' => 'transfer_from_cohort',
            'amount' => $amount,
            'balance_before' => $wallet->balance - $amount,
            'balance_after' => $wallet->balance,
            'status' => 'completed',
            'description' => "Withdrawal from cohort: {$this->cohort->name}",
        ]);

        // Mark fund as withdrawn
        $this->status = 'withdrawn';
        $this->save();

        return true;
    }

    // Get return percentage
    public function getReturnPercentageAttribute(): float
    {
        if ($this->principal_amount <= 0) {
            return 0;
        }
        return (($this->current_value - $this->principal_amount) / $this->principal_amount) * 100;
    }

    // Get formatted values
    public function getFormattedPrincipalAttribute(): string
    {
        return 'R ' . number_format($this->principal_amount / 100, 2);
    }

    public function getFormattedCurrentValueAttribute(): string
    {
        return 'R ' . number_format($this->current_value / 100, 2);
    }

    public function getFormattedEarningsAttribute(): string
    {
        return 'R ' . number_format($this->total_earnings / 100, 2);
    }
}
