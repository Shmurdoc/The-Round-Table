<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DistributionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'distribution_id',
        'cohort_member_id',
        'user_id',
        'amount',
        'currency',
        'bank_name',
        'account_number',
        'account_holder',
        'status',
        'payment_reference',
        'failure_reason',
        'processed_at',
        'completed_at',
        'transaction_id',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function distribution(): BelongsTo
    {
        return $this->belongsTo(Distribution::class);
    }

    public function cohortMember(): BelongsTo
    {
        return $this->belongsTo(CohortMember::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
