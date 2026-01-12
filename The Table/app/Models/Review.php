<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cohort_id',
        'user_id',
        'rating',
        'review_text',
        'transparency_rating',
        'communication_rating',
        'execution_rating',
        'returns_rating',
        'status',
        'published_at',
        'verified_participant',
        'helpful_count',
        'not_helpful_count',
        'admin_response',
        'admin_responded_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'admin_responded_at' => 'datetime',
        'verified_participant' => 'boolean',
    ];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAverageRating(): float
    {
        $ratings = array_filter([
            $this->transparency_rating,
            $this->communication_rating,
            $this->execution_rating,
            $this->returns_rating,
        ]);
        
        return count($ratings) > 0 ? array_sum($ratings) / count($ratings) : $this->rating;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeVerified($query)
    {
        return $query->where('verified_participant', true);
    }
}
