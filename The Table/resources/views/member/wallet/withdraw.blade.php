@extends('layouts.modern')

@section('title', 'Withdraw Funds - RoundTable')
@section('page-title', 'Withdraw Funds')

@section('content')
<div class="max-w-xl mx-auto space-y-6 slide-up">
    <!-- Back Button -->
    <a href="{{ route('wallet.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to Wallet
    </a>

    <!-- Withdraw Card -->
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-amber-400 to-amber-500 px-8 py-6">
            <h2 class="text-white font-bold text-2xl">Withdraw Funds</h2>
            <p class="text-amber-100 mt-1">Transfer money to your bank account</p>
        </div>

        <form action="{{ route('wallet.withdraw.submit') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <!-- Available Balance -->
            <div class="bg-slate-50 rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-slate-500">Available Balance</span>
                    <span class="font-bold text-emerald-600 text-xl">R{{ number_format($wallet->available_balance / 100, 2) }}</span>
                </div>
                @if($wallet->locked_balance > 0)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-400">
                        <i data-lucide="lock" class="w-4 h-4 inline mr-1"></i>
                        Locked in Cohorts
                    </span>
                    <span class="font-medium text-slate-500">R{{ number_format($wallet->locked_balance / 100, 2) }}</span>
                </div>
                @endif
            </div>

            @if($wallet->available_balance < 5000)
            <!-- Minimum Withdrawal Warning -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">
                <div class="flex items-start space-x-3">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-medium text-amber-800">Minimum Withdrawal: R50.00</p>
                        <p class="text-sm text-amber-600 mt-1">
                            You need at least R50.00 available balance to make a withdrawal.
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Amount Input -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Withdrawal Amount (ZAR)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl font-bold">R</span>
                    <input type="number" 
                           name="amount" 
                           id="amount"
                           min="50" 
                           max="{{ $wallet->available_balance / 100 }}"
                           step="0.01"
                           placeholder="0.00"
                           value="{{ old('amount') }}"
                           class="w-full pl-10 pr-4 py-4 text-2xl font-bold border-2 border-slate-200 rounded-2xl focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition"
                           required>
                </div>
                @error('amount')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quick Amount Buttons -->
            <div class="flex space-x-3">
                <button type="button" 
                        onclick="document.getElementById('amount').value = {{ ($wallet->available_balance / 100) * 0.25 }}"
                        class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    25%
                </button>
                <button type="button" 
                        onclick="document.getElementById('amount').value = {{ ($wallet->available_balance / 100) * 0.5 }}"
                        class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    50%
                </button>
                <button type="button" 
                        onclick="document.getElementById('amount').value = {{ ($wallet->available_balance / 100) * 0.75 }}"
                        class="flex-1 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    75%
                </button>
                <button type="button" 
                        onclick="document.getElementById('amount').value = {{ $wallet->available_balance / 100 }}"
                        class="flex-1 py-3 bg-amber-100 hover:bg-amber-200 text-amber-700 font-bold rounded-xl transition">
                    Max
                </button>
            </div>

            <!-- Bank Details -->
            <div class="space-y-4">
                <h4 class="font-bold text-slate-700">Bank Details</h4>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-2">Bank Name</label>
                    <select name="bank_name" 
                            class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition bg-white"
                            required>
                        <option value="">Select your bank</option>
                        <option value="fnb" {{ old('bank_name') == 'fnb' ? 'selected' : '' }}>First National Bank (FNB)</option>
                        <option value="standard" {{ old('bank_name') == 'standard' ? 'selected' : '' }}>Standard Bank</option>
                        <option value="absa" {{ old('bank_name') == 'absa' ? 'selected' : '' }}>ABSA</option>
                        <option value="nedbank" {{ old('bank_name') == 'nedbank' ? 'selected' : '' }}>Nedbank</option>
                        <option value="capitec" {{ old('bank_name') == 'capitec' ? 'selected' : '' }}>Capitec Bank</option>
                        <option value="discovery" {{ old('bank_name') == 'discovery' ? 'selected' : '' }}>Discovery Bank</option>
                        <option value="tymebank" {{ old('bank_name') == 'tymebank' ? 'selected' : '' }}>TymeBank</option>
                        <option value="africanbank" {{ old('bank_name') == 'africanbank' ? 'selected' : '' }}>African Bank</option>
                        <option value="other" {{ old('bank_name') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('bank_name')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-2">Account Holder Name</label>
                    <input type="text" 
                           name="account_name" 
                           value="{{ old('account_name', auth()->user()->full_name) }}"
                           placeholder="Name as it appears on your account"
                           class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition"
                           required>
                    @error('account_name')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-2">Account Number</label>
                    <input type="text" 
                           name="account_number"
                           value="{{ old('account_number') }}"
                           placeholder="Enter your account number"
                           pattern="[0-9]{6,16}"
                           class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition"
                           required>
                    @error('account_number')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-2">Branch Code</label>
                    <input type="text" 
                           name="branch_code"
                           value="{{ old('branch_code') }}"
                           placeholder="6-digit branch code"
                           pattern="[0-9]{6}"
                           class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition"
                           required>
                    @error('branch_code')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-600 mb-2">Account Type</label>
                    <div class="flex space-x-4">
                        <label class="flex-1 flex items-center justify-center p-3 bg-slate-50 rounded-xl cursor-pointer border-2 border-transparent has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50 transition">
                            <input type="radio" name="account_type" value="savings" class="sr-only" {{ old('account_type', 'savings') == 'savings' ? 'checked' : '' }}>
                            <span class="font-medium text-slate-700">Savings</span>
                        </label>
                        <label class="flex-1 flex items-center justify-center p-3 bg-slate-50 rounded-xl cursor-pointer border-2 border-transparent has-[:checked]:border-amber-400 has-[:checked]:bg-amber-50 transition">
                            <input type="radio" name="account_type" value="current" class="sr-only" {{ old('account_type') == 'current' ? 'checked' : '' }}>
                            <span class="font-medium text-slate-700">Current</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="bg-slate-50 rounded-2xl p-4">
                <div class="flex items-start space-x-3">
                    <i data-lucide="shield-check" class="w-5 h-5 text-slate-400 flex-shrink-0 mt-0.5"></i>
                    <div class="text-sm text-slate-500">
                        <p class="font-medium text-slate-700 mb-1">Secure Withdrawal</p>
                        <p>Your withdrawal request will be reviewed by our team. Funds are typically transferred within 1-3 business days.</p>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" 
                    {{ $wallet->available_balance < 5000 ? 'disabled' : '' }}
                    class="w-full py-4 bg-gradient-to-r from-amber-400 to-amber-500 text-white font-bold text-lg rounded-2xl shadow-lg shadow-amber-200 hover:shadow-xl hover:shadow-amber-300 transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
                Submit Withdrawal Request
            </button>

            <p class="text-center text-slate-400 text-sm">
                Withdrawals are processed within 1-3 business days
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
