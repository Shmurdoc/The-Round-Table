<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Timeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'cohort_id',
        'user_id',
        'title',
        'description',
        'event_date',
        'event_type',
        'is_business_day',
        'is_visible_to_members',
        'profit_amount',
        'proof_document',
        'metadata',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_business_day' => 'boolean',
        'is_visible_to_members' => 'boolean',
        'metadata' => 'array',
    ];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isBusinessDay(): bool
    {
        // Monday = 1, Sunday = 0
        return !in_array($this->event_date->dayOfWeek, [0, 6]);
    }

    public function getEventTypeIcon(): string
    {
        return match($this->event_type) {
            'milestone' => 'flag',
            'progress' => 'trending-up',
            'profit' => 'dollar-sign',
            'update' => 'bell',
            'meeting' => 'users',
            'achievement' => 'award',
            'alert' => 'alert-triangle',
            default => 'calendar',
        };
    }

    public function getEventTypeColor(): string
    {
        return match($this->event_type) {
            'milestone' => 'purple',
            'progress' => 'blue',
            'profit' => 'emerald',
            'update' => 'slate',
            'meeting' => 'amber',
            'achievement' => 'yellow',
            'alert' => 'red',
            default => 'gray',
        };
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible_to_members', true);
    }

    public function scopeBusinessDays($query)
    {
        return $query->where('is_business_day', true);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('event_date', '>=', Carbon::now()->subDays($days));
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('event_type', $type);
    }
}
