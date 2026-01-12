<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\CryptoPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CryptoPaymentController extends Controller
{
    public function __construct(
        private CryptoPaymentService $cryptoService
    ) {}

    /**
     * Show crypto deposit page
     */
    public function depositPage()
    {
        $user = Auth::user();
        
        $supportedCurrencies = [
            'USDT' => ['name' => 'Tether (USDT)', 'networks' => ['TRC20', 'ERC20', 'BEP20']],
            'USDC' => ['name' => 'USD Coin (USDC)', 'networks' => ['ERC20', 'POLYGON', 'BEP20']],
            'BTC' => ['name' => 'Bitcoin', 'networks' => ['BTC']],
            'ETH' => ['name' => 'Ethereum', 'networks' => ['ETH']],
            'BNB' => ['name' => 'Binance Coin', 'networks' => ['BSC']],
        ];

        return view('crypto.deposit', [
            'currencies' => $supportedCurrencies,
            'userTier' => $user->tier ?? 1,
        ]);
    }

    /**
     * Generate deposit address
     */
    public function generateAddress(Request $request)
    {
        $request->validate([
            'currency' => 'required|in:USDT,USDC,BTC,ETH,BNB',
            'network' => 'required|string',
        ]);

        $user = Auth::user();
        
        try {
            $addressData = $this->cryptoService->generateDepositAddress(
                $user,
                $request->currency,
                $request->network
            );

            return response()->json([
                'success' => true,
                'data' => $addressData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Show crypto withdrawal page
     */
    public function withdrawalPage()
    {
        $user = Auth::user();
        
        // Get user's crypto balances
        $wallets = $user->cryptoWallets()
            ->where('balance', '>', 0)
            ->where('status', 'active')
            ->get();

        return view('crypto.withdrawal', [
            'wallets' => $wallets,
            'userTier' => $user->tier ?? 1,
        ]);
    }

    /**
     * Process withdrawal request
     */
    public function processWithdrawal(Request $request)
    {
        $request->validate([
            'currency' => 'required|in:USDT,USDC,BTC,ETH,BNB',
            'network' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'destination_address' => 'required|string',
            'two_fa_code' => 'required|string', // 2FA verification
        ]);

        $user = Auth::user();
        
        // Verify 2FA code
        if (!$this->verify2FA($user, $request->two_fa_code)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid 2FA code',
            ], 401);
        }

        try {
            $result = $this->cryptoService->processWithdrawal(
                $user,
                (float) $request->amount,
                $request->currency,
                $request->destination_address,
                $request->network
            );

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get real-time crypto rates
     */
    public function getRates(Request $request)
    {
        $currency = $request->query('currency', 'USDT');
        
        try {
            $rate = $this->cryptoService->getCryptoRate($currency, 'ZAR');
            
            return response()->json([
                'success' => true,
                'currency' => $currency,
                'rate' => $rate,
                'updated_at' => now()->toISOString(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch rates',
            ], 500);
        }
    }

    /**
     * Webhook for incoming deposits (from payment processor)
     */
    public function depositWebhook(Request $request)
    {
        // Verify webhook signature
        if (!$this->verifyWebhookSignature($request)) {
            return response()->json(['error' => 'Invalid signature'], 403);
        }

        try {
            $transaction = $this->cryptoService->processDeposit(
                $request->input('address'),
                (float) $request->input('amount'),
                $request->input('currency'),
                $request->input('tx_hash')
            );

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get transaction history
     */
    public function transactionHistory()
    {
        $user = Auth::user();
        
        $transactions = $user->transactions()
            ->whereIn('type', ['crypto_deposit', 'crypto_withdrawal'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('crypto.history', [
            'transactions' => $transactions,
        ]);
    }

    // Helper methods

    private function verify2FA($user, string $code): bool
    {
        // Implement 2FA verification (Google Authenticator, SMS, etc.)
        // Placeholder for now
        return true;
    }

    private function verifyWebhookSignature(Request $request): bool
    {
        // Verify webhook signature from payment processor
        // Placeholder for now
        return true;
    }
}
