@extends('layouts.modern')

@section('title', 'Deposit Funds - RoundTable')
@section('page-title', 'Deposit Funds')

@section('content')
<div class="max-w-xl mx-auto space-y-6 slide-up">
    <!-- Back Button -->
    <a href="{{ route('wallet.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to Wallet
    </a>

    <!-- Deposit Card -->
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-400 to-emerald-500 px-8 py-6">
            <h2 class="text-white font-bold text-2xl">Deposit Funds</h2>
            <p class="text-emerald-100 mt-1">Add money to your RoundTable wallet</p>
        </div>

        <form action="{{ route('wallet.deposit.submit') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf

            <!-- Current Balance -->
            <div class="bg-slate-50 rounded-2xl p-4 flex items-center justify-between">
                <span class="text-slate-500">Current Balance</span>
                <span class="font-bold text-slate-900 text-xl">R{{ number_format($wallet->balance / 100, 2) }}</span>
            </div>

            <!-- Amount Input -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Deposit Amount (USDT)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl font-bold">$</span>
                    <input type="number" 
                           name="amount" 
                           id="amount"
                           min="50" 
                           step="0.01"
                           placeholder="0.00"
                           value="{{ old('amount') }}"
                           class="w-full pl-10 pr-4 py-4 text-2xl font-bold border-2 border-slate-200 rounded-2xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 transition"
                           required>
                </div>
                @error('amount')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p class="text-slate-400 text-sm mt-2">Minimum deposit: $10 USDT</p>
            </div>

            <!-- Quick Amount Buttons -->
            <div class="grid grid-cols-4 gap-3">
                @foreach([50, 100, 500, 1000] as $quickAmount)
                <button type="button" 
                        onclick="document.getElementById('amount').value = {{ $quickAmount }}"
                        class="py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    ${{ number_format($quickAmount, 0) }}
                </button>
                @endforeach
            </div>

            <!-- Payment Method -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Cryptocurrency Network</label>
                <div class="space-y-3">
                    <label class="flex items-center p-4 bg-slate-50 rounded-2xl cursor-pointer border-2 border-transparent has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 transition">
                        <input type="radio" name="payment_method" value="usdttrc20" class="sr-only" checked>
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mr-4 shadow-sm">
                            <svg class="w-7 h-7 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-slate-900">USDT (TRC20)</p>
                            <p class="text-sm text-slate-500">Tron network - Lowest fees (Recommended)</p>
                        </div>
                        <div class="w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center payment-check">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full hidden"></div>
                        </div>
                    </label>

                    <label class="flex items-center p-4 bg-slate-50 rounded-2xl cursor-pointer border-2 border-transparent has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 transition">
                        <input type="radio" name="payment_method" value="usdtbep20" class="sr-only">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mr-4 shadow-sm">
                            <svg class="w-7 h-7 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-slate-900">USDT (BEP20)</p>
                            <p class="text-sm text-slate-500">Binance Smart Chain - Fast & affordable</p>
                        </div>
                        <div class="w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center payment-check">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full hidden"></div>
                        </div>
                    </label>

                    <label class="flex items-center p-4 bg-slate-50 rounded-2xl cursor-pointer border-2 border-transparent has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 transition">
                        <input type="radio" name="payment_method" value="usdterc20" class="sr-only">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mr-4 shadow-sm">
                            <svg class="w-7 h-7 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <p class="font-bold text-slate-900">USDT (ERC20)</p>
                            <p class="text-sm text-slate-500">Ethereum network - Most compatible</p>
                        </div>
                        <div class="w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center payment-check">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full hidden"></div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Crypto Payment Info -->
            <div id="crypto-info" class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border border-emerald-200">
                <h4 class="font-bold text-emerald-800 mb-4 flex items-center">
                    <i data-lucide="info" class="w-5 h-5 mr-2"></i>
                    Crypto Deposit Instructions
                </h4>
                <div class="space-y-4">
                    <div class="bg-white/70 rounded-xl p-4 border border-emerald-100">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="text-emerald-600 font-bold text-sm">1</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-900 mb-1">Enter Deposit Amount</p>
                                <p class="text-xs text-slate-600">Specify how much USDT you want to deposit (minimum $10)</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/70 rounded-xl p-4 border border-emerald-100">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="text-emerald-600 font-bold text-sm">2</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-900 mb-1">Select Network</p>
                                <p class="text-xs text-slate-600">Choose TRC20 (recommended), BEP20, or ERC20 network</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/70 rounded-xl p-4 border border-emerald-100">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                <span class="text-emerald-600 font-bold text-sm">3</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-bold text-slate-900 mb-1">Complete Payment</p>
                                <p class="text-xs text-slate-600">You'll receive a unique wallet address to send your USDT</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mt-4">
                    <p class="text-amber-800 text-xs flex items-start">
                        <i data-lucide="alert-triangle" class="w-4 h-4 mr-2 mt-0.5 flex-shrink-0"></i>
                        <span><strong>Important:</strong> Ensure you select the correct network. Sending USDT on the wrong network may result in permanent loss of funds.</span>
                    </p>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Additional Notes (Optional)</label>
                <textarea name="notes" 
                          rows="3"
                          placeholder="Any additional information about your deposit..."
                          class="w-full px-4 py-3 border-2 border-slate-200 rounded-2xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 transition resize-none">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit -->
            <button type="submit" 
                    class="w-full py-4 bg-gradient-to-r from-emerald-400 to-emerald-500 text-white font-bold text-lg rounded-2xl shadow-lg shadow-emerald-200 hover:shadow-xl hover:shadow-emerald-300 transition-all transform hover:scale-[1.02]">
                Continue to Crypto Payment
            </button>

            <p class="text-center text-slate-400 text-sm">
                Deposits are credited automatically after blockchain confirmation
            </p>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
