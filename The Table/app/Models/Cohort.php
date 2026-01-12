<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Cohort extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cohort_id',
        'admin_id',
        'admin_user_id',
        'name',
        'title',
        'description',
        'cohort_class',
        'asset_type',
        'contribution_amount',
        'max_members',
        'start_date',
        'minimum_viable_capital',
        'ideal_target',
        'hard_cap',
        'current_capital',
        'min_contribution',
        'max_contribution',
        // Target settings
        'target_amount',
        'per_member_target',
        'target_member_count',
        // Profit sharing
        'admin_profit_share',
        'total_profit_generated',
        'admin_profit_taken',
        'members_profit_distributed',
        'special_member_count',
        'funding_start_date',
        'funding_end_date',
        'expected_deployment_date',
        'expected_exit_date',
        'duration_months',
        'setup_fee_percent',
        'setup_fee_fixed',
        'management_fee_percent',
        'management_fee_fixed',
        'performance_fee_percent',
        'exit_fee_percent',
        'exit_fee_fixed',
        'risk_level',
        'projected_annual_return',
        'risk_factors',
        'exit_strategy',
        'status',
        'production_mode',
        'production_activated_at',
        'production_activated_by',
        'documents',
        'images',
        'prospectus_file',
        'featured_image',
        'governance_rules',
        'immutable',
        'total_revenue',
        'total_expenses',
        'admin_fees_paid',
        'actual_return_percent',
        'performance_bond_amount',
        'performance_bond_status',
        'insurance_policies',
        'escrow_account_number',
        'escrow_agent',
        'deployment_account',
        'revenue_account',
        'admin_account',
        'member_count',
        'view_count',
        'average_rating',
        'featured',
        'launched_at',
        'deployed_at',
        'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'funding_start_date' => 'date',
        'funding_end_date' => 'date',
        'expected_deployment_date' => 'date',
        'expected_exit_date' => 'date',
        'launched_at' => 'datetime',
        'deployed_at' => 'datetime',
        'completed_at' => 'datetime',
        'production_activated_at' => 'datetime',
        'production_mode' => 'boolean',
        'documents' => 'array',
        'images' => 'array',
        'governance_rules' => 'array',
        'insurance_policies' => 'array',
        'immutable' => 'boolean',
        'featured' => 'boolean',
        'projected_annual_return' => 'decimal:2',
        'actual_return_percent' => 'decimal:2',
        'average_rating' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($cohort) {
            if (empty($cohort->cohort_id)) {
                $cohort->cohort_id = 'CID-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            }
        });
    }

    // Relationships
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'cohort_user')
            ->withPivot('contribution_amount', 'joined_at', 'status')
            ->withTimestamps();
    }

    public function members(): HasMany
    {
        return $this->hasMany(CohortMember::class);
    }

    public function investments(): HasMany
    {
        return $this->hasMany(Investment::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(Vote::class);
    }

    public function distributions(): HasMany
    {
        return $this->hasMany(Distribution::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function communications(): HasMany
    {
        return $this->hasMany(Communication::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function timelines(): HasMany
    {
        return $this->hasMany(Timeline::class);
    }

    public function productionActivatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'production_activated_by');
    }

    // Business Logic Methods
    public function fundingProgress(): float
    {
        if ($this->ideal_target == 0) return 0;
        return min(100, ($this->current_capital / $this->ideal_target) * 100);
    }

    public function isFundingOpen(): bool
    {
        return $this->status === 'funding' 
            && now()->between($this->funding_start_date, $this->funding_end_date);
    }

    public function hasMetMVC(): bool
    {
        return $this->current_capital >= $this->minimum_viable_capital;
    }

    public function isOversubscribed(): bool
    {
        return $this->current_capital > $this->hard_cap;
    }

    public function netIncome(): int
    {
        return $this->total_revenue - $this->total_expenses - $this->admin_fees_paid;
    }

    public function calculateROI(): ?float
    {
        if ($this->current_capital == 0) return null;
        return ($this->netIncome() / $this->current_capital) * 100;
    }

    public function canAcceptContribution(int $amount): bool
    {
        if (!$this->isFundingOpen()) return false;
        if ($amount < $this->min_contribution) return false;
        if ($amount > $this->max_contribution) return false;
        if (($this->current_capital + $amount) > $this->hard_cap) return false;
        return true;
    }

    /**
     * Activate production mode - partnership begins operations
     */
    public function activateProduction(User $admin): bool
    {
        if ($this->production_mode) {
            return false; // Already in production
        }

        $this->production_mode = true;
        $this->production_activated_at = now();
        $this->production_activated_by = $admin->id;
        $this->save();

        // Notify all partners
        foreach ($this->members as $member) {
            Notification::create([
                'user_id' => $member->user_id,
                'notification_type' => 'production_activated',
                'title' => '🚀 Partnership Now in Production!',
                'message' => "{$this->title} has been activated for production operations. You'll receive daily progress updates and weekly profit distributions.",
                'cohort_id' => $this->id,
                'action_url' => route('cohorts.show', $this),
                'action_text' => 'View Partnership',
                'priority' => 'high',
            ]);
        }

        // Create timeline entry
        Timeline::create([
            'cohort_id' => $this->id,
            'user_id' => $admin->id,
            'title' => 'Production Mode Activated',
            'description' => 'This partnership has officially entered production operations. Daily updates and weekly profit distributions will now commence.',
            'event_date' => now()->toDateString(),
            'event_type' => 'milestone',
            'is_business_day' => !in_array(now()->dayOfWeek, [0, 6]),
        ]);

        return true;
    }

    /**
     * Check if partnership is operational (in production)
     */
    public function isOperational(): bool
    {
        return $this->production_mode && $this->status === 'operational';
    }

    // Attribute Accessors for view compatibility
    public function getNameAttribute($value)
    {
        // If 'name' field exists, use it; otherwise fall back to 'title'
        return $value ?? $this->attributes['title'] ?? null;
    }

    public function getAdminUserIdAttribute()
    {
        // Map admin_user_id to admin_id for view compatibility
        return $this->attributes['admin_id'] ?? $this->attributes['admin_user_id'] ?? null;
    }

    public function getMembersCountAttribute()
    {
        // Map members_count to member_count
        return $this->attributes['member_count'] ?? 0;
    }

    // Scopes
    public function scopeFunding($query)
    {
        return $query->where('status', 'funding');
    }

    public function scopeOperational($query)
    {
        return $query->where('status', 'operational');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByClass($query, string $class)
    {
        return $query->where('cohort_class', $class);
    }

    // Daily profit relationship
    public function dailyProfits(): HasMany
    {
        return $this->hasMany(DailyProfit::class);
    }

    /**
     * Record daily profit and calculate shares
     */
    public function recordDailyProfit(int $totalProfitCents, ?string $notes = null): DailyProfit
    {
        $adminSharePercent = $this->admin_profit_share ?? 50;
        $adminShare = (int) round(($totalProfitCents * $adminSharePercent) / 100);
        $membersShare = $totalProfitCents - $adminShare;

        $dailyProfit = DailyProfit::create([
            'cohort_id' => $this->id,
            'profit_date' => now()->toDateString(),
            'total_profit' => $totalProfitCents,
            'admin_share' => $adminShare,
            'members_share' => $membersShare,
            'admin_share_percentage' => $adminSharePercent,
            'notes' => $notes,
        ]);

        // Update cohort total
        $this->increment('total_profit_generated', $totalProfitCents);

        return $dailyProfit;
    }

    /**
     * Get expected contribution per member based on target
     */
    public function getExpectedPerMemberContribution(): int
    {
        if ($this->per_member_target > 0) {
            return $this->per_member_target;
        }

        // Calculate from target and expected members
        if ($this->target_member_count > 0 && $this->target_amount > 0) {
            return (int) ceil($this->target_amount / $this->target_member_count);
        }

        // Fall back to min_contribution or ideal_target / max_members
        if ($this->max_members > 0 && $this->ideal_target > 0) {
            return (int) ceil($this->ideal_target / $this->max_members);
        }

        return $this->min_contribution ?? 0;
    }

    /**
     * Check if member contribution exceeds expected limit
     */
    public function isExcessContribution(int $amountCents): bool
    {
        $expected = $this->getExpectedPerMemberContribution();
        return $expected > 0 && $amountCents > $expected;
    }

    /**
     * Calculate how many members would be "replaced" by an excess contribution
     */
    public function calculateMemberSlotsUsed(int $amountCents): int
    {
        $expected = $this->getExpectedPerMemberContribution();
        if ($expected <= 0) return 1;

        return (int) ceil($amountCents / $expected);
    }

    /**
     * Update special member count
     */
    public function recalculateSpecialMembers(): void
    {
        $this->special_member_count = $this->members()
            ->where('is_special_member', true)
            ->count();
        $this->save();
    }

    /**
     * Get effective member count (accounting for special members)
     */
    public function getEffectiveMemberCount(): int
    {
        $regularMembers = $this->member_count - $this->special_member_count;
        
        // Special members take up multiple "slots"
        $specialMemberSlots = 0;
        foreach ($this->members()->where('is_special_member', true)->get() as $member) {
            $specialMemberSlots += $this->calculateMemberSlotsUsed($member->capital_committed);
        }

        return $regularMembers + $specialMemberSlots;
    }

    /**
     * Check if cohort can accept new contribution
     */
    public function canAcceptMemberContribution(int $amountCents, ?int $userId = null): array
    {
        // Check if within cohort limits
        if (($this->current_capital + $amountCents) > $this->hard_cap) {
            return ['allowed' => false, 'reason' => 'Contribution would exceed cohort limit.'];
        }

        if ($amountCents < $this->min_contribution) {
            return ['allowed' => false, 'reason' => 'Contribution below minimum.'];
        }

        if ($this->max_contribution > 0 && $amountCents > $this->max_contribution) {
            return ['allowed' => false, 'reason' => 'Contribution exceeds maximum allowed.'];
        }

        // Check member slots
        $slotsNeeded = $this->calculateMemberSlotsUsed($amountCents);
        $effectiveMembers = $this->getEffectiveMemberCount();

        if (($effectiveMembers + $slotsNeeded) > $this->max_members) {
            return ['allowed' => false, 'reason' => 'Not enough member slots available.'];
        }

        $isSpecial = $this->isExcessContribution($amountCents);

        return [
            'allowed' => true,
            'is_special_member' => $isSpecial,
            'slots_used' => $slotsNeeded,
            'excess_amount' => $isSpecial ? $amountCents - $this->getExpectedPerMemberContribution() : 0,
        ];
    }
}
