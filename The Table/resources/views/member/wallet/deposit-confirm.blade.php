@extends('layouts.modern')

@section('title', 'Complete Deposit - RoundTable')
@section('page-title', 'Complete Your Deposit')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 slide-up">
    <!-- Back Button -->
    <a href="{{ route('wallet.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to Wallet
    </a>

    <!-- Status Banner -->
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-200 rounded-2xl p-6">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-6 h-6 text-white"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-amber-900 text-lg mb-1">Payment Pending</h3>
                <p class="text-amber-700 text-sm">Complete your USDT transfer to finalize this deposit</p>
            </div>
        </div>
    </div>

    <!-- Payment Details Card -->
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-white font-bold text-2xl mb-1">Send USDT Payment</h2>
                    <p class="text-emerald-100 text-sm">Transaction ID: {{ $transaction->transaction_id }}</p>
                </div>
                <div class="text-right">
                    <div class="text-white/80 text-xs mb-1">Amount</div>
                    <div class="text-white font-bold text-3xl font-mono">${{ number_format($transaction->amount / 100, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="p-8 space-y-6">
            <!-- Network Info -->
            <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl p-6 border border-slate-200">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-900">Network</h3>
                    @php
                        $networkName = strtoupper(str_replace('usdt', 'USDT ', $transaction->payment_method));
                        $networkColor = match($transaction->payment_method) {
                            'usdttrc20' => 'emerald',
                            'usdtbep20' => 'amber',
                            'usdterc20' => 'indigo',
                            default => 'slate'
                        };
                    @endphp
                    <span class="px-3 py-1.5 bg-{{ $networkColor }}-100 text-{{ $networkColor }}-700 text-sm font-bold rounded-lg border border-{{ $networkColor }}-200">
                        {{ $networkName }}
                    </span>
                </div>
                
                @if($transaction->payment_method === 'usdttrc20')
                    <div class="space-y-2 text-sm">
                        <div class="flex items-start space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-slate-600">Tron Network (TRC20)</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-slate-600">Lowest transaction fees (~$1)</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-slate-600">Fast confirmation (1-3 minutes)</span>
                        </div>
                    </div>
                @elseif($transaction->payment_method === 'usdtbep20')
                    <div class="space-y-2 text-sm">
                        <div class="flex items-start space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-slate-600">Binance Smart Chain (BEP20)</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-slate-600">Low transaction fees (~$0.50)</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-amber-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-slate-600">Fast confirmation (1-2 minutes)</span>
                        </div>
                    </div>
                @else
                    <div class="space-y-2 text-sm">
                        <div class="flex items-start space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-indigo-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-slate-600">Ethereum Network (ERC20)</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i data-lucide="info" class="w-4 h-4 text-indigo-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-slate-600">Higher fees (varies, $5-$50+)</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i data-lucide="check-circle" class="w-4 h-4 text-indigo-500 mt-0.5 flex-shrink-0"></i>
                            <span class="text-slate-600">Most widely supported</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Wallet Address -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Send USDT to this address</label>
                <div class="bg-slate-900 rounded-2xl p-6 relative">
                    <div class="absolute top-4 right-4">
                        <button onclick="copyAddress()" class="px-3 py-1.5 bg-white/10 hover:bg-white/20 text-white text-xs font-bold rounded-lg transition flex items-center space-x-1.5">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                            <span id="copyText">Copy</span>
                        </button>
                    </div>
                    
                    @php
                        // Demo addresses - replace with actual NOWPayments addresses in production
                        $demoAddresses = [
                            'usdttrc20' => 'TYDzsYUEpvnYmQk4zGP9sWWcTEd2MiAtW6',
                            'usdtbep20' => '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb',
                            'usdterc20' => '0x742d35Cc6634C0532925a3b844Bc9e7595f0bEb'
                        ];
                        $address = $demoAddresses[$transaction->payment_method] ?? 'Address will be generated';
                    @endphp
                    
                    <div id="walletAddress" class="text-white font-mono text-sm break-all pr-20">
                        {{ $address }}
                    </div>
                </div>
                <p class="text-slate-500 text-xs mt-2">
                    <i data-lucide="shield" class="w-3 h-3 inline mr-1"></i>
                    This address is unique to your transaction and will expire in 24 hours
                </p>
            </div>

            <!-- QR Code Placeholder -->
            <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-2xl p-8 text-center border border-slate-200">
                <div class="w-48 h-48 bg-white rounded-xl mx-auto flex items-center justify-center border-4 border-slate-300">
                    <div class="text-center">
                        <i data-lucide="qr-code" class="w-20 h-20 text-slate-400 mx-auto mb-2"></i>
                        <p class="text-xs text-slate-500">QR Code</p>
                    </div>
                </div>
                <p class="text-sm text-slate-600 mt-4">Scan with your crypto wallet app</p>
            </div>

            <!-- Important Notice -->
            <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-5">
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-red-500 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-white"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-red-900 mb-2">Important Reminders</h4>
                        <ul class="space-y-1.5 text-sm text-red-800">
                            <li class="flex items-start">
                                <span class="text-red-500 mr-2">•</span>
                                <span>Send exactly <strong>${{ number_format($transaction->amount / 100, 2) }} USDT</strong> to avoid delays</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-red-500 mr-2">•</span>
                                <span>Only send USDT on <strong>{{ strtoupper(str_replace('usdt', '', $transaction->payment_method)) }}</strong> network</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-red-500 mr-2">•</span>
                                <span>Sending on wrong network will result in permanent loss of funds</span>
                            </li>
                            <li class="flex items-start">
                                <span class="text-red-500 mr-2">•</span>
                                <span>Complete payment within 24 hours or this transaction will expire</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Status Updates -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-5 border border-blue-200">
                <h4 class="font-bold text-blue-900 mb-3 flex items-center">
                    <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                    What happens next?
                </h4>
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-lg flex items-center justify-center flex-shrink-0 font-bold text-sm">1</div>
                        <div class="flex-1 pt-1">
                            <p class="text-sm font-bold text-slate-900">Send USDT</p>
                            <p class="text-xs text-slate-600">Transfer the exact amount to the address above</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-lg flex items-center justify-center flex-shrink-0 font-bold text-sm">2</div>
                        <div class="flex-1 pt-1">
                            <p class="text-sm font-bold text-slate-900">Blockchain Confirmation</p>
                            <p class="text-xs text-slate-600">Wait for network confirmation (1-10 minutes)</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-500 text-white rounded-lg flex items-center justify-center flex-shrink-0 font-bold text-sm">3</div>
                        <div class="flex-1 pt-1">
                            <p class="text-sm font-bold text-slate-900">Automatic Credit</p>
                            <p class="text-xs text-slate-600">Funds automatically added to your wallet balance</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4 pt-2">
                <a href="{{ route('wallet.index') }}" 
                   class="flex-1 py-4 px-6 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold rounded-2xl text-center shadow-lg shadow-emerald-200 hover:shadow-xl hover:shadow-emerald-300 transition-all transform hover:scale-[1.02]">
                    <i data-lucide="check-circle" class="w-5 h-5 inline-block mr-2"></i>
                    I've Sent the Payment
                </a>
                <a href="{{ route('wallet.deposit.form') }}" 
                   class="px-6 py-4 bg-white text-slate-700 font-bold rounded-2xl text-center border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-all">
                    <i data-lucide="x" class="w-5 h-5 inline-block"></i>
                </a>
            </div>

            <p class="text-center text-slate-400 text-xs">
                Need help? Contact support or check transaction status in your wallet
            </p>
        </div>
    </div>

    <!-- Transaction Details -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6">
        <h3 class="font-bold text-slate-900 mb-4">Transaction Details</h3>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-500">Transaction ID</span>
                <span class="font-mono text-slate-900">{{ $transaction->transaction_id }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-500">Amount</span>
                <span class="font-bold text-slate-900">${{ number_format($transaction->amount / 100, 2) }} USDT</span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-500">Network</span>
                <span class="font-bold text-slate-900">{{ strtoupper(str_replace('usdt', 'USDT ', $transaction->payment_method)) }}</span>
            </div>
            <div class="flex justify-between py-2 border-b border-slate-100">
                <span class="text-slate-500">Status</span>
                <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-lg">
                    Pending Payment
                </span>
            </div>
            <div class="flex justify-between py-2">
                <span class="text-slate-500">Created</span>
                <span class="text-slate-900">{{ $transaction->created_at->format('M d, Y \a\t H:i') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    function copyAddress() {
        const address = document.getElementById('walletAddress').textContent.trim();
        navigator.clipboard.writeText(address).then(() => {
            const copyText = document.getElementById('copyText');
            copyText.textContent = 'Copied!';
            setTimeout(() => {
                copyText.textContent = 'Copy';
            }, 2000);
        });
    }
</script>
@endpush
