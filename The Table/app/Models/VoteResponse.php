<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoteResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'vote_id',
        'user_id',
        'cohort_member_id',
        'choice',
        'comment',
        'capital_weight',
        'vote_weight',
        'voted_at',
        'ip_address',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    public function vote(): BelongsTo
    {
        return $this->belongsTo(Vote::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cohortMember(): BelongsTo
    {
        return $this->belongsTo(CohortMember::class);
    }
}
