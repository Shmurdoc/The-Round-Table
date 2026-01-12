@extends('layouts.modern')

@section('title', 'Transfer to Cohort - RoundTable')
@section('page-title', 'Transfer to Cohort')

@section('content')
<div class="max-w-xl mx-auto space-y-6 slide-up">
    <!-- Back Button -->
    <a href="{{ route('wallet.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to Wallet
    </a>

    <!-- Transfer Card -->
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-8 py-6">
            <h2 class="text-white font-bold text-2xl">Transfer to Cohort</h2>
            <p class="text-purple-100 mt-1">Move funds from your wallet to a cohort project</p>
        </div>

        <form action="{{ route('wallet.transfer.submit') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <!-- Available Balance -->
            <div class="bg-slate-50 rounded-2xl p-4 flex items-center justify-between">
                <span class="text-slate-500">Available Balance</span>
                <span class="font-bold text-emerald-600 text-xl">R{{ number_format($wallet->available_balance / 100, 2) }}</span>
            </div>

            <!-- Select Cohort -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Select Cohort Project</label>
                <div class="space-y-3" id="cohort-list">
                    @forelse($cohorts as $cohort)
                    <label class="block p-4 bg-slate-50 rounded-2xl cursor-pointer border-2 border-transparent has-[:checked]:border-purple-400 has-[:checked]:bg-purple-50 transition hover:bg-slate-100">
                        <input type="radio" 
                               name="cohort_id" 
                               value="{{ $cohort->id }}" 
                               data-min="{{ $cohort->minimum_contribution }}"
                               data-max="{{ $cohort->maximum_contribution ?? 'unlimited' }}"
                               class="sr-only cohort-radio"
                               {{ old('cohort_id') == $cohort->id ? 'checked' : '' }}>
                        <div class="flex items-center space-x-4">
                            <div class="w-14 h-14 rounded-xl flex items-center justify-center text-white font-bold text-lg bg-gradient-to-br from-purple-400 to-purple-500">
                                {{ substr($cohort->name, 0, 2) }}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-slate-900">{{ $cohort->name }}</h4>
                                <p class="text-sm text-slate-500">{{ $cohort->category ?? 'General' }}</p>
                                <div class="flex items-center space-x-4 mt-2 text-xs">
                                    <span class="text-emerald-600 font-bold">
                                        {{ $cohort->expected_return }}% Expected Return
                                    </span>
                                    <span class="text-slate-400">
                                        Min: R{{ number_format($cohort->minimum_contribution / 100, 0) }}
                                    </span>
                                    @if($cohort->maximum_contribution)
                                    <span class="text-slate-400">
                                        Max: R{{ number_format($cohort->maximum_contribution / 100, 0) }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center">
                                <div class="w-3 h-3 bg-purple-500 rounded-full cohort-check hidden"></div>
                            </div>
                        </div>
                    </label>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                        </div>
                        <p class="text-slate-500">No active cohorts available</p>
                        <a href="{{ route('cohorts.index') }}" class="inline-block mt-4 text-sm font-bold text-purple-600">
                            Browse All Cohorts
                        </a>
                    </div>
                    @endforelse
                </div>
                @error('cohort_id')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            @if($cohorts->count() > 0)
            <!-- Amount Input -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Contribution Amount (ZAR)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl font-bold">R</span>
                    <input type="number" 
                           name="amount" 
                           id="amount"
                           min="100" 
                           max="{{ $wallet->available_balance / 100 }}"
                           step="0.01"
                           placeholder="0.00"
                           value="{{ old('amount') }}"
                           class="w-full pl-10 pr-4 py-4 text-2xl font-bold border-2 border-slate-200 rounded-2xl focus:border-purple-400 focus:ring-4 focus:ring-purple-100 transition"
                           required>
                </div>
                @error('amount')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p class="text-slate-400 text-sm mt-2" id="amount-hint">Select a cohort to see minimum contribution</p>
            </div>

            <!-- Important Notice -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                <h4 class="font-bold text-amber-800 mb-3 flex items-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
                    Important Information
                </h4>
                <ul class="space-y-2 text-sm text-amber-700">
                    <li class="flex items-start">
                        <i data-lucide="clock" class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5"></i>
                        <span>Funds will be automatically locked 24 hours after transfer or when an admin locks the cohort.</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="lock" class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5"></i>
                        <span>Once locked, you cannot withdraw until the cohort's maturity date.</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="trending-up" class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5"></i>
                        <span>Profits are calculated daily and added to your partnership value.</span>
                    </li>
                </ul>
            </div>

            <!-- Transfer Preview -->
            <div id="transfer-preview" class="hidden bg-purple-50 rounded-2xl p-5">
                <h4 class="font-bold text-purple-800 mb-3">Transfer Preview</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-purple-600">Transfer Amount:</span>
                        <span class="font-bold text-purple-900" id="preview-amount">R0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-purple-600">To Cohort:</span>
                        <span class="font-bold text-purple-900" id="preview-cohort">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-purple-600">Remaining Balance:</span>
                        <span class="font-bold text-purple-900" id="preview-remaining">R{{ number_format($wallet->available_balance / 100, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" 
                    class="w-full py-4 bg-gradient-to-r from-purple-500 to-purple-600 text-white font-bold text-lg rounded-2xl shadow-lg shadow-purple-200 hover:shadow-xl hover:shadow-purple-300 transition-all transform hover:scale-[1.02]">
                Transfer to Cohort
            </button>

            <p class="text-center text-slate-400 text-sm">
                Your funds will start earning returns immediately
            </p>
            @endif
        </form>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-100">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="shield" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <h4 class="font-bold text-slate-900">Secure Partnership</h4>
            </div>
            <p class="text-sm text-slate-500">Your contributions are protected and tracked transparently throughout the cohort lifecycle.</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100">
            <div class="flex items-center space-x-3 mb-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-purple-600"></i>
                </div>
                <h4 class="font-bold text-slate-900">Daily Returns</h4>
            </div>
            <p class="text-sm text-slate-500">Profits are calculated and applied daily based on the cohort's performance and profit rate.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    const amountInput = document.getElementById('amount');
    const previewSection = document.getElementById('transfer-preview');
    const walletBalance = {{ $wallet->available_balance }};

    // Handle cohort selection
    document.querySelectorAll('.cohort-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            // Update checkmarks
            document.querySelectorAll('.cohort-check').forEach(check => {
                check.classList.add('hidden');
            });
            this.closest('label').querySelector('.cohort-check').classList.remove('hidden');

            // Update amount hint
            const min = this.dataset.min;
            const max = this.dataset.max;
            const hint = document.getElementById('amount-hint');
            
            if (max === 'unlimited') {
                hint.textContent = `Minimum contribution: R${(min/100).toLocaleString()}`;
            } else {
                hint.textContent = `Min: R${(min/100).toLocaleString()} | Max: R${(max/100).toLocaleString()}`;
            }

            // Set minimum on input
            amountInput.min = min / 100;
            if (max !== 'unlimited') {
                amountInput.max = Math.min(max / 100, walletBalance / 100);
            }

            // Update preview
            updatePreview();
        });
    });

    // Handle amount change
    amountInput.addEventListener('input', updatePreview);

    function updatePreview() {
        const amount = parseFloat(amountInput.value) || 0;
        const selectedCohort = document.querySelector('.cohort-radio:checked');
        
        if (amount > 0 && selectedCohort) {
            previewSection.classList.remove('hidden');
            document.getElementById('preview-amount').textContent = 'R' + amount.toLocaleString('en-ZA', {minimumFractionDigits: 2});
            document.getElementById('preview-cohort').textContent = selectedCohort.closest('label').querySelector('h4').textContent;
            document.getElementById('preview-remaining').textContent = 'R' + ((walletBalance/100) - amount).toLocaleString('en-ZA', {minimumFractionDigits: 2});
        } else {
            previewSection.classList.add('hidden');
        }
    }
</script>
@endpush
