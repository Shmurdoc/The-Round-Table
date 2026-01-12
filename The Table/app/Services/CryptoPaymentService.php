<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use App\Models\CryptoWallet;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class CryptoPaymentService
{
    private const SUPPORTED_CURRENCIES = [
        'USDT' => ['TRC20', 'ERC20', 'BEP20'],
        'USDC' => ['ERC20', 'POLYGON', 'BEP20'],
        'BTC' => ['BTC'],
        'ETH' => ['ETH'],
        'BNB' => ['BSC'],
    ];

    private const NETWORK_FEES = [
        'USDT_TRC20' => 1,
        'USDT_ERC20' => 5,
        'USDT_BEP20' => 0.20,
        'USDC_ERC20' => 5,
        'USDC_POLYGON' => 0.10,
        'BTC' => 0.0005,
        'ETH' => 0.005,
        'BNB' => 0.001,
    ];

    /**
     * Generate unique deposit address for member
     */
    public function generateDepositAddress(User $user, string $currency, string $network): array
    {
        // In production, integrate with wallet provider API (Coinbase, BitGo, etc.)
        // For now, generate deterministic address from user ID + currency
        
        $wallet = CryptoWallet::firstOrCreate([
            'user_id' => $user->id,
            'currency' => $currency,
            'network' => $network,
        ], [
            'address' => $this->generateAddress($user->id, $currency, $network),
            'balance' => 0,
            'status' => 'active',
        ]);

        return [
            'address' => $wallet->address,
            'currency' => $currency,
            'network' => $network,
            'qr_code_url' => $this->generateQRCode($wallet->address),
        ];
    }

    /**
     * Process incoming crypto deposit
     */
    public function processDeposit(string $address, float $amount, string $currency, string $txHash): Transaction
    {
        $wallet = CryptoWallet::where('address', $address)->firstOrFail();
        $user = $wallet->user;

        // Verify transaction on blockchain (integrate with block explorer API)
        $this->verifyBlockchainTransaction($txHash, $currency, $amount);

        // Calculate fees based on user tier
        $tierFee = $this->getTierDepositFee($user->tier ?? 1);
        $feeAmount = $amount * $tierFee;
        $netAmount = $amount - $feeAmount;

        // Convert to ZAR if auto-convert enabled
        $zarAmount = $user->auto_convert_crypto 
            ? $this->convertToZAR($netAmount, $currency)
            : 0;

        // Update wallet balance
        $wallet->increment('balance', $netAmount);

        // Create transaction record
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'crypto_deposit',
            'amount' => $zarAmount ?: ($netAmount * $this->getCryptoRate($currency, 'ZAR')),
            'currency' => $currency,
            'crypto_amount' => $netAmount,
            'fee' => $feeAmount,
            'status' => 'completed',
            'reference' => $txHash,
            'metadata' => [
                'network' => $wallet->network,
                'raw_amount' => $amount,
                'tier_fee_percent' => $tierFee * 100,
                'exchange_rate' => $this->getCryptoRate($currency, 'ZAR'),
            ],
        ]);

        // If auto-convert, update user ZAR balance
        if ($user->auto_convert_crypto) {
            $user->increment('balance', $zarAmount);
        }

        Log::info("Crypto deposit processed", [
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => $currency,
            'tx_hash' => $txHash,
        ]);

        return $transaction;
    }

    /**
     * Process crypto withdrawal
     */
    public function processWithdrawal(User $user, float $amount, string $currency, string $destinationAddress, string $network): array
    {
        // Validate withdrawal
        $this->validateWithdrawal($user, $amount, $currency, $network);

        // Calculate fees
        $networkFee = self::NETWORK_FEES["{$currency}_{$network}"] ?? 0.001;
        $platformFee = $this->getPlatformWithdrawalFee($user->tier ?? 1);
        $totalFee = $networkFee + ($amount * $platformFee);
        $netAmount = $amount - $totalFee;

        // Verify destination address format
        $this->validateAddress($destinationAddress, $currency, $network);

        // Create pending withdrawal transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'crypto_withdrawal',
            'amount' => $netAmount * $this->getCryptoRate($currency, 'ZAR'),
            'currency' => $currency,
            'crypto_amount' => $netAmount,
            'fee' => $totalFee,
            'status' => 'pending',
            'reference' => null, // Will be updated with TXID
            'metadata' => [
                'destination_address' => $destinationAddress,
                'network' => $network,
                'network_fee' => $networkFee,
                'platform_fee' => $amount * $platformFee,
            ],
        ]);

        // Queue withdrawal for processing
        if ($amount >= 100000) {
            // Large withdrawal - manual review
            $transaction->update(['status' => 'pending_review']);
            // Send notification to admins
        } else {
            // Auto-process small/medium withdrawals
            $this->executeBlockchainWithdrawal($transaction);
        }

        return [
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
            'net_amount' => $netAmount,
            'total_fee' => $totalFee,
            'estimated_completion' => $this->getEstimatedCompletion($amount),
        ];
    }

    /**
     * Get real-time crypto exchange rates
     */
    public function getCryptoRate(string $fromCurrency, string $toCurrency = 'ZAR'): float
    {
        // Integrate with multiple exchange APIs for best rate
        $rates = [];

        try {
            // Binance
            $binanceRate = $this->fetchBinanceRate($fromCurrency, $toCurrency);
            if ($binanceRate) $rates[] = $binanceRate;

            // Coinbase
            $coinbaseRate = $this->fetchCoinbaseRate($fromCurrency, $toCurrency);
            if ($coinbaseRate) $rates[] = $coinbaseRate;

            // Luno (SA-specific)
            $lunoRate = $this->fetchLunoRate($fromCurrency, $toCurrency);
            if ($lunoRate) $rates[] = $lunoRate;

        } catch (Exception $e) {
            Log::error("Error fetching crypto rates: " . $e->getMessage());
        }

        // Calculate weighted average
        $averageRate = count($rates) > 0 ? array_sum($rates) / count($rates) : 0;

        // Apply platform spread
        $spread = 0.015; // 1.5%
        return $averageRate * (1 - $spread);
    }

    /**
     * Convert crypto to ZAR
     */
    private function convertToZAR(float $cryptoAmount, string $currency): float
    {
        $rate = $this->getCryptoRate($currency, 'ZAR');
        return $cryptoAmount * $rate;
    }

    /**
     * Get tier-based deposit fee
     */
    private function getTierDepositFee(int $tier): float
    {
        return match($tier) {
            1 => 0.01,   // 1.0%
            2 => 0.0075, // 0.75%
            3 => 0.005,  // 0.5%
            4 => 0.0035, // 0.35%
            5 => 0.0025, // 0.25%
            default => 0.01,
        };
    }

    /**
     * Get platform withdrawal fee (excluding network fee)
     */
    private function getPlatformWithdrawalFee(int $tier): float
    {
        return $tier === 5 ? 0 : 0.005; // 0.5% for tier 1-4, free for tier 5
    }

    /**
     * Generate deterministic address (placeholder - use real wallet provider in production)
     */
    private function generateAddress(int $userId, string $currency, string $network): string
    {
        // This is a placeholder - in production, use HD wallet or API
        $prefix = match($network) {
            'TRC20' => 'T',
            'ERC20', 'POLYGON' => '0x',
            'BEP20' => '0x',
            'BTC' => random_int(0, 1) ? '1' : '3',
            default => '0x',
        };

        $hash = hash('sha256', $userId . $currency . $network . config('app.key'));
        return $prefix . substr($hash, 0, 40);
    }

    /**
     * Generate QR code for address
     */
    private function generateQRCode(string $address): string
    {
        // Integrate with QR code generation library or API
        return "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($address);
    }

    /**
     * Verify transaction on blockchain
     */
    private function verifyBlockchainTransaction(string $txHash, string $currency, float $amount): bool
    {
        // Integrate with blockchain explorer APIs (Etherscan, Tronscan, Blockchain.com)
        // Verify: transaction exists, correct amount, sufficient confirmations
        return true; // Placeholder
    }

    /**
     * Validate withdrawal request
     */
    private function validateWithdrawal(User $user, float $amount, string $currency, string $network): void
    {
        $wallet = CryptoWallet::where([
            'user_id' => $user->id,
            'currency' => $currency,
            'network' => $network,
        ])->first();

        if (!$wallet || $wallet->balance < $amount) {
            throw new Exception("Insufficient {$currency} balance");
        }

        // Check daily/monthly limits
        $dailyLimit = $this->getDailyWithdrawalLimit($user->tier ?? 1);
        $todayWithdrawals = Transaction::where('user_id', $user->id)
            ->where('type', 'crypto_withdrawal')
            ->whereDate('created_at', today())
            ->sum('crypto_amount');

        if (($todayWithdrawals + $amount) > $dailyLimit) {
            throw new Exception("Daily withdrawal limit exceeded");
        }
    }

    /**
     * Validate destination address format
     */
    private function validateAddress(string $address, string $currency, string $network): void
    {
        // Implement address validation logic based on network
        $valid = match($network) {
            'TRC20' => preg_match('/^T[a-zA-Z0-9]{33}$/', $address),
            'ERC20', 'POLYGON', 'BEP20' => preg_match('/^0x[a-fA-F0-9]{40}$/', $address),
            'BTC' => preg_match('/^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$/', $address),
            default => false,
        };

        if (!$valid) {
            throw new Exception("Invalid {$network} address format");
        }
    }

    /**
     * Execute blockchain withdrawal
     */
    private function executeBlockchainWithdrawal(Transaction $transaction): void
    {
        // Integrate with wallet provider API to broadcast transaction
        // Update transaction with TXID
        // This is a placeholder - implement with actual wallet service
        
        $transaction->update([
            'status' => 'processing',
            'reference' => 'tx_' . bin2hex(random_bytes(16)), // Placeholder TXID
        ]);

        Log::info("Blockchain withdrawal executed", [
            'transaction_id' => $transaction->id,
        ]);
    }

    /**
     * Get estimated completion time
     */
    private function getEstimatedCompletion(float $amount): string
    {
        if ($amount < 10000) return '30 minutes';
        if ($amount < 100000) return '2 hours';
        return '24-48 hours (manual review)';
    }

    /**
     * Get daily withdrawal limit based on tier
     */
    private function getDailyWithdrawalLimit(int $tier): float
    {
        return match($tier) {
            1 => 50000,
            2 => 100000,
            3 => 250000,
            4 => 500000,
            5 => 1000000,
            default => 50000,
        };
    }

    // Exchange API integration methods (placeholders)
    
    private function fetchBinanceRate(string $from, string $to): ?float
    {
        try {
            $symbol = $from . 'USDT';
            $response = Http::get("https://api.binance.com/api/v3/ticker/price", ['symbol' => $symbol]);
            if ($response->successful()) {
                $usdRate = $response->json()['price'];
                // Convert USD to ZAR (fetch USDZAR rate separately)
                return $usdRate * 18.5; // Placeholder conversion
            }
        } catch (Exception $e) {
            Log::warning("Binance rate fetch failed: " . $e->getMessage());
        }
        return null;
    }

    private function fetchCoinbaseRate(string $from, string $to): ?float
    {
        // Implement Coinbase API integration
        return null;
    }

    private function fetchLunoRate(string $from, string $to): ?float
    {
        // Implement Luno API integration (SA-specific)
        return null;
    }
}
