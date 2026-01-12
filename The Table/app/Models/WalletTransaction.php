<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'wallet_id',
        'user_id',
        'cohort_id',
        'cohort_fund_id',
        'transaction_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'status',
        'payment_method',
        'payment_reference',
        'description',
        'admin_notes',
        'processed_by',
        'processed_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_before' => 'integer',
        'balance_after' => 'integer',
        'processed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function cohortFund(): BelongsTo
    {
        return $this->belongsTo(CohortFund::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeDeposits($query)
    {
        return $query->where('type', 'deposit');
    }

    public function scopeWithdrawals($query)
    {
        return $query->where('type', 'withdrawal');
    }

    // Helpers
    public function getFormattedAmountAttribute(): string
    {
        $prefix = in_array($this->type, ['deposit', 'transfer_from_cohort', 'profit', 'refund']) ? '+' : '-';
        return $prefix . 'R ' . number_format($this->amount / 100, 2);
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'deposit' => 'arrow-down-circle',
            'withdrawal' => 'arrow-up-circle',
            'transfer_to_cohort' => 'send',
            'transfer_from_cohort' => 'download',
            'profit' => 'trending-up',
            'refund' => 'rotate-ccw',
            default => 'circle',
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match($this->type) {
            'deposit', 'transfer_from_cohort', 'profit', 'refund' => 'emerald',
            'withdrawal', 'transfer_to_cohort' => 'amber',
            default => 'slate',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'completed' => 'bg-emerald-100 text-emerald-700',
            'pending' => 'bg-amber-100 text-amber-700',
            'processing' => 'bg-blue-100 text-blue-700',
            'failed' => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-slate-100 text-slate-700',
            default => 'bg-slate-100 text-slate-700',
        };
    }

    // Approve transaction (for withdrawals)
    public function approve(User $admin): self
    {
        $this->status = 'completed';
        $this->processed_by = $admin->id;
        $this->processed_at = now();
        $this->save();
        return $this;
    }

    // Reject transaction
    public function reject(User $admin, string $reason = null): self
    {
        // Refund if withdrawal
        if ($this->type === 'withdrawal' && $this->status === 'pending') {
            $wallet = $this->wallet;
            $wallet->balance += $this->amount;
            $wallet->total_withdrawn -= $this->amount;
            $wallet->save();
        }

        $this->status = 'cancelled';
        $this->processed_by = $admin->id;
        $this->processed_at = now();
        $this->admin_notes = $reason;
        $this->save();
        
        return $this;
    }
}
