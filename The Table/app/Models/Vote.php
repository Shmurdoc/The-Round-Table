<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cohort_id',
        'created_by',
        'title',
        'description',
        'type',
        'vote_type',
        'voting_options',
        'deadline',
        'threshold_percentage',
        'weighted_by_capital',
        'requires_minimum_participation',
        'minimum_participation_percent',
        'voting_starts_at',
        'voting_ends_at',
        'status',
        'result',
        'winning_option',
        'final_vote_count',
        'closed_at',
        'total_votes_cast',
        'total_capital_voted',
        'votes_for',
        'votes_against',
        'votes_abstain',
        'capital_for',
        'capital_against',
        'capital_abstain',
        'passed',
        'participation_rate',
        'approval_rate',
        'finalized_at',
        'executed',
        'executed_at',
        'execution_notes',
    ];

    protected $casts = [
        'voting_starts_at' => 'datetime',
        'voting_ends_at' => 'datetime',
        'deadline' => 'datetime',
        'closed_at' => 'datetime',
        'finalized_at' => 'datetime',
        'executed_at' => 'datetime',
        'weighted_by_capital' => 'boolean',
        'requires_minimum_participation' => 'boolean',
        'passed' => 'boolean',
        'executed' => 'boolean',
        'voting_options' => 'array',
        'participation_rate' => 'decimal:2',
        'approval_rate' => 'decimal:2',
    ];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(VoteResponse::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && now()->between($this->voting_starts_at, $this->voting_ends_at);
    }

    public function finalizeVote(): void
    {
        $totalMembers = $this->cohort->member_count;
        $totalCapital = $this->cohort->current_capital;

        if ($this->weighted_by_capital) {
            $this->approval_rate = $totalCapital > 0 
                ? ($this->capital_for / $totalCapital) * 100 
                : 0;
            $this->participation_rate = $totalCapital > 0 
                ? ($this->total_capital_voted / $totalCapital) * 100 
                : 0;
        } else {
            $this->approval_rate = $this->total_votes_cast > 0 
                ? ($this->votes_for / $this->total_votes_cast) * 100 
                : 0;
            $this->participation_rate = $totalMembers > 0 
                ? ($this->total_votes_cast / $totalMembers) * 100 
                : 0;
        }

        $this->passed = $this->approval_rate >= $this->threshold_percentage;
        $this->status = $this->passed ? 'passed' : 'rejected';
        $this->finalized_at = now();
        $this->save();
    }

    /**
     * Get participation rate as percentage
     */
    public function getParticipationRate(): float
    {
        $totalMembers = $this->cohort->members()->count();
        $totalResponses = $this->responses()->count();
        
        return $totalMembers > 0 ? ($totalResponses / $totalMembers) * 100 : 0;
    }

    /**
     * Get vote distribution by option
     */
    public function getVoteDistribution(): array
    {
        $distribution = [];
        $options = $this->voting_options ?? [];
        
        foreach ($options as $option) {
            $distribution[$option] = $this->responses()->where('vote_option', $option)->count();
        }
        
        return $distribution;
    }

    /**
     * Get leading option
     */
    public function getLeadingOption(): ?string
    {
        $distribution = $this->getVoteDistribution();
        
        if (empty($distribution)) {
            return null;
        }
        
        $maxVotes = max($distribution);
        
        if ($maxVotes === 0) {
            return null;
        }
        
        return array_search($maxVotes, $distribution);
    }

    /**
     * Determine the result of the vote
     */
    public function determineResult(): array
    {
        $distribution = $this->getVoteDistribution();
        $totalResponses = $this->responses()->count();
        $totalMembers = $this->cohort->members()->count();
        
        $participationRate = $totalMembers > 0 ? ($totalResponses / $totalMembers) * 100 : 0;
        
        // Check minimum participation if required
        if ($this->requires_minimum_participation && $this->minimum_participation_percent) {
            if ($participationRate < $this->minimum_participation_percent) {
                return [
                    'outcome' => 'insufficient_participation',
                    'message' => 'Not enough members participated in the vote.',
                    'participation_rate' => $participationRate,
                ];
            }
        }
        
        $leadingOption = $this->getLeadingOption();
        $leadingVotes = $distribution[$leadingOption] ?? 0;
        
        // Check vote type requirements
        $threshold = match($this->vote_type) {
            'unanimous' => 100,
            'supermajority' => 66.67,
            default => 50,
        };
        
        $leadingPercentage = $totalResponses > 0 ? ($leadingVotes / $totalResponses) * 100 : 0;
        
        if ($leadingPercentage >= $threshold) {
            return [
                'outcome' => 'passed',
                'winning_option' => $leadingOption,
                'vote_percentage' => $leadingPercentage,
                'participation_rate' => $participationRate,
            ];
        }
        
        return [
            'outcome' => 'rejected',
            'leading_option' => $leadingOption,
            'vote_percentage' => $leadingPercentage,
            'participation_rate' => $participationRate,
            'message' => "Vote did not reach the required {$threshold}% threshold.",
        ];
    }

    /**
     * Check if user has voted
     */
    public function hasUserVoted(?int $userId = null): bool
    {
        $userId = $userId ?? auth()->id();
        return $this->responses()->where('user_id', $userId)->exists();
    }

    /**
     * Get user's vote response
     */
    public function getUserResponse(?int $userId = null): ?VoteResponse
    {
        $userId = $userId ?? auth()->id();
        return $this->responses()->where('user_id', $userId)->first();
    }
}
