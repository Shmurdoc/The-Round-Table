<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DailyProfit extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_id',
        'profit_date',
        'total_profit',
        'admin_share',
        'members_share',
        'admin_share_percentage',
        'distributed',
        'distributed_at',
        'distributed_by',
        'notes',
    ];

    protected $casts = [
        'profit_date' => 'date',
        'distributed_at' => 'datetime',
        'distributed' => 'boolean',
        'admin_share_percentage' => 'decimal:2',
    ];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function distributedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'distributed_by');
    }

    public function memberDistributions(): HasMany
    {
        return $this->hasMany(MemberProfitDistribution::class);
    }

    /**
     * Calculate and distribute profit among members
     */
    public function distribute(): bool
    {
        if ($this->distributed) {
            return false;
        }

        $cohort = $this->cohort;
        $activeMembers = $cohort->members()->where('status', 'active')->get();

        if ($activeMembers->isEmpty()) {
            return false;
        }

        // Calculate total capital for profit sharing
        $totalCapital = $activeMembers->sum('capital_committed');

        if ($totalCapital <= 0) {
            return false;
        }

        // Distribute to each member based on their capital share
        foreach ($activeMembers as $member) {
            $sharePercentage = ($member->capital_committed / $totalCapital) * 100;
            $memberAmount = (int) round(($this->members_share * $sharePercentage) / 100);

            if ($memberAmount > 0) {
                MemberProfitDistribution::create([
                    'daily_profit_id' => $this->id,
                    'cohort_member_id' => $member->id,
                    'user_id' => $member->user_id,
                    'amount' => $memberAmount,
                    'share_percentage' => $sharePercentage,
                    'status' => 'pending',
                ]);

                // Update member's profit share percentage for records
                $member->profit_share_percentage = $sharePercentage;
                $member->save();
            }
        }

        // Mark as distributed
        $this->distributed = true;
        $this->distributed_at = now();
        $this->save();

        // Update cohort totals
        $cohort->increment('members_profit_distributed', $this->members_share);
        $cohort->increment('admin_profit_taken', $this->admin_share);

        return true;
    }

    // Formatted amounts
    public function getFormattedTotalProfitAttribute(): string
    {
        return 'R' . number_format($this->total_profit / 100, 2);
    }

    public function getFormattedAdminShareAttribute(): string
    {
        return 'R' . number_format($this->admin_share / 100, 2);
    }

    public function getFormattedMembersShareAttribute(): string
    {
        return 'R' . number_format($this->members_share / 100, 2);
    }

    // Scopes
    public function scopeForCohort($query, $cohortId)
    {
        return $query->where('cohort_id', $cohortId);
    }

    public function scopeUndistributed($query)
    {
        return $query->where('distributed', false);
    }

    public function scopeDistributed($query)
    {
        return $query->where('distributed', true);
    }
}
