<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Wallet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'wallet_id',
        'balance',
        'pending_balance',
        'locked_balance',
        'total_deposited',
        'total_withdrawn',
        'total_earnings',
        'currency',
        'status',
        'last_activity_at',
    ];

    protected $casts = [
        'balance' => 'integer',
        'locked_balance' => 'integer',
        'total_deposited' => 'integer',
        'total_withdrawn' => 'integer',
        'total_earnings' => 'integer',
        'last_activity_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($wallet) {
            if (empty($wallet->wallet_id)) {
                $wallet->wallet_id = 'RGL' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function cohortFunds(): HasMany
    {
        return $this->hasMany(CohortFund::class);
    }

    // Helper methods
    public function getAvailableBalanceAttribute(): int
    {
        return $this->balance - $this->locked_balance;
    }

    public function getTotalValueAttribute(): int
    {
        return $this->balance + $this->cohortFunds()->sum('current_value');
    }

    public function getFormattedBalanceAttribute(): string
    {
        return 'R ' . number_format($this->balance / 100, 2);
    }

    public function getFormattedAvailableBalanceAttribute(): string
    {
        return 'R ' . number_format($this->available_balance / 100, 2);
    }

    public function canWithdraw(int $amount): bool
    {
        return $this->status === 'active' && $this->available_balance >= $amount;
    }

    public function deposit(int $amount, string $description = null, string $paymentMethod = 'manual'): WalletTransaction
    {
        $balanceBefore = $this->balance;
        $this->balance += $amount;
        $this->total_deposited += $amount;
        $this->last_activity_at = now();
        $this->save();

        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'transaction_id' => 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(8)),
            'type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'status' => 'completed',
            'payment_method' => $paymentMethod,
            'description' => $description ?? 'Wallet deposit',
        ]);
    }

    public function withdraw(int $amount, string $description = null): ?WalletTransaction
    {
        if (!$this->canWithdraw($amount)) {
            return null;
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->total_withdrawn += $amount;
        $this->last_activity_at = now();
        $this->save();

        return $this->transactions()->create([
            'user_id' => $this->user_id,
            'transaction_id' => 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(8)),
            'type' => 'withdrawal',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'status' => 'pending', // Requires admin approval
            'description' => $description ?? 'Wallet withdrawal',
        ]);
    }

    public function transferToCohort(Cohort $cohort, int $amount): ?CohortFund
    {
        if (!$this->canWithdraw($amount)) {
            return null;
        }

        $balanceBefore = $this->balance;
        $this->balance -= $amount;
        $this->last_activity_at = now();
        $this->save();

        // Create transaction record
        $this->transactions()->create([
            'user_id' => $this->user_id,
            'cohort_id' => $cohort->id,
            'transaction_id' => 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(8)),
            'type' => 'transfer_to_cohort',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->balance,
            'status' => 'completed',
            'description' => "Transfer to cohort: {$cohort->name}",
        ]);

        // Create cohort fund record
        return $this->cohortFunds()->create([
            'user_id' => $this->user_id,
            'cohort_id' => $cohort->id,
            'fund_id' => 'FUND-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'principal_amount' => $amount,
            'current_value' => $amount,
            'status' => 'pending',
            'auto_lock_at' => now()->addHours(24), // Auto-lock after 24 hours
        ]);
    }
}
