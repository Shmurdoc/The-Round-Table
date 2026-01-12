<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_id',
        'cohort_id',
        'user_id',
        'type',
        'amount',
        'currency',
        'direction',
        'category',
        'description',
        'notes',
        'status',
        'from_account',
        'to_account',
        'payment_method',
        'reference_number',
        'receipt_file',
        'invoice_file',
        'supporting_documents',
        'reconciled',
        'reconciled_at',
        'reconciled_by',
        'requires_approval',
        'approval_status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'blockchain_hash',
        'blockchain_network',
        'transaction_date',
        'ip_address',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'reconciled_at' => 'datetime',
        'approved_at' => 'datetime',
        'supporting_documents' => 'array',
        'reconciled' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($transaction) {
            if (empty($transaction->transaction_id)) {
                $transaction->transaction_id = 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(8));
            }
            if (empty($transaction->transaction_date)) {
                $transaction->transaction_date = now();
            }
        });
    }

    public function cohort(): BelongsTo
    {
        return $this->belongsTo(Cohort::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeInflow($query)
    {
        return $query->where('direction', 'inflow');
    }

    public function scopeOutflow($query)
    {
        return $query->where('direction', 'outflow');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
