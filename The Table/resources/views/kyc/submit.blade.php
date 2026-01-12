@extends('layouts.modern')

@section('title', 'KYC Verification - RoundTable')
@section('page-title', 'Identity Verification')

@push('styles')
<style>
    .gradient-text {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .glass-card {
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(251, 191, 36, 0.1);
    }
    
    .cyber-border {
        position: relative;
        border-radius: 1rem;
        padding: 2px;
        background: linear-gradient(135deg, #fbbf24, #f59e0b);
    }
    
    .cyber-border-inner {
        border-radius: 0.875rem;
        background: rgba(15, 23, 42, 0.95);
    }
    
    .file-upload-zone {
        transition: all 0.3s ease;
    }
    
    .file-upload-zone:hover {
        border-color: rgba(251, 191, 36, 0.5);
        background: rgba(251, 191, 36, 0.05);
    }
    
    .status-badge {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-24">
    <!-- Hero Header -->
    <div class="cyber-border shadow-2xl">
        <div class="cyber-border-inner p-8 sm:p-10">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center shadow-lg shadow-amber-500/30 flex-shrink-0">
                    <i data-lucide="shield-check" class="w-8 h-8 sm:w-10 sm:h-10 text-white"></i>
                </div>
                <div class="flex-1">
                    <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">
                        KYC <span class="gradient-text">Verification</span>
                    </h1>
                    <p class="text-slate-300 text-sm sm:text-base">Complete your identity verification to unlock full platform access and participate in investment partnerships.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Messages -->
    @if(Auth::user()->kyc_status === 'pending')
    <div class="glass-card border-amber-500/30 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-amber-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-6 h-6 text-amber-400 status-badge"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-amber-400 text-lg mb-1">Under Review</h4>
                <p class="text-slate-300 text-sm">Your KYC submission is being processed. We'll notify you via email once it's been reviewed (typically within 24-48 hours).</p>
            </div>
        </div>
    </div>
    @elseif(Auth::user()->kyc_status === 'approved')
    <div class="glass-card border-green-500/30 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle" class="w-6 h-6 text-green-400"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-green-400 text-lg mb-1">Verified!</h4>
                <p class="text-slate-300 text-sm">Your KYC has been approved. You can now participate in all partnership cohorts and access full platform features.</p>
            </div>
        </div>
    </div>
    @elseif(Auth::user()->kyc_status === 'rejected')
    <div class="glass-card border-red-500/30 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-red-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="x-circle" class="w-6 h-6 text-red-400"></i>
            </div>
            <div class="flex-1">
                <h4 class="font-bold text-red-400 text-lg mb-1">Verification Failed</h4>
                <p class="text-slate-300 text-sm mb-3">Your KYC was rejected. Please review the reason below and resubmit with correct information.</p>
                @if(Auth::user()->kyc_rejection_reason)
                <div class="bg-red-500/10 border border-red-500/20 rounded-lg p-3">
                    <p class="text-red-400 text-sm"><strong>Reason:</strong> {{ Auth::user()->kyc_rejection_reason }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    @if(!in_array(Auth::user()->kyc_status, ['approved', 'verified']))
    <form action="{{ route('kyc.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Personal Information -->
        <div class="glass-card rounded-2xl overflow-hidden border border-slate-700/50">
            <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 px-6 py-5 border-b border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-amber-500/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="user" class="w-5 h-5 text-amber-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-lg">Personal Information</h3>
                        <p class="text-sm text-slate-400">Your basic identity details</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-5">
                <!-- Read-only Fields -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Full Name</label>
                        <div class="px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300">
                            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                        <div class="px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 truncate">
                            {{ Auth::user()->email }}
                        </div>
                    </div>
                </div>

                <!-- ID Number -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        South African ID Number <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="credit-card" class="w-5 h-5 text-slate-500"></i>
                        </div>
                        <input type="text" 
                               name="id_number" 
                               id="id_number"
                               value="{{ old('id_number', Auth::user()->kyc_id_number) }}" 
                               placeholder="0000000000000" 
                               maxlength="13"
                               class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 transition-all @error('id_number') border-red-500/50 @enderror"
                               required>
                    </div>
                    @error('id_number')
                    <p class="text-red-400 text-sm mt-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                    <p class="text-slate-500 text-xs mt-2">13-digit South African ID number</p>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Phone Number <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="phone" class="w-5 h-5 text-slate-500"></i>
                        </div>
                        <input type="tel" 
                               name="phone" 
                               id="phone"
                               value="{{ old('phone', Auth::user()->phone) }}" 
                               placeholder="+27 00 000 0000"
                               class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 transition-all @error('phone') border-red-500/50 @enderror"
                               required>
                    </div>
                    @error('phone')
                    <p class="text-red-400 text-sm mt-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Physical Address <span class="text-red-400">*</span>
                    </label>
                    <textarea name="address" 
                              rows="3"
                              placeholder="Enter your full physical address..."
                              class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 transition-all resize-none @error('address') border-red-500/50 @enderror"
                              required>{{ old('address', Auth::user()->address) }}</textarea>
                    @error('address')
                    <p class="text-red-400 text-sm mt-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Document Upload -->
        <div class="glass-card rounded-2xl overflow-hidden border border-slate-700/50">
            <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 px-6 py-5 border-b border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-lg">Document Upload</h3>
                        <p class="text-sm text-slate-400">Upload clear photos or scans of your documents</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-5">
                <!-- ID Front -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        <i data-lucide="image" class="w-4 h-4 inline-block mr-1"></i>
                        ID Document (Front) <span class="text-red-400">*</span>
                    </label>
                    <div class="file-upload-zone border-2 border-dashed border-slate-700/50 rounded-xl p-6 text-center transition-all">
                        <input type="file" 
                               name="id_document_front" 
                               id="id_document_front"
                               accept="image/*,application/pdf"
                               class="hidden"
                               required>
                        <label for="id_document_front" class="cursor-pointer">
                            <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="upload-cloud" class="w-6 h-6 text-amber-400"></i>
                            </div>
                            <p class="text-white font-medium mb-1">Click to upload or drag and drop</p>
                            <p class="text-slate-400 text-sm">PNG, JPG, PDF up to 5MB</p>
                        </label>
                    </div>
                    @error('id_document_front')
                    <p class="text-red-400 text-sm mt-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- ID Back -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        <i data-lucide="image" class="w-4 h-4 inline-block mr-1"></i>
                        ID Document (Back) <span class="text-red-400">*</span>
                    </label>
                    <div class="file-upload-zone border-2 border-dashed border-slate-700/50 rounded-xl p-6 text-center transition-all">
                        <input type="file" 
                               name="id_document_back" 
                               id="id_document_back"
                               accept="image/*,application/pdf"
                               class="hidden"
                               required>
                        <label for="id_document_back" class="cursor-pointer">
                            <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="upload-cloud" class="w-6 h-6 text-amber-400"></i>
                            </div>
                            <p class="text-white font-medium mb-1">Click to upload or drag and drop</p>
                            <p class="text-slate-400 text-sm">PNG, JPG, PDF up to 5MB</p>
                        </label>
                    </div>
                    @error('id_document_back')
                    <p class="text-red-400 text-sm mt-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Proof of Residence -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        <i data-lucide="home" class="w-4 h-4 inline-block mr-1"></i>
                        Proof of Residence <span class="text-red-400">*</span>
                    </label>
                    <div class="file-upload-zone border-2 border-dashed border-slate-700/50 rounded-xl p-6 text-center transition-all">
                        <input type="file" 
                               name="proof_of_residence" 
                               id="proof_of_residence"
                               accept="image/*,application/pdf"
                               class="hidden"
                               required>
                        <label for="proof_of_residence" class="cursor-pointer">
                            <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="upload-cloud" class="w-6 h-6 text-amber-400"></i>
                            </div>
                            <p class="text-white font-medium mb-1">Click to upload or drag and drop</p>
                            <p class="text-slate-400 text-sm">PNG, JPG, PDF up to 5MB</p>
                        </label>
                    </div>
                    <p class="text-slate-500 text-xs mt-2">Utility bill, bank statement, or lease agreement (not older than 3 months)</p>
                    @error('proof_of_residence')
                    <p class="text-red-400 text-sm mt-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Crypto Wallet Details -->
        <div class="glass-card rounded-2xl overflow-hidden border border-slate-700/50">
            <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 px-6 py-5 border-b border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="wallet" class="w-5 h-5 text-green-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-lg">Crypto Wallet Address</h3>
                        <p class="text-sm text-slate-400">For receiving USDT withdrawals and profit distributions</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 space-y-5">
                <!-- Network Selection -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-3">
                        Preferred Network <span class="text-red-400">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['TRC20', 'BEP20', 'ERC20'] as $network)
                        <label class="cursor-pointer group">
                            <input type="radio" name="crypto_network" value="{{ $network }}" class="peer sr-only" 
                                   {{ old('crypto_network', Auth::user()->crypto_network ?? 'TRC20') == $network ? 'checked' : '' }} required>
                            <div class="p-4 border-2 border-slate-700/50 rounded-xl text-center font-medium text-slate-400 peer-checked:border-green-500 peer-checked:bg-green-500/10 peer-checked:text-green-400 transition-all group-hover:border-slate-600">
                                <div class="text-lg mb-1">{{ $network }}</div>
                                @if($network == 'TRC20')
                                <span class="text-xs text-amber-400">⭐ Recommended</span>
                                @else
                                <span class="text-xs text-slate-500">Available</span>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('crypto_network')
                    <p class="text-red-400 text-sm mt-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Wallet Address -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        USDT Wallet Address <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="key" class="w-5 h-5 text-slate-500"></i>
                        </div>
                        <input type="text" 
                               name="crypto_wallet_address" 
                               value="{{ old('crypto_wallet_address', Auth::user()->crypto_wallet_address) }}" 
                               placeholder="Enter your USDT wallet address"
                               class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 font-mono text-sm focus:border-green-500/50 focus:ring-2 focus:ring-green-500/20 transition-all @error('crypto_wallet_address') border-red-500/50 @enderror"
                               required>
                    </div>
                    @error('crypto_wallet_address')
                    <p class="text-red-400 text-sm mt-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Wallet Confirmation -->
                <div class="bg-amber-500/10 border border-amber-500/30 rounded-xl p-4">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-400 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-amber-400 text-sm mb-2">⚠️ Important: Verify Your Wallet Address</h4>
                            <p class="text-amber-200/80 text-xs leading-relaxed">
                                Double-check your wallet address and ensure it matches your selected network. All profit distributions and withdrawals will be sent to this address. Incorrect addresses may result in <strong>permanent loss of funds</strong>.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Holder Name -->
        <div class="glass-card rounded-2xl overflow-hidden border border-slate-700/50">
            <div class="bg-gradient-to-r from-slate-800/80 to-slate-900/80 px-6 py-5 border-b border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-500/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="user-check" class="w-5 h-5 text-purple-400"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-white text-lg">Account Holder Verification</h3>
                        <p class="text-sm text-slate-400">Confirm wallet ownership</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">
                        Full Name (as per ID) <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="user" class="w-5 h-5 text-slate-500"></i>
                        </div>
                        <input type="text" 
                               name="account_holder_name" 
                               value="{{ old('account_holder_name', Auth::user()->name) }}" 
                               placeholder="Enter your full name"
                               class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:border-purple-500/50 focus:ring-2 focus:ring-purple-500/20 transition-all @error('account_holder_name') border-red-500/50 @enderror"
                               required>
                    </div>
                    @error('account_holder_name')
                    <p class="text-red-400 text-sm mt-2 flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i>
                        {{ $message }}
                    </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Terms & Submit -->
        <div class="glass-card rounded-2xl border border-slate-700/50 p-6 space-y-5">
            <label class="flex items-start gap-3 cursor-pointer group">
                <input type="checkbox" id="terms" class="mt-1 w-5 h-5 rounded border-slate-600 bg-slate-800 text-amber-500 focus:ring-amber-500/20 focus:ring-offset-0 cursor-pointer" required>
                <span class="text-sm text-slate-300 select-none">
                    I confirm that the information provided is accurate and I agree to the 
                    <a href="/terms" class="text-amber-400 hover:text-amber-300 transition-colors font-medium">Terms & Conditions</a> and 
                    <a href="/privacy" class="text-amber-400 hover:text-amber-300 transition-colors font-medium">Privacy Policy</a>.
                </span>
            </label>

            <button type="submit" class="w-full py-4 bg-gradient-to-r from-amber-400 to-amber-600 hover:from-amber-500 hover:to-amber-700 text-white font-bold rounded-xl shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 transition-all transform hover:scale-[1.02] flex items-center justify-center gap-2 group">
                <i data-lucide="send" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                <span>Submit KYC Verification</span>
            </button>
        </div>
    </form>
    @else
    <!-- Approved State -->
    <div class="glass-card rounded-2xl border border-green-500/30 p-10 text-center">
        <div class="w-24 h-24 bg-gradient-to-br from-green-400 to-green-600 rounded-3xl mx-auto mb-6 flex items-center justify-center shadow-2xl shadow-green-500/30">
            <i data-lucide="check" class="w-12 h-12 text-white"></i>
        </div>
        <h3 class="text-3xl font-bold text-white mb-3">You're All Set!</h3>
        <p class="text-slate-300 mb-8 max-w-md mx-auto">Your KYC verification is complete. You can now participate in all partnership opportunities and access full platform features.</p>
        <a href="{{ route('cohorts.index') }}" class="inline-flex items-center gap-2 py-4 px-8 bg-gradient-to-r from-green-400 to-green-600 hover:from-green-500 hover:to-green-700 text-white font-bold rounded-xl shadow-lg shadow-green-500/30 hover:shadow-green-500/50 transition-all transform hover:scale-[1.02] group">
            <i data-lucide="briefcase" class="w-5 h-5"></i>
            <span>Browse Partnership Cohorts</span>
            <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // ID Number validation (South African - numbers only)
    document.getElementById('id_number')?.addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '').substring(0, 13);
    });

    // Phone number formatting
    document.getElementById('phone')?.addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        if (value.length > 0 && !value.startsWith('27')) {
            value = '27' + value;
        }
        this.value = value.substring(0, 11);
    });
    
    // File upload preview
    ['id_document_front', 'id_document_back', 'proof_of_residence'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('change', function(e) {
                const label = this.nextElementSibling || this.parentElement.querySelector('label[for="' + id + '"]');
                if (this.files.length > 0) {
                    const fileName = this.files[0].name;
                    const uploadIcon = label.querySelector('[data-lucide="upload-cloud"]');
                    if (uploadIcon) {
                        uploadIcon.setAttribute('data-lucide', 'check-circle');
                        lucide.createIcons();
                    }
                    const textElement = label.querySelector('p.text-white');
                    if (textElement) {
                        textElement.textContent = fileName;
                        textElement.classList.add('text-green-400');
                    }
                }
            });
        }
    });
</script>
@endpush
@endsection
