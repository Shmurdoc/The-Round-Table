<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CohortMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cohort_id',
        'user_id',
        'capital_committed',
        'capital_paid',
        'payment_status',
        'ownership_percentage',
        'shares',
        'status',
        'is_special_member',
        'base_contribution_limit',
        'excess_contribution',
        'profit_share_percentage',
        'joined_at',
        'commitment_date',
        'payment_date',
        'total_distributions_received',
        'total_profit_received',
        'final_distribution',
        'actual_return_percent',
        'votes_cast',
        'last_activity_at',
        'distribution_bank_name',
        'distribution_account_number',
        'distribution_account_holder',
        'tax_documents',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'commitment_date' => 'datetime',
        'payment_date' => 'datetime',
        'last_activity_at' => 'datetime',
        'tax_documents' => 'array',
        'ownership_percentage' => 'decimal:8',
        'profit_share_percentage' => 'decimal:6',
        'actual_return_percent' => 'decimal:4',
        'is_special_member' => 'boolean',
    ];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calculateOwnership(): void
    {
        $totalCapital = $this->cohort->current_capital;
        if ($totalCapital > 0) {
            $this->ownership_percentage = ($this->capital_committed / $totalCapital) * 100;
            $this->save();
        }
    }

    public function getROI(): ?float
    {
        if ($this->capital_paid == 0) return null;
        $totalReturns = $this->total_distributions_received + $this->final_distribution + $this->total_profit_received;
        return (($totalReturns - $this->capital_paid) / $this->capital_paid) * 100;
    }

    // Profit distributions for this member
    public function profitDistributions(): HasMany
    {
        return $this->hasMany(MemberProfitDistribution::class);
    }

    /**
     * Calculate and set special member status based on contribution
     */
    public function evaluateSpecialMemberStatus(): void
    {
        $cohort = $this->cohort;
        $expectedLimit = $cohort->getExpectedPerMemberContribution();

        if ($expectedLimit > 0 && $this->capital_committed > $expectedLimit) {
            $this->is_special_member = true;
            $this->base_contribution_limit = $expectedLimit;
            $this->excess_contribution = $this->capital_committed - $expectedLimit;
        } else {
            $this->is_special_member = false;
            $this->base_contribution_limit = $expectedLimit;
            $this->excess_contribution = 0;
        }

        $this->save();
        $cohort->recalculateSpecialMembers();
    }

    /**
     * Get member type label
     */
    public function getMemberTypeAttribute(): string
    {
        return $this->is_special_member ? 'Special Partner' : 'Partner';
    }

    /**
     * Get slots this member occupies
     */
    public function getSlotsOccupiedAttribute(): int
    {
        return $this->cohort->calculateMemberSlotsUsed($this->capital_committed);
    }

    /**
     * Get formatted capital committed
     */
    public function getFormattedCapitalAttribute(): string
    {
        return 'R' . number_format($this->capital_committed / 100, 2);
    }

    /**
     * Get formatted total profit received
     */
    public function getFormattedProfitReceivedAttribute(): string
    {
        return 'R' . number_format($this->total_profit_received / 100, 2);
    }
}
