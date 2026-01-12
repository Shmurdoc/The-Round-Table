<?php

namespace App\Http\Controllers;

use App\Services\NOWPaymentsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Transaction;
use App\Models\CohortMember;
use App\Models\Cohort;

class NOWPaymentsController extends Controller
{
    protected $nowPayments;

    public function __construct(NOWPaymentsService $nowPayments)
    {
        $this->nowPayments = $nowPayments;
    }

    /**
     * Handle IPN webhook from NOWPayments
     * This is called when a payment is received/confirmed
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        try {
            // Log the incoming webhook for debugging
            Log::info('NOWPayments IPN received', [
                'payload' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Verify IPN signature to ensure it's from NOWPayments
            $signature = $request->header('x-nowpayments-sig');
            if (!$this->nowPayments->verifyIPNSignature($request->all(), $signature)) {
                Log::warning('NOWPayments IPN signature verification failed', [
                    'received_signature' => $signature,
                    'payload' => $request->all()
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid signature'
                ], 401);
            }

            // Process the payment
            $result = $this->nowPayments->processIPNCallback($request->all());

            if ($result['success']) {
                Log::info('NOWPayments IPN processed successfully', [
                    'payment_id' => $request->input('payment_id'),
                    'amount' => $request->input('actually_paid'),
                    'user_id' => $result['user_id'] ?? null
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully'
                ], 200);
            } else {
                Log::error('NOWPayments IPN processing failed', [
                    'error' => $result['error'],
                    'payment_id' => $request->input('payment_id')
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $result['error']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('NOWPayments webhook exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    /**
     * Initiate USDT payment for joining a cohort
     * Called from join-usdt.blade.php form submission
     */
    public function initiatePayment(Request $request): JsonResponse
    {
        $request->validate([
            'cohort_id' => 'required|exists:cohorts,id',
            'amount' => 'required|numeric|min:100',
            'network' => 'required|in:trc20,bep20,erc20'
        ]);

        try {
            $user = auth()->user();
            $cohort = Cohort::findOrFail($request->cohort_id);
            $amountCents = (int)($request->amount * 100);

            // Create pending transaction record
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'cohort_id' => $cohort->id,
                'type' => 'deposit',
                'amount' => $amountCents,
                'status' => 'pending',
                'payment_method' => 'usdt_' . $request->network,
                'description' => "Partnership contribution to {$cohort->name}",
                'metadata' => [
                    'network' => $request->network,
                    'payment_provider' => 'nowpayments'
                ]
            ]);

            // Create payment with NOWPayments
            $payment = $this->nowPayments->createPayment(
                $request->amount,
                'USDTTRC20', // Currency code - adjust based on network
                $transaction->id // Order ID for tracking
            );

            if ($payment['success']) {
                // Update transaction with payment details
                $transaction->update([
                    'payment_reference' => $payment['payment_id'],
                    'metadata' => array_merge($transaction->metadata ?? [], [
                        'nowpayments_id' => $payment['payment_id'],
                        'pay_address' => $payment['pay_address'],
                        'pay_amount' => $payment['pay_amount']
                    ])
                ]);

                Log::info('NOWPayments payment initiated', [
                    'user_id' => $user->id,
                    'transaction_id' => $transaction->id,
                    'payment_id' => $payment['payment_id']
                ]);

                return response()->json([
                    'success' => true,
                    'payment_id' => $payment['payment_id'],
                    'pay_address' => $payment['pay_address'],
                    'pay_amount' => $payment['pay_amount'],
                    'transaction_id' => $transaction->id,
                    'message' => 'Payment address generated. Please send USDT to complete your partnership contribution.'
                ]);

            } else {
                $transaction->update(['status' => 'failed']);
                
                Log::error('NOWPayments payment creation failed', [
                    'error' => $payment['error'],
                    'transaction_id' => $transaction->id
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $payment['error']
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment initiation exception', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment. Please try again.'
            ], 500);
        }
    }

    /**
     * Check payment status
     * Called via AJAX to check if payment has been confirmed
     */
    public function checkPaymentStatus(Request $request): JsonResponse
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id'
        ]);

        try {
            $transaction = Transaction::findOrFail($request->transaction_id);

            // Ensure user owns this transaction
            if ($transaction->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $paymentId = $transaction->metadata['nowpayments_id'] ?? null;
            if (!$paymentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment ID not found'
                ], 404);
            }

            // Get payment status from NOWPayments
            $status = $this->nowPayments->getPaymentStatus($paymentId);

            if ($status['success']) {
                return response()->json([
                    'success' => true,
                    'status' => $status['payment_status'],
                    'transaction_status' => $transaction->status,
                    'message' => $this->getStatusMessage($status['payment_status'])
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to check payment status'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment status check exception', [
                'error' => $e->getMessage(),
                'transaction_id' => $request->transaction_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to check payment status'
            ], 500);
        }
    }

    /**
     * Get user-friendly status message
     */
    private function getStatusMessage(string $status): string
    {
        return match($status) {
            'waiting' => 'Waiting for your USDT payment...',
            'confirming' => 'Payment received! Confirming on blockchain...',
            'confirmed' => 'Payment confirmed! Processing your partnership...',
            'finished' => 'Payment complete! Welcome to the partnership!',
            'failed' => 'Payment failed. Please try again.',
            'expired' => 'Payment expired. Please create a new payment.',
            default => 'Checking payment status...'
        };
    }
}
