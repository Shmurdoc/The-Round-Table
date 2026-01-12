<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cohort_id',
        'title',
        'message',
        'type',
        'notification_type',
        'action_url',
        'action_text',
        'read',
        'read_at',
        'sent_email',
        'sent_sms',
        'sent_push',
        'priority',
    ];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
        'sent_email' => 'boolean',
        'sent_sms' => 'boolean',
        'sent_push' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function markAsRead(): void
    {
        $this->read = true;
        $this->read_at = now();
        $this->save();
    }

    /**
     * Static helper to send a notification
     */
    public static function send(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?int $cohortId = null,
        ?string $actionUrl = null,
        ?string $actionText = null,
        string $priority = 'normal'
    ): self {
        return static::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'cohort_id' => $cohortId,
            'action_url' => $actionUrl,
            'action_text' => $actionText,
            'priority' => $priority,
            'read' => false,
        ]);
    }

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('read', false);
    }

    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }
}
