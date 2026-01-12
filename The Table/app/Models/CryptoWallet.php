<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoWallet extends Model
{
    protected $fillable = [
        'user_id',
        'currency',
        'network',
        'address',
        'balance',
        'status',
        'metadata',
    ];

    protected $casts = [
        'balance' => 'decimal:8',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get balance in ZAR equivalent
     */
    public function getBalanceInZAR(): float
    {
        $cryptoService = app(\App\Services\CryptoPaymentService::class);
        $rate = $cryptoService->getCryptoRate($this->currency, 'ZAR');
        return $this->balance * $rate;
    }
}
