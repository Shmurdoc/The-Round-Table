@extends('layouts.modern')

@section('title', 'Join ' . $cohort->title . ' - RoundTable')
@section('page-title', 'Join Cohort')

@section('content')
<div class="slide-up max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('cohorts.show', $cohort) }}" class="flex items-center space-x-2 text-slate-500 hover:text-slate-700 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="text-sm font-medium">Back to {{ $cohort->title }}</span>
        </a>
    </div>

    <!-- Progress Steps -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between">
            @php
                $steps = [
                    ['num' => 1, 'title' => 'Review Details', 'icon' => 'file-text'],
                    ['num' => 2, 'title' => 'Choose Amount', 'icon' => 'wallet'],
                    ['num' => 3, 'title' => 'Payment', 'icon' => 'credit-card'],
                    ['num' => 4, 'title' => 'Confirmation', 'icon' => 'check-circle'],
                ];
            @endphp
            @foreach($steps as $i => $step)
                <div class="flex items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <div id="step-indicator-{{ $step['num'] }}" 
                             class="w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300
                                    {{ $step['num'] === 1 ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                            <i data-lucide="{{ $step['icon'] }}" class="w-5 h-5"></i>
                        </div>
                        <span class="text-xs font-medium text-slate-600 mt-2 hidden md:block">{{ $step['title'] }}</span>
                    </div>
                    @if($i < count($steps) - 1)
                        <div id="step-line-{{ $step['num'] }}" class="flex-1 h-1 mx-4 bg-slate-100 rounded transition-all duration-300"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <form action="{{ route('cohorts.process-payment', $cohort) }}" method="POST" id="joinForm">
        @csrf
        
        <!-- Step 1: Review Details -->
        <div id="step-1" class="step-content">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-slate-900 to-slate-800 p-6 text-white">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-amber-500/20 text-amber-400 text-xs font-bold uppercase rounded-lg">
                            {{ ucfirst($cohort->cohort_class ?? 'Utilization') }}
                        </span>
                        <span class="px-3 py-1 bg-white/10 text-white text-xs font-medium rounded-lg">
                            {{ ucfirst(str_replace('_', ' ', $cohort->asset_type ?? 'Investment')) }}
                        </span>
                    </div>
                    <h2 class="text-2xl font-bold">{{ $cohort->title }}</h2>
                    <p class="text-slate-400 mt-2">Review the investment details before proceeding</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Investment Summary -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-slate-50 rounded-xl p-4 text-center">
                            <div class="text-xs text-slate-500 uppercase font-bold mb-1">Duration</div>
                            <div class="text-xl font-bold text-slate-900">{{ $cohort->duration_months }} Months</div>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 text-center">
                            <div class="text-xs text-slate-500 uppercase font-bold mb-1">Risk Level</div>
                            <div class="text-xl font-bold {{ $cohort->risk_level === 'low' ? 'text-emerald-600' : ($cohort->risk_level === 'moderate' ? 'text-amber-600' : 'text-red-600') }}">
                                {{ ucfirst($cohort->risk_level ?? 'Moderate') }}
                            </div>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 text-center">
                            <div class="text-xs text-slate-500 uppercase font-bold mb-1">Projected Return</div>
                            <div class="text-xl font-bold text-emerald-600">{{ $cohort->projected_annual_return ?? 15 }}% p.a.</div>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4 text-center">
                            <div class="text-xs text-slate-500 uppercase font-bold mb-1">Members</div>
                            <div class="text-xl font-bold text-slate-900">{{ $cohort->member_count ?? 0 }}</div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <h3 class="font-bold text-slate-900 mb-2">About This Investment</h3>
                        <p class="text-slate-600">{{ $cohort->description }}</p>
                    </div>

                    <!-- Fee Structure -->
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <h4 class="font-bold text-amber-900 mb-3 flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4"></i>
                            Fee Structure
                        </h4>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-amber-700">Setup Fee:</span>
                                <span class="font-bold text-amber-900">{{ ($cohort->setup_fee_percent ?? 0) / 100 }}%</span>
                            </div>
                            <div>
                                <span class="text-amber-700">Management Fee:</span>
                                <span class="font-bold text-amber-900">{{ ($cohort->management_fee_percent ?? 0) / 100 }}% p.a.</span>
                            </div>
                            <div>
                                <span class="text-amber-700">Performance Fee:</span>
                                <span class="font-bold text-amber-900">{{ ($cohort->performance_fee_percent ?? 0) / 100 }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Funding Progress -->
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-slate-700">Funding Progress</span>
                            <span class="text-sm font-bold text-slate-900">
                                R{{ number_format($cohort->current_capital / 100, 0) }} / R{{ number_format($cohort->ideal_target / 100, 0) }}
                            </span>
                        </div>
                        @php $progress = min(100, ($cohort->current_capital / max(1, $cohort->ideal_target)) * 100); @endphp
                        <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-amber-400 to-amber-500 rounded-full transition-all" 
                                 style="width: {{ $progress }}%"></div>
                        </div>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="border-t border-slate-100 pt-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="terms-agree" class="mt-1 w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500" required>
                            <span class="text-sm text-slate-600">
                                I have read and understand the investment details, risk factors, and fee structure. 
                                I understand that investments carry risk and I may lose some or all of my capital.
                            </span>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="nextStep(2)" id="step1-next" disabled
                                class="px-8 py-3 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            Continue to Amount
                            <i data-lucide="arrow-right" class="w-5 h-5 inline ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Choose Amount -->
        <div id="step-2" class="step-content hidden">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 p-6 text-white">
                    <h2 class="text-2xl font-bold">Choose Your Investment Amount</h2>
                    <p class="text-emerald-100 mt-1">Select how much you want to invest in this cohort</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Wallet Balance Info -->
                    <div class="bg-slate-50 rounded-xl p-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                                <i data-lucide="wallet" class="w-6 h-6 text-emerald-600"></i>
                            </div>
                            <div>
                                <div class="text-sm text-slate-500">Your Wallet Balance</div>
                                <div class="text-xl font-bold text-slate-900">R{{ number_format($walletBalance / 100, 2) }}</div>
                            </div>
                        </div>
                        @if($walletBalance < $cohort->min_contribution)
                            <a href="{{ route('wallet.deposit.form') }}" class="px-4 py-2 bg-amber-500 text-white text-sm font-bold rounded-lg hover:bg-amber-600 transition">
                                Deposit Funds
                            </a>
                        @endif
                    </div>

                    <!-- Amount Input -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Investment Amount</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-2xl font-bold">R</span>
                            <input type="number" 
                                   name="amount" 
                                   id="investmentAmount"
                                   min="{{ $cohort->min_contribution / 100 }}" 
                                   max="{{ min($cohort->max_contribution, $cohort->hard_cap - $cohort->current_capital) / 100 }}"
                                   step="100"
                                   value="{{ $cohort->min_contribution / 100 }}"
                                   class="w-full pl-12 pr-4 py-4 text-3xl font-bold border-2 border-slate-200 rounded-2xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 transition"
                                   required>
                        </div>
                        <div class="flex justify-between text-sm text-slate-500 mt-2">
                            <span>Min: R{{ number_format($cohort->min_contribution / 100, 0) }}</span>
                            <span>Max: R{{ number_format(min($cohort->max_contribution, $cohort->hard_cap - $cohort->current_capital) / 100, 0) }}</span>
                        </div>
                    </div>

                    <!-- Quick Amount Buttons -->
                    <div class="grid grid-cols-4 gap-3">
                        @php
                            $quickAmounts = [
                                $cohort->min_contribution / 100,
                                5000,
                                10000,
                                25000
                            ];
                        @endphp
                        @foreach($quickAmounts as $amt)
                            <button type="button" 
                                    onclick="document.getElementById('investmentAmount').value = {{ $amt }}; updateOwnership();"
                                    class="py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                                R{{ number_format($amt, 0) }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Ownership Preview -->
                    <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                        <h4 class="font-bold text-emerald-900 mb-2">Your Investment Preview</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-emerald-700">Estimated Ownership</div>
                                <div id="ownershipPreview" class="text-2xl font-bold text-emerald-900">0.00%</div>
                            </div>
                            <div>
                                <div class="text-sm text-emerald-700">Expected Annual Return</div>
                                <div id="returnPreview" class="text-2xl font-bold text-emerald-900">R0</div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Source -->
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-3">Payment Source</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 bg-slate-50 rounded-xl cursor-pointer border-2 border-transparent has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 transition {{ $walletBalance >= $cohort->min_contribution ? '' : 'opacity-50 cursor-not-allowed' }}">
                                <input type="radio" name="payment_source" value="wallet" class="sr-only" 
                                       {{ $walletBalance >= $cohort->min_contribution ? 'checked' : 'disabled' }}>
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                    <i data-lucide="wallet" class="w-6 h-6 text-emerald-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-slate-900">Use Wallet Balance</p>
                                    <p class="text-sm text-slate-500">Pay from your R{{ number_format($walletBalance / 100, 2) }} balance</p>
                                </div>
                            </label>

                            <label class="flex items-center p-4 bg-slate-50 rounded-xl cursor-pointer border-2 border-transparent has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 transition">
                                <input type="radio" name="payment_source" value="direct" class="sr-only" 
                                       {{ $walletBalance < $cohort->min_contribution ? 'checked' : '' }}>
                                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mr-4 shadow-sm">
                                    <i data-lucide="credit-card" class="w-6 h-6 text-slate-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-slate-900">Direct Payment</p>
                                    <p class="text-sm text-slate-500">Pay via EFT or card payment</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" onclick="prevStep(1)" 
                                class="px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                            <i data-lucide="arrow-left" class="w-5 h-5 inline mr-2"></i>
                            Back
                        </button>
                        <button type="button" onclick="nextStep(3)" id="step2-next"
                                class="px-8 py-3 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition disabled:opacity-50">
                            Continue to Payment
                            <i data-lucide="arrow-right" class="w-5 h-5 inline ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Payment Confirmation -->
        <div id="step-3" class="step-content hidden">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-gradient-to-r from-amber-500 to-amber-600 p-6 text-white">
                    <h2 class="text-2xl font-bold">Confirm Your Investment</h2>
                    <p class="text-amber-100 mt-1">Review and confirm your investment details</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-slate-50 rounded-xl p-6">
                        <h3 class="font-bold text-slate-900 mb-4">Order Summary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-slate-600">Investment in {{ $cohort->title }}</span>
                                <span id="summaryAmount" class="font-bold text-slate-900">R0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-600">Setup Fee ({{ ($cohort->setup_fee_percent ?? 0) / 100 }}%)</span>
                                <span id="summaryFee" class="font-medium text-slate-700">R0.00</span>
                            </div>
                            <div class="border-t border-slate-200 pt-3 flex justify-between">
                                <span class="font-bold text-slate-900">Total Payment</span>
                                <span id="summaryTotal" class="text-xl font-bold text-amber-600">R0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Investment Details -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-emerald-50 rounded-xl p-4">
                            <div class="text-sm text-emerald-700">Your Ownership Stake</div>
                            <div id="summaryOwnership" class="text-2xl font-bold text-emerald-900">0.00%</div>
                        </div>
                        <div class="bg-blue-50 rounded-xl p-4">
                            <div class="text-sm text-blue-700">Expected Exit Date</div>
                            <div class="text-xl font-bold text-blue-900">
                                {{ $cohort->expected_exit_date ? \Carbon\Carbon::parse($cohort->expected_exit_date)->format('M Y') : 'TBD' }}
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method Display -->
                    <div id="paymentMethodDisplay" class="bg-slate-50 rounded-xl p-4">
                        <div class="text-sm text-slate-500 mb-1">Payment Method</div>
                        <div class="font-bold text-slate-900">Wallet Balance</div>
                    </div>

                    <!-- Final Agreement -->
                    <div class="border-t border-slate-100 pt-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" id="final-agree" class="mt-1 w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500" required>
                            <span class="text-sm text-slate-600">
                                I confirm that I want to invest in this cohort and I authorize the payment. 
                                I understand this investment is subject to the terms and conditions of the platform.
                            </span>
                        </label>
                    </div>

                    <div class="flex justify-between">
                        <button type="button" onclick="prevStep(2)" 
                                class="px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                            <i data-lucide="arrow-left" class="w-5 h-5 inline mr-2"></i>
                            Back
                        </button>
                        <button type="submit" id="submitBtn" disabled
                                class="px-8 py-3 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <i data-lucide="check" class="w-5 h-5 inline mr-2"></i>
                            Confirm Investment
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let currentStep = 1;
    const cohortData = {
        minContribution: {{ $cohort->min_contribution }},
        maxContribution: {{ min($cohort->max_contribution, $cohort->hard_cap - $cohort->current_capital) }},
        currentCapital: {{ $cohort->current_capital }},
        idealTarget: {{ $cohort->ideal_target }},
        projectedReturn: {{ $cohort->projected_annual_return ?? 15 }},
        setupFeePercent: {{ $cohort->setup_fee_percent ?? 0 }},
        walletBalance: {{ $walletBalance }}
    };

    // Step 1 validation
    document.getElementById('terms-agree').addEventListener('change', function() {
        document.getElementById('step1-next').disabled = !this.checked;
    });

    // Step 3 validation
    document.getElementById('final-agree').addEventListener('change', function() {
        document.getElementById('submitBtn').disabled = !this.checked;
    });

    // Amount input change
    document.getElementById('investmentAmount').addEventListener('input', updateOwnership);

    function updateOwnership() {
        const amount = parseFloat(document.getElementById('investmentAmount').value) * 100 || 0;
        const newTotal = cohortData.currentCapital + amount;
        const ownership = (amount / newTotal) * 100;
        const annualReturn = (amount / 100) * (cohortData.projectedReturn / 100);
        
        document.getElementById('ownershipPreview').textContent = ownership.toFixed(2) + '%';
        document.getElementById('returnPreview').textContent = 'R' + annualReturn.toLocaleString('en-ZA', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        
        // Update summary
        const setupFee = (amount * cohortData.setupFeePercent / 10000);
        const total = amount + setupFee;
        
        document.getElementById('summaryAmount').textContent = 'R' + (amount / 100).toLocaleString('en-ZA', {minimumFractionDigits: 2});
        document.getElementById('summaryFee').textContent = 'R' + (setupFee / 100).toLocaleString('en-ZA', {minimumFractionDigits: 2});
        document.getElementById('summaryTotal').textContent = 'R' + (total / 100).toLocaleString('en-ZA', {minimumFractionDigits: 2});
        document.getElementById('summaryOwnership').textContent = ownership.toFixed(2) + '%';

        // Validate step 2
        const paymentSource = document.querySelector('input[name="payment_source"]:checked');
        const canAfford = paymentSource?.value === 'wallet' ? cohortData.walletBalance >= amount : true;
        const validAmount = amount >= cohortData.minContribution && amount <= cohortData.maxContribution;
        document.getElementById('step2-next').disabled = !(validAmount && canAfford);
    }

    function nextStep(step) {
        document.getElementById('step-' + currentStep).classList.add('hidden');
        document.getElementById('step-' + step).classList.remove('hidden');
        
        // Update indicators
        for (let i = 1; i <= 4; i++) {
            const indicator = document.getElementById('step-indicator-' + i);
            const line = document.getElementById('step-line-' + i);
            
            if (i < step) {
                indicator.className = 'w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 bg-emerald-500 text-white';
                if (line) line.className = 'flex-1 h-1 mx-4 bg-emerald-500 rounded transition-all duration-300';
            } else if (i === step) {
                indicator.className = 'w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 bg-amber-500 text-white';
            } else {
                indicator.className = 'w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 bg-slate-100 text-slate-400';
                if (line) line.className = 'flex-1 h-1 mx-4 bg-slate-100 rounded transition-all duration-300';
            }
        }
        
        currentStep = step;
        updateOwnership();
        
        // Update payment method display
        const paymentSource = document.querySelector('input[name="payment_source"]:checked');
        if (paymentSource) {
            document.getElementById('paymentMethodDisplay').innerHTML = 
                paymentSource.value === 'wallet' 
                    ? '<div class="text-sm text-slate-500 mb-1">Payment Method</div><div class="font-bold text-slate-900">Wallet Balance</div>'
                    : '<div class="text-sm text-slate-500 mb-1">Payment Method</div><div class="font-bold text-slate-900">Direct Payment (EFT/Card)</div>';
        }
    }

    function prevStep(step) {
        document.getElementById('step-' + currentStep).classList.add('hidden');
        document.getElementById('step-' + step).classList.remove('hidden');
        currentStep = step;
    }

    // Initialize
    updateOwnership();
    lucide.createIcons();
</script>
@endpush
@endsection
