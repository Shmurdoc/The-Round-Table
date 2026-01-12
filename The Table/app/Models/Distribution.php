<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Distribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'distribution_id',
        'cohort_id',
        'processed_by',
        'type',
        'total_amount',
        'admin_share',
        'partners_share',
        'split_percentage',
        'description',
        'period_year',
        'period_month',
        'period_quarter',
        'status',
        'scheduled_date',
        'processed_at',
        'completed_at',
        'total_recipients',
        'successful_payments',
        'failed_payments',
        'batch_reference',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Accessor for distribution_date (alias for scheduled_date)
     * This provides backwards compatibility with views using distribution_date
     */
    public function getDistributionDateAttribute(): ?\Illuminate\Support\Carbon
    {
        return $this->scheduled_date;
    }

    /**
     * Get the amount per member (alias for partners_share / total_recipients)
     */
    public function getAmountPerMemberAttribute(): float
    {
        if ($this->total_recipients && $this->total_recipients > 0) {
            return ($this->partners_share ?? $this->total_amount) / $this->total_recipients / 100;
        }
        return $this->total_amount / 100;
    }

    /**
     * Get creator (alias for processedBy)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get notes (uses description field)
     */
    public function getNotesAttribute(): ?string
    {
        return $this->description;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($distribution) {
            if (empty($distribution->distribution_id)) {
                $distribution->distribution_id = 'DIST-' . date('Ymd') . '-' . strtoupper(Str::random(8));
            }
        });
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DistributionPayment::class);
    }

    /**
     * Create payments with 50% admin operational share
     */
    public function createPayments(): void
    {
        $grossProfit = $this->gross_profit ?? $this->total_amount;
        
        // Calculate 50/50 split
        $adminShare = ($grossProfit * ($this->split_percentage ?? 50)) / 100;
        $partnersShare = $grossProfit - $adminShare;
        
        // Update distribution record
        $this->admin_share = (int)$adminShare;
        $this->partners_share = (int)$partnersShare;
        $this->save();
        
        // Create admin payment FIRST (operational partner)
        $admin = $this->cohort->admin;
        DistributionPayment::create([
            'distribution_id' => $this->id,
            'user_id' => $admin->id,
            'cohort_member_id' => null, // Admin is not a regular member
            'amount' => (int)$adminShare,
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // Credit admin wallet
        $admin->wallet_balance += (int)$adminShare;
        $admin->save();
        
        // Get all active partners
        $members = $this->cohort->members()
            ->where('status', 'active')
            ->get();
        
        $totalCapital = $members->sum('capital_paid');
        
        // Distribute remaining 50% to partners pro-rata
        foreach ($members as $member) {
            if ($totalCapital > 0) {
                $memberPercentage = ($member->capital_paid / $totalCapital) * 100;
                $amount = ($partnersShare * $memberPercentage) / 100;
            } else {
                $amount = 0;
            }
            
            DistributionPayment::create([
                'distribution_id' => $this->id,
                'cohort_member_id' => $member->id,
                'user_id' => $member->user_id,
                'amount' => $amount,
                'bank_name' => $member->distribution_bank_name ?? $member->user->bank_name,
                'account_number' => $member->distribution_account_number ?? $member->user->account_number,
                'account_holder' => $member->distribution_account_holder ?? $member->user->account_holder_name,
                'status' => 'pending',
            ]);
        }

        $this->total_recipients = $members->count();
        $this->save();
    }
}
