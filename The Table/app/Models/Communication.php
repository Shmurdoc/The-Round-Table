<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Communication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'cohort_id',
        'sender_id',
        'type',
        'subject',
        'content',
        'recipient_type',
        'recipient_id',
        'parent_id',
        'is_answer',
        'is_public',
        'status',
        'read_at',
        'attachments',
        'priority',
        'views_count',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'attachments' => 'array',
        'is_answer' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Communication::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Communication::class, 'parent_id');
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeQuestions($query)
    {
        return $query->where('type', 'question')->whereNull('parent_id');
    }

    public function scopeAnnouncements($query)
    {
        return $query->where('type', 'announcement');
    }
}
