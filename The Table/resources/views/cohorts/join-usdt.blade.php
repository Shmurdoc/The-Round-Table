@extends('layouts.modern')

@section('title', 'Join ' . $cohort->title . ' - RoundTable')
@section('page-title', 'Join Project Partnership')

@section('content')
<div class="slide-up max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('cohorts.show', $cohort) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900/5 hover:bg-slate-900/10 text-slate-700 rounded-xl transition-all border border-slate-200 hover:border-slate-300">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="text-sm font-semibold">Back to {{ $cohort->title }}</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form Area -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Hero Header with Image -->
            <div class="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-slate-900/10">
                @if($cohort->featured_image)
                <div class="absolute inset-0">
                    <img src="{{ asset('storage/' . $cohort->featured_image) }}" 
                         alt="{{ $cohort->title }}"
                         class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-900/95 via-indigo-900/90 to-purple-900/95"></div>
                </div>
                @else
                <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-indigo-900 to-purple-900"></div>
                @endif
                
                <div class="relative z-10 p-8">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl"></div>
                    
                    <div class="relative z-10">
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            <span class="px-3 py-1.5 bg-amber-500/20 text-amber-300 text-xs font-bold uppercase tracking-wider rounded-lg border border-amber-500/30 backdrop-blur-sm">
                                <i data-lucide="zap" class="w-3 h-3 inline-block mr-1"></i>
                                Project Partnership
                            </span>
                            <span class="px-3 py-1.5 bg-white/10 text-white text-xs font-bold rounded-lg border border-white/20 backdrop-blur-sm">
                                <i data-lucide="clock" class="w-3 h-3 inline-block mr-1"></i>
                                {{ $cohort->duration_months ?? 6 }} Months
                            </span>
                            @php
                                $riskColors = match($cohort->risk_level ?? 'moderate') {
                                    'low' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                                    'high' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                                    default => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                };
                            @endphp
                            <span class="px-3 py-1.5 {{ $riskColors }} text-xs font-bold rounded-lg border backdrop-blur-sm">
                                {{ ucfirst($cohort->risk_level ?? 'Moderate') }} Risk
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-extrabold mb-3 text-white drop-shadow-lg">Become a Project Partner</h1>
                        <p class="text-slate-200 text-lg drop-shadow">{{ $cohort->title }}</p>
                        <div class="mt-6 flex items-center gap-4">
                            <div class="flex items-center gap-2 text-white/80">
                                <i data-lucide="trending-up" class="w-4 h-4 text-emerald-400"></i>
                                <span class="text-sm font-semibold">{{ $cohort->projected_annual_return ?? '12-18' }}% Projected Return</span>
                            </div>
                            <div class="flex items-center gap-2 text-white/80">
                                <i data-lucide="users" class="w-4 h-4 text-blue-400"></i>
                                <span class="text-sm font-semibold">{{ $cohort->member_count ?? 0 }}/50 Partners</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-6">
                    @if(!in_array(Auth::user()->kyc_status, ['approved', 'verified']))
                        <!-- KYC Required Alert -->
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
                            <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="shield-alert" class="w-7 h-7 text-amber-600"></i>
                            </div>
                            <h3 class="font-bold text-amber-900 text-lg mb-2">Verification Required</h3>
                            <p class="text-sm text-amber-700 mb-6">Complete identity verification to join partnerships.</p>
                            <a href="{{ route('kyc.form') }}" class="inline-flex items-center space-x-2 px-6 py-3 bg-amber-600 text-white rounded-xl font-bold hover:bg-amber-700 transition-colors">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                                <span>Complete Verification</span>
                            </a>
                        </div>
                    @else
                        <!-- Payment Form -->
                        <form action="{{ route('cohorts.process-payment', $cohort) }}" method="POST" id="paymentForm" class="space-y-6">
                            @csrf
                            
                            <!-- Contribution Amount -->
                            <div>
                                <label class="block text-sm font-bold text-slate-900 mb-3">Your Contribution</label>
                                <div class="bg-gradient-to-br from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-xl p-5">
                                    <!-- Amount Input -->
                                    <div class="mb-4">
                                        <div class="relative">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-bold text-indigo-900">R</span>
                                            <input type="number" 
                                                   name="amount" 
                                                   id="contributionAmount"
                                                   value="{{ $cohort->min_contribution / 100 }}"
                                                   min="{{ $cohort->min_contribution / 100 }}"
                                                   max="{{ min($cohort->max_contribution, $cohort->hard_cap - $cohort->current_capital, $walletBalance) / 100 }}"
                                                   step="100"
                                                   class="w-full pl-12 pr-4 py-4 text-3xl font-mono font-bold text-indigo-900 bg-white border-2 border-indigo-300 rounded-xl focus:ring-4 focus:ring-indigo-200 focus:border-indigo-500 transition-all"
                                                   placeholder="Enter amount">
                                        </div>
                                    </div>

                                    <!-- Range Slider -->
                                    <div class="mb-4">
                                        <input type="range" 
                                               id="contributionSlider"
                                               min="{{ $cohort->min_contribution / 100 }}"
                                               max="{{ min($cohort->max_contribution, $cohort->hard_cap - $cohort->current_capital, $walletBalance) / 100 }}"
                                               value="{{ $cohort->min_contribution / 100 }}"
                                               step="100"
                                               class="w-full h-3 bg-indigo-200 rounded-lg appearance-none cursor-pointer slider">
                                    </div>

                                    <!-- Stats Row -->
                                    <div class="grid grid-cols-3 gap-3 mb-3">
                                        <div class="bg-white/70 rounded-lg p-3 text-center">
                                            <div class="text-xs text-indigo-600 font-bold mb-1">Minimum</div>
                                            <div class="text-sm font-mono font-bold text-slate-900">R{{ number_format($cohort->min_contribution / 100, 0) }}</div>
                                        </div>
                                        <div class="bg-white/70 rounded-lg p-3 text-center">
                                            <div class="text-xs text-indigo-600 font-bold mb-1">Your Wallet</div>
                                            <div class="text-sm font-mono font-bold text-slate-900">R{{ number_format($walletBalance / 100, 0) }}</div>
                                        </div>
                                        <div class="bg-white/70 rounded-lg p-3 text-center">
                                            <div class="text-xs text-indigo-600 font-bold mb-1">Max Available</div>
                                            <div class="text-sm font-mono font-bold text-slate-900">R{{ number_format(($cohort->hard_cap - $cohort->current_capital) / 100, 0) }}</div>
                                        </div>
                                    </div>

                                    <!-- Partnership Share Info -->
                                    <div class="bg-white/50 rounded-lg px-3 py-2 border border-indigo-100">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="text-xs text-indigo-700 font-bold mb-1">Your Partnership Share</div>
                                                <div class="text-xs text-slate-600">
                                                    Proportional partnership rights in this project
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-2xl font-bold text-indigo-900" id="partnershipPercentage">
                                                    {{ number_format(($cohort->min_contribution / ($cohort->current_capital + $cohort->min_contribution)) * 100, 2) }}%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Source Selection -->
                            <div>
                                <label class="block text-sm font-bold text-slate-900 mb-3">Payment Method</label>
                                
                                @php
                                    $userWallet = Auth::user()->getOrCreateWallet();
                                    $walletBalance = $userWallet->balance;
                                    $hasEnoughBalance = $walletBalance >= $cohort->min_contribution;
                                @endphp

                                <!-- Wallet Payment Option -->
                                <label class="relative cursor-pointer mb-3 block">
                                    <input type="radio" name="payment_source" value="wallet" class="peer sr-only" {{ $hasEnoughBalance ? 'checked' : '' }} id="walletPayment">
                                    <div class="p-5 rounded-xl border-2 border-slate-200 bg-white peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 transition-all hover:border-slate-300 {{ !$hasEnoughBalance ? 'opacity-50' : '' }}">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                                                    <i data-lucide="wallet" class="w-7 h-7 text-emerald-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900 flex items-center gap-2">
                                                        Pay from Wallet
                                                        @if($hasEnoughBalance)
                                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded">
                                                                Instant
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-slate-500">
                                                        Available: R{{ number_format($walletBalance / 100, 2) }}
                                                        @if(!$hasEnoughBalance)
                                                            <span class="text-red-600 font-bold ml-1">(Insufficient)</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-xs text-slate-500">No fees</div>
                                        </div>
                                    </div>
                                    <div class="absolute top-5 right-5 hidden peer-checked:block">
                                        <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                    </div>
                                </label>

                                @if(!$hasEnoughBalance)
                                    <div class="mb-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                                        <p class="text-xs text-amber-700">
                                            <i data-lucide="info" class="w-3.5 h-3.5 inline mr-1"></i>
                                            Need to <a href="{{ route('wallet.index') }}" class="font-bold text-amber-800 hover:text-amber-900 underline">add funds to your wallet</a>? You need at least R{{ number_format($cohort->min_contribution / 100, 2) }} to join this cohort.
                                        </p>
                                    </div>
                                @endif

                                <!-- USDT Payment Option -->
                                <label class="relative cursor-pointer block">
                                    <input type="radio" name="payment_source" value="direct" class="peer sr-only" {{ !$hasEnoughBalance ? 'checked' : '' }} id="cryptoPayment">
                                    <div class="p-5 rounded-xl border-2 border-slate-200 bg-white peer-checked:border-indigo-500 peer-checked:bg-indigo-50/50 transition-all hover:border-slate-300">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                                                    <svg class="w-7 h-7 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900">Pay with Crypto (USDT)</div>
                                                    <div class="text-xs text-slate-500">Select network on next page</div>
                                                </div>
                                            </div>
                                            <div class="text-xs text-slate-500">External payment</div>
                                        </div>
                                    </div>
                                    <div class="absolute top-5 right-5 hidden peer-checked:block">
                                        <div class="w-6 h-6 bg-indigo-500 rounded-full flex items-center justify-center">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- USDT Network Selection (show only when crypto is selected) -->
                            <div id="cryptoNetworkSection" style="display: none;">
                                <label class="block text-sm font-bold text-slate-900 mb-3">Select USDT Network</label>
                                
                                <!-- USDT TRC20 (Recommended) -->
                                <label class="relative cursor-pointer mb-3 block">
                                    <input type="radio" name="usdt_network" value="usdttrc20" class="peer sr-only" checked>
                                    <div class="p-5 rounded-xl border-2 border-slate-200 bg-white peer-checked:border-emerald-500 peer-checked:bg-emerald-50/50 transition-all hover:border-slate-300">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                                                    <svg class="w-7 h-7 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900">USDT (TRC20)</div>
                                                    <div class="text-xs text-slate-500">TRON Network • Low fees (~$1)</div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider rounded-md">
                                                    Recommended
                                                </span>
                                                <div class="text-xs text-slate-500 mt-1">~30 seconds</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="absolute top-5 right-5 hidden peer-checked:block">
                                        <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                    </div>
                                </label>
                                
                                <!-- USDT BEP20 -->
                                <label class="relative cursor-pointer mb-3 block">
                                    <input type="radio" name="usdt_network" value="usdtbep20" class="peer sr-only">
                                    <div class="p-5 rounded-xl border-2 border-slate-200 bg-white peer-checked:border-amber-500 peer-checked:bg-amber-50/50 transition-all hover:border-slate-300">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                                                    <svg class="w-7 h-7 text-amber-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900">USDT (BEP20)</div>
                                                    <div class="text-xs text-slate-500">Binance Smart Chain • Low fees (~$0.50)</div>
                                                </div>
                                            </div>
                                            <div class="text-xs text-slate-500">~3 seconds</div>
                                        </div>
                                    </div>
                                    <div class="absolute top-5 right-5 hidden peer-checked:block">
                                        <div class="w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                    </div>
                                </label>
                                
                                <!-- USDT ERC20 -->
                                <label class="relative cursor-pointer block">
                                    <input type="radio" name="usdt_network" value="usdterc20" class="peer sr-only">
                                    <div class="p-5 rounded-xl border-2 border-slate-200 bg-white peer-checked:border-blue-500 peer-checked:bg-blue-50/50 transition-all hover:border-slate-300 opacity-60">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4">
                                                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                                    <svg class="w-7 h-7 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900">USDT (ERC20)</div>
                                                    <div class="text-xs text-slate-500">Ethereum Network • High fees (~$15)</div>
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <span class="px-2.5 py-1 bg-red-100 text-red-700 text-[10px] font-bold uppercase tracking-wider rounded-md">
                                                    Not Recommended
                                                </span>
                                                <div class="text-xs text-slate-500 mt-1">~2 minutes</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="absolute top-5 right-5 hidden peer-checked:block">
                                        <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                            <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Security Notice (wallet payment) -->
                            <div id="walletNotice" style="display: none;" class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="zap" class="w-4 h-4 text-emerald-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-emerald-900 text-sm">Instant Partnership Activation</h4>
                                        <p class="text-xs text-emerald-700 mt-1">
                                            Using your wallet balance means immediate confirmation and instant partnership activation.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Notice (crypto payment) -->
                            <div id="cryptoNotice" style="display: none;" class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="shield-check" class="w-4 h-4 text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-blue-900 text-sm">Secure Payment via NOWPayments</h4>
                                        <p class="text-xs text-blue-700 mt-1">
                                            You'll receive a unique USDT wallet address. Send the exact amount to complete your partnership registration.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms Agreement -->
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <label class="flex items-start space-x-3 cursor-pointer">
                                    <input type="checkbox" name="terms" id="termsCheck" required class="mt-1 w-4 h-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-slate-600">
                                        I understand this is a <strong>project partnership</strong>, not an investment. Profits are shared based on contribution percentage. The project manager receives 50% for operational involvement. I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">partnership terms</a>.
                                    </span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold py-4 rounded-xl text-lg hover:from-indigo-700 hover:to-purple-700 transition-all shadow-xl shadow-indigo-500/30 active:scale-[0.99] flex items-center justify-center space-x-2 group" id="submitBtn">
                                <i data-lucide="lock" class="w-5 h-5"></i>
                                <span id="submitBtnText">Complete Partnership</span>
                                <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="space-y-6">
            <!-- Project Summary -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-24">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Partnership Summary</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Project</span>
                        <span class="text-sm font-bold text-slate-900">{{ $cohort->title }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Type</span>
                        <span class="text-sm font-bold text-slate-900">{{ ucfirst($cohort->cohort_class ?? 'Standard') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Duration</span>
                        <span class="text-sm font-bold text-slate-900">{{ $cohort->duration_months ?? 6 }} Months</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Target Return</span>
                        <span class="text-sm font-bold text-emerald-600">{{ $cohort->projected_annual_return ?? '12-18' }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Partners</span>
                        <span class="text-sm font-bold text-slate-900">{{ $cohort->member_count ?? 0 }}/50</span>
                    </div>
                    
                    <div class="pt-4 border-t border-slate-100">
                        @php
                            $percent = $cohort->hard_cap > 0 ? ($cohort->current_capital / $cohort->hard_cap) * 100 : 0;
                        @endphp
                        <div class="flex justify-between text-xs mb-2">
                            <span class="text-slate-500">Funding Progress</span>
                            <span class="font-bold text-slate-900">{{ number_format($percent, 0) }}%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 h-full rounded-full transition-all" style="width: {{ min($percent, 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Profit Sharing Info -->
                    <div class="pt-4 border-t border-slate-100">
                        <div class="bg-indigo-50 rounded-lg p-3">
                            <div class="text-xs font-bold text-indigo-900 mb-2">Profit Distribution</div>
                            <div class="space-y-1 text-xs text-indigo-700">
                                <div class="flex justify-between">
                                    <span>Project Manager:</span>
                                    <span class="font-mono font-bold">50%</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Partners (Pro-rata):</span>
                                    <span class="font-mono font-bold">50%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Badge -->
            <div class="bg-gradient-to-br from-emerald-50 to-cyan-50 rounded-xl border border-emerald-200 p-5">
                <div class="flex items-center space-x-2 mb-3">
                    <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600"></i>
                    <span class="text-sm font-bold text-emerald-900">Funds Protected</span>
                </div>
                <p class="text-xs text-emerald-700 leading-relaxed mb-3">
                    All contributions are secured through smart contracts and multi-signature wallets. Transparent tracking of every transaction.
                </p>
                <div class="flex items-center space-x-2 text-xs text-emerald-600 font-mono">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-10-5z"/>
                    </svg>
                    <span>AES-256 Encrypted</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Contribution amount handling
    const contributionAmount = document.getElementById('contributionAmount');
    const contributionSlider = document.getElementById('contributionSlider');
    const partnershipPercentage = document.getElementById('partnershipPercentage');
    const currentCapital = {{ $cohort->current_capital }};
    
    // Sync slider with input
    contributionAmount?.addEventListener('input', function() {
        const value = parseFloat(this.value) || {{ $cohort->min_contribution / 100 }};
        contributionSlider.value = value;
        updatePartnershipShare(value);
        validateWalletBalance(value);
    });
    
    contributionSlider?.addEventListener('input', function() {
        const value = parseFloat(this.value);
        contributionAmount.value = value;
        updatePartnershipShare(value);
        validateWalletBalance(value);
    });
    
    // Calculate and update partnership share percentage
    function updatePartnershipShare(contributionInRands) {
        const contributionInCents = contributionInRands * 100;
        const totalCapitalAfter = currentCapital + contributionInCents;
        const percentage = (contributionInCents / totalCapitalAfter) * 100;
        partnershipPercentage.textContent = percentage.toFixed(2) + '%';
    }
    
    // Validate wallet balance
    function validateWalletBalance(contributionInRands) {
        const walletBalance = {{ $walletBalance }};
        const contributionInCents = contributionInRands * 100;
        const walletPaymentRadio = document.getElementById('walletPayment');
        
        if (walletPaymentRadio && walletPaymentRadio.checked) {
            if (contributionInCents > walletBalance) {
                contributionAmount.classList.add('border-red-500');
                contributionAmount.classList.remove('border-indigo-300');
            } else {
                contributionAmount.classList.remove('border-red-500');
                contributionAmount.classList.add('border-indigo-300');
            }
        }
    }
    
    // Payment source toggle
    const walletPayment = document.getElementById('walletPayment');
    const cryptoPayment = document.getElementById('cryptoPayment');
    const cryptoNetworkSection = document.getElementById('cryptoNetworkSection');
    const walletNotice = document.getElementById('walletNotice');
    const cryptoNotice = document.getElementById('cryptoNotice');
    const submitBtnText = document.getElementById('submitBtnText');

    function updatePaymentUI() {
        if (walletPayment && walletPayment.checked) {
            cryptoNetworkSection.style.display = 'none';
            walletNotice.style.display = 'block';
            cryptoNotice.style.display = 'none';
            submitBtnText.textContent = 'Complete Partnership';
            validateWalletBalance(parseFloat(contributionAmount.value));
        } else if (cryptoPayment && cryptoPayment.checked) {
            cryptoNetworkSection.style.display = 'block';
            walletNotice.style.display = 'none';
            cryptoNotice.style.display = 'block';
            submitBtnText.textContent = 'Generate Payment Address';
            contributionAmount.classList.remove('border-red-500');
            contributionAmount.classList.add('border-indigo-300');
        }
        lucide.createIcons();
    }

    walletPayment?.addEventListener('change', updatePaymentUI);
    cryptoPayment?.addEventListener('change', updatePaymentUI);
    
    // Initial update
    updatePaymentUI();
    
    // Form submission handler
    document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
        const terms = document.getElementById('termsCheck');
        if (!terms.checked) {
            e.preventDefault();
            alert('Please accept the partnership terms to continue');
            return false;
        }
        
        // Validate wallet balance for wallet payments
        if (walletPayment && walletPayment.checked) {
            const contributionInCents = parseFloat(contributionAmount.value) * 100;
            const walletBalance = {{ $walletBalance }};
            
            if (contributionInCents > walletBalance) {
                e.preventDefault();
                alert('Insufficient wallet balance. Please enter a lower amount or deposit more funds.');
                return false;
            }
        }
        
        // Validate minimum and maximum
        const amount = parseFloat(contributionAmount.value);
        const minContribution = {{ $cohort->min_contribution / 100 }};
        const maxContribution = {{ min($cohort->max_contribution, $cohort->hard_cap - $cohort->current_capital) / 100 }};
        
        if (amount < minContribution) {
            e.preventDefault();
            alert('Contribution must be at least R' + minContribution.toLocaleString());
            return false;
        }
        
        if (amount > maxContribution) {
            e.preventDefault();
            alert('Contribution cannot exceed R' + maxContribution.toLocaleString());
            return false;
        }
    });
</script>
@endpush
