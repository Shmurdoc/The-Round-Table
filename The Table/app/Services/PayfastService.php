<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PayfastService
{
    private $merchantId;
    private $merchantKey;
    private $passphrase;
    private $testMode;
    private $pfHost;

    public function __construct()
    {
        $this->merchantId = config('services.payfast.merchant_id');
        $this->merchantKey = config('services.payfast.merchant_key');
        $this->passphrase = config('services.payfast.passphrase');
        $this->testMode = config('services.payfast.test_mode', true);
        $this->pfHost = $this->testMode ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';
    }

    /**
     * Generate payment form data for PayFast
     */
    public function generatePaymentData(array $data)
    {
        $paymentData = [
            'merchant_id' => $this->merchantId,
            'merchant_key' => $this->merchantKey,
            'return_url' => $data['return_url'],
            'cancel_url' => $data['cancel_url'],
            'notify_url' => $data['notify_url'],
            
            // Transaction details
            'name_first' => $data['name_first'],
            'name_last' => $data['name_last'],
            'email_address' => $data['email'],
            'cell_number' => $data['phone'] ?? '',
            
            // Payment details
            'm_payment_id' => $data['payment_id'],
            'amount' => number_format($data['amount'], 2, '.', ''),
            'item_name' => $data['item_name'],
            'item_description' => $data['item_description'] ?? '',
            
            // Custom fields
            'custom_str1' => $data['custom_str1'] ?? '',
            'custom_str2' => $data['custom_str2'] ?? '',
            'custom_int1' => $data['custom_int1'] ?? '',
            
            // Email confirmation
            'email_confirmation' => 1,
            'confirmation_address' => $data['email'],
        ];

        // Generate signature
        $paymentData['signature'] = $this->generateSignature($paymentData);

        return $paymentData;
    }

    /**
     * Generate PayFast signature
     */
    private function generateSignature(array $data, $passPhrase = null)
    {
        // Remove signature if it exists
        if (isset($data['signature'])) {
            unset($data['signature']);
        }

        // Sort the array by key alphabetically
        ksort($data);

        // Create parameter string
        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(stripslashes($val)) . '&';
            }
        }

        // Remove last ampersand
        $getString = substr($pfOutput, 0, -1);
        
        if ($passPhrase !== null || $this->passphrase !== null) {
            $getString .= '&passphrase=' . urlencode($passPhrase ?? $this->passphrase);
        }

        return md5($getString);
    }

    /**
     * Validate PayFast callback/webhook
     */
    public function validateCallback(array $pfData, array $pfParamString)
    {
        // Check if signature is valid
        $signature = $pfData['signature'];
        unset($pfData['signature']);
        
        $validSignature = $this->generateSignature($pfData);
        
        if ($signature !== $validSignature) {
            Log::error('PayFast: Invalid signature');
            return false;
        }

        // Verify IP address (PayFast servers)
        $validHosts = [
            'www.payfast.co.za',
            'sandbox.payfast.co.za',
            'w1w.payfast.co.za',
            'w2w.payfast.co.za',
        ];

        $validIps = [];
        foreach ($validHosts as $pfHostname) {
            $ips = gethostbynamel($pfHostname);
            if ($ips !== false) {
                $validIps = array_merge($validIps, $ips);
            }
        }

        $validIps = array_unique($validIps);
        $referrerIp = request()->ip();

        if (!in_array($referrerIp, $validIps) && !$this->testMode) {
            Log::error('PayFast: Invalid IP address', ['ip' => $referrerIp]);
            return false;
        }

        // Verify payment amount
        /** @var \Illuminate\Http\Client\Response $response */
        $response = Http::asForm()->post("https://{$this->pfHost}/eng/query/validate", $pfData);
        
        if ($response->body() !== 'VALID') {
            Log::error('PayFast: Server validation failed', ['response' => $response->body()]);
            return false;
        }

        return true;
    }

    /**
     * Get PayFast payment URL
     */
    public function getPaymentUrl()
    {
        return "https://{$this->pfHost}/eng/process";
    }

    /**
     * Check payment status
     */
    public function getPaymentStatus($paymentId)
    {
        // PayFast doesn't have a direct API to check status
        // Status is received via ITN (Instant Transaction Notification)
        // This method is for internal tracking only
        return [
            'status' => 'pending',
            'message' => 'Payment status will be updated via webhook'
        ];
    }

    /**
     * Generate PayFast payment form HTML
     */
    public function generatePaymentForm(array $data)
    {
        $paymentData = $this->generatePaymentData($data);
        $actionUrl = $this->getPaymentUrl();

        $html = '<form id="payfast-form" action="' . $actionUrl . '" method="POST">';
        
        foreach ($paymentData as $key => $value) {
            $html .= '<input type="hidden" name="' . $key . '" value="' . htmlspecialchars($value) . '">';
        }
        
        $html .= '<button type="submit" class="btn btn-success btn-lg w-100">';
        $html .= '<i class="fas fa-credit-card"></i> Pay with PayFast';
        $html .= '</button>';
        $html .= '</form>';

        return $html;
    }
}
