<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cohort_id',
        'submitted_by',
        'type',
        'title',
        'period_year',
        'period_month',
        'period_quarter',
        'status',
        'due_date',
        'submitted_at',
        'published_at',
        'content',
        'data',
        'period_revenue',
        'period_expenses',
        'period_net_income',
        'cumulative_revenue',
        'cumulative_expenses',
        'cash_position',
        'utilization_rate',
        'occupancy_rate',
        'maintenance_incidents',
        'asset_condition_notes',
        'attachments',
        'views_count',
        'downloads_count',
        'days_overdue',
        'penalty_applied',
        'penalty_amount',
    ];

    protected $casts = [
        'due_date' => 'date',
        'submitted_at' => 'datetime',
        'published_at' => 'datetime',
        'data' => 'array',
        'attachments' => 'array',
        'utilization_rate' => 'decimal:2',
        'occupancy_rate' => 'decimal:2',
        'penalty_applied' => 'boolean',
    ];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function isOverdue(): bool
    {
        return $this->status !== 'published' && now()->isAfter($this->due_date);
    }

    public function calculateOverdueDays(): int
    {
        if (!$this->isOverdue()) return 0;
        return now()->diffInDays($this->due_date);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'published')
            ->where('due_date', '<', now());
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
