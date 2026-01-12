<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberProfitDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'daily_profit_id',
        'cohort_member_id',
        'user_id',
        'amount',
        'share_percentage',
        'status',
        'credited_at',
    ];

    protected $casts = [
        'credited_at' => 'datetime',
        'share_percentage' => 'decimal:6',
    ];

    public function dailyProfit(): BelongsTo
    {
        return $this->belongsTo(DailyProfit::class);
    }

    public function cohortMember(): BelongsTo
    {
        return $this->belongsTo(CohortMember::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Credit profit to user's wallet
     */
    public function creditToWallet(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $user = $this->user;
        $wallet = $user->getOrCreateWallet();

        // Add to wallet balance
        $wallet->balance += $this->amount;
        $wallet->total_earnings += $this->amount;
        $wallet->save();

        // Create wallet transaction
        $wallet->transactions()->create([
            'user_id' => $user->id,
            'transaction_id' => 'PRF-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'type' => 'profit',
            'amount' => $this->amount,
            'balance_before' => $wallet->balance - $this->amount,
            'balance_after' => $wallet->balance,
            'status' => 'approved',
            'description' => 'Daily profit distribution from cohort',
            'metadata' => [
                'daily_profit_id' => $this->daily_profit_id,
                'cohort_id' => $this->dailyProfit->cohort_id,
                'share_percentage' => $this->share_percentage,
            ],
        ]);

        // Update member's total profit received
        $this->cohortMember->increment('total_profit_received', $this->amount);

        // Mark as credited
        $this->status = 'credited';
        $this->credited_at = now();
        $this->save();

        return true;
    }

    // Formatted amount
    public function getFormattedAmountAttribute(): string
    {
        return 'R' . number_format($this->amount / 100, 2);
    }

    // Status badge
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-amber-100 text-amber-700',
            'credited' => 'bg-emerald-100 text-emerald-700',
            'withdrawn' => 'bg-slate-100 text-slate-700',
            default => 'bg-slate-100 text-slate-600',
        };
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCredited($query)
    {
        return $query->where('status', 'credited');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
