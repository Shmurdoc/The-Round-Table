<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Transaction;
use Exception;

/**
 * NOWPayments API Service for USDT Payments
 * Documentation: https://documenter.getpostman.com/view/7907941/S1a32n38
 */
class NOWPaymentsService
{
    private string $apiKey;
    private string $apiUrl;
    private string $ipnSecret;

    public function __construct()
    {
        $this->apiKey = config('services.nowpayments.api_key');
        $this->apiUrl = config('services.nowpayments.api_url', 'https://api.nowpayments.io/v1');
        $this->ipnSecret = config('services.nowpayments.ipn_secret');
    }

    /**
     * Get API status
     */
    public function getStatus(): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->get("{$this->apiUrl}/status");

            return $response->json();
        } catch (Exception $e) {
            Log::error('NOWPayments API Status Error: ' . $e->getMessage());
            throw new Exception('Failed to get NOWPayments status');
        }
    }

    /**
     * Get available currencies
     */
    public function getAvailableCurrencies(): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->get("{$this->apiUrl}/currencies");

            return $response->json()['currencies'] ?? [];
        } catch (Exception $e) {
            Log::error('NOWPayments Get Currencies Error: ' . $e->getMessage());
            return ['usdttrc20', 'usdterc20', 'usdtbep20'];
        }
    }

    /**
     * Get minimum payment amount for a currency
     */
    public function getMinimumAmount(string $currency = 'usdttrc20'): float
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->get("{$this->apiUrl}/min-amount", [
                'currency_from' => 'usd',
                'currency_to' => $currency,
            ]);

            return (float) ($response->json()['min_amount'] ?? 10.0);
        } catch (Exception $e) {
            Log::error('NOWPayments Get Min Amount Error: ' . $e->getMessage());
            return 10.0; // Default minimum
        }
    }

    /**
     * Create payment
     * 
     * @param float $amountUSD Amount in USD
     * @param string $currency Payment currency (usdttrc20, usdterc20, usdtbep20)
     * @param User $user User making payment
     * @param string $orderDescription Description for the payment
     * @return array Payment details including pay_address
     */
    public function createPayment(
        float $amountUSD,
        string $currency,
        User $user,
        string $orderDescription = 'RoundTable Contribution'
    ): array {
        try {
            $orderId = 'RT-' . $user->id . '-' . time();

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->apiUrl}/payment", [
                'price_amount' => $amountUSD,
                'price_currency' => 'usd',
                'pay_currency' => $currency,
                'order_id' => $orderId,
                'order_description' => $orderDescription,
                'ipn_callback_url' => route('webhook.nowpayments'),
                'success_url' => route('wallet.index') . '?payment=success',
                'cancel_url' => route('wallet.deposit.form') . '?payment=cancelled',
            ]);

            if (!$response->successful()) {
                Log::error('NOWPayments Create Payment Error', [
                    'response' => $response->json(),
                    'status' => $response->status(),
                ]);
                throw new Exception('Failed to create payment: ' . ($response->json()['message'] ?? 'Unknown error'));
            }

            $data = $response->json();

            // Create transaction record
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'deposit',
                'payment_method' => 'crypto',
                'crypto_currency' => $currency,
                'amount' => (int) ($amountUSD * 100), // Store in cents
                'status' => 'pending',
                'payment_id' => $data['payment_id'] ?? null,
                'order_id' => $orderId,
                'pay_address' => $data['pay_address'] ?? null,
                'pay_amount' => $data['pay_amount'] ?? null,
                'metadata' => json_encode([
                    'network' => $this->getCurrencyNetwork($currency),
                    'payment_url' => $data['invoice_url'] ?? null,
                ]),
            ]);

            Log::info('NOWPayments payment created', [
                'order_id' => $orderId,
                'payment_id' => $data['payment_id'] ?? 'N/A',
                'user_id' => $user->id,
            ]);

            return $data;
        } catch (Exception $e) {
            Log::error('NOWPayments Create Payment Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $paymentId): array
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
            ])->get("{$this->apiUrl}/payment/{$paymentId}");

            if (!$response->successful()) {
                throw new Exception('Failed to get payment status');
            }

            return $response->json();
        } catch (Exception $e) {
            Log::error('NOWPayments Get Payment Status Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify IPN callback signature
     */
    public function verifyIPNSignature(string $receivedSignature, string $payload): bool
    {
        $expectedSignature = hash_hmac('sha512', $payload, $this->ipnSecret);
        return hash_equals($expectedSignature, $receivedSignature);
    }

    /**
     * Process IPN callback
     */
    public function processIPNCallback(array $data): void
    {
        try {
            $paymentId = $data['payment_id'] ?? null;
            $orderId = $data['order_id'] ?? null;
            $paymentStatus = $data['payment_status'] ?? null;

            if (!$paymentId || !$orderId) {
                throw new Exception('Invalid IPN data');
            }

            // Find transaction
            $transaction = Transaction::where('payment_id', $paymentId)
                ->orWhere('order_id', $orderId)
                ->first();

            if (!$transaction) {
                Log::warning('NOWPayments IPN: Transaction not found', ['payment_id' => $paymentId]);
                return;
            }

            // Update transaction status
            $statusMap = [
                'finished' => 'completed',
                'confirmed' => 'completed',
                'sending' => 'processing',
                'partially_paid' => 'pending',
                'failed' => 'failed',
                'refunded' => 'refunded',
                'expired' => 'failed',
            ];

            $newStatus = $statusMap[$paymentStatus] ?? 'pending';
            $transaction->status = $newStatus;
            $transaction->save();

            // If completed, credit user's wallet
            if ($newStatus === 'completed' && $transaction->user) {
                $transaction->user->wallet_balance += $transaction->amount;
                $transaction->user->save();

                Log::info('NOWPayments: Wallet credited', [
                    'user_id' => $transaction->user_id,
                    'amount' => $transaction->amount / 100,
                    'transaction_id' => $transaction->id,
                ]);
            }

            Log::info('NOWPayments IPN processed', [
                'payment_id' => $paymentId,
                'status' => $newStatus,
            ]);
        } catch (Exception $e) {
            Log::error('NOWPayments IPN Processing Error: ' . $e->getMessage(), [
                'data' => $data,
            ]);
        }
    }

    /**
     * Get supported USDT networks
     */
    public function getSupportedNetworks(): array
    {
        return [
            'usdttrc20' => [
                'name' => 'USDT (TRC20)',
                'network' => 'TRON',
                'fee' => 'Low',
                'recommended' => true,
            ],
            'usdterc20' => [
                'name' => 'USDT (ERC20)',
                'network' => 'Ethereum',
                'fee' => 'High',
                'recommended' => false,
            ],
            'usdtbep20' => [
                'name' => 'USDT (BEP20)',
                'network' => 'BSC',
                'fee' => 'Low',
                'recommended' => true,
            ],
        ];
    }

    /**
     * Get network from currency code
     */
    private function getCurrencyNetwork(string $currency): string
    {
        return match ($currency) {
            'usdttrc20' => 'TRC20',
            'usdterc20' => 'ERC20',
            'usdtbep20' => 'BEP20',
            default => 'Unknown',
        };
    }

    /**
     * Estimate transaction fee
     */
    public function estimateFee(string $currency, float $amount): float
    {
        // NOWPayments typically doesn't charge extra fees for deposits
        // But network fees vary
        return match ($currency) {
            'usdttrc20' => 1.0, // ~1 USDT for TRC20
            'usdterc20' => 15.0, // ~15 USDT for ERC20 (high gas)
            'usdtbep20' => 0.5, // ~0.5 USDT for BEP20
            default => 0.0,
        };
    }
}
