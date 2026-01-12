@extends('layouts.modern')

@section('title', 'KYC Verification')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('member.dashboard') }}" class="text-slate-400 hover:text-white transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/30 flex items-center justify-center">
                <i data-lucide="shield-check" class="w-6 h-6 text-amber-400"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">KYC Verification</h1>
                <p class="text-slate-400 text-sm">Complete your identity verification to participate in investments</p>
            </div>
        </div>
    </div>

    <!-- Status Banners -->
    @if(Auth::user()->kyc_status === 'pending')
        <div class="mb-6 p-4 rounded-xl bg-blue-500/10 border border-blue-500/30 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-4 h-4 text-blue-400"></i>
            </div>
            <div>
                <p class="font-medium text-blue-400">Verification Pending</p>
                <p class="text-sm text-blue-300/80">Your KYC submission is under review. We'll notify you once it's been processed.</p>
            </div>
        </div>
    @elseif(in_array(Auth::user()->kyc_status, ['approved', 'verified']))
        <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/30 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle" class="w-4 h-4 text-green-400"></i>
            </div>
            <div>
                <p class="font-medium text-green-400">Verified</p>
                <p class="text-sm text-green-300/80">Your KYC has been approved! You can now participate in investments.</p>
            </div>
        </div>
    @elseif(Auth::user()->kyc_status === 'rejected')
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-500/20 flex items-center justify-center flex-shrink-0">
                <i data-lucide="x-circle" class="w-4 h-4 text-red-400"></i>
            </div>
            <div>
                <p class="font-medium text-red-400">Verification Rejected</p>
                <p class="text-sm text-red-300/80">Your KYC was rejected. Please resubmit with correct information.</p>
                @if(Auth::user()->kyc_rejection_reason)
                    <p class="text-sm text-red-300 mt-1"><strong>Reason:</strong> {{ Auth::user()->kyc_rejection_reason }}</p>
                @endif
            </div>
        </div>
    @endif

    @if(!in_array(Auth::user()->kyc_status, ['approved', 'verified']))
        <!-- Form Card -->
        <div class="bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 overflow-hidden">
            <form action="{{ route('kyc.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <!-- Personal Information Section -->
                <div class="p-6 border-b border-slate-700/50">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                            <i data-lucide="user" class="w-4 h-4 text-blue-400"></i>
                        </div>
                        <h2 class="font-semibold text-white">Personal Information</h2>
                    </div>
                    
                    <div class="space-y-5">
                        <!-- Read-only info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-2">Full Name</label>
                                <div class="px-4 py-3 bg-slate-700/30 border border-slate-600/50 rounded-xl text-slate-300">
                                    {{ Auth::user()->name }}
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-400 mb-2">Email Address</label>
                                <div class="px-4 py-3 bg-slate-700/30 border border-slate-600/50 rounded-xl text-slate-300">
                                    {{ Auth::user()->email }}
                                </div>
                            </div>
                        </div>

                        <!-- ID Number -->
                        <div>
                            <label for="id_number" class="block text-sm font-medium text-slate-300 mb-2">
                                South African ID Number <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="credit-card" class="w-4 h-4 text-slate-500"></i>
                                </div>
                                <input 
                                    type="text" 
                                    id="id_number" 
                                    name="id_number" 
                                    value="{{ old('id_number', Auth::user()->kyc_id_number) }}"
                                    maxlength="13"
                                    required
                                    class="w-full pl-11 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all @error('id_number') border-red-500 @enderror"
                                    placeholder="0000000000000"
                                >
                            </div>
                            <p class="mt-1 text-xs text-slate-500">13-digit South African ID number</p>
                            @error('id_number')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-300 mb-2">
                                Phone Number <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="phone" class="w-4 h-4 text-slate-500"></i>
                                </div>
                                <input 
                                    type="tel" 
                                    id="phone" 
                                    name="phone" 
                                    value="{{ old('phone', Auth::user()->phone) }}"
                                    required
                                    class="w-full pl-11 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all @error('phone') border-red-500 @enderror"
                                    placeholder="+27 00 000 0000"
                                >
                            </div>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-slate-300 mb-2">
                                Physical Address <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute top-3 left-4 pointer-events-none">
                                    <i data-lucide="map-pin" class="w-4 h-4 text-slate-500"></i>
                                </div>
                                <textarea 
                                    id="address" 
                                    name="address" 
                                    rows="3"
                                    required
                                    class="w-full pl-11 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all resize-none @error('address') border-red-500 @enderror"
                                    placeholder="Enter your full residential address"
                                >{{ old('address', Auth::user()->address) }}</textarea>
                            </div>
                            @error('address')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Document Upload Section -->
                <div class="p-6 border-b border-slate-700/50">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center">
                            <i data-lucide="upload" class="w-4 h-4 text-purple-400"></i>
                        </div>
                        <h2 class="font-semibold text-white">Document Upload</h2>
                    </div>

                    <div class="space-y-5">
                        <!-- ID Document -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                ID Document (Front & Back) <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="file" 
                                    name="id_document" 
                                    id="id_document"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                    class="hidden"
                                    onchange="updateFileName(this, 'id_document_label')"
                                >
                                <label 
                                    for="id_document" 
                                    class="flex items-center justify-center gap-3 p-6 border-2 border-dashed border-slate-600 rounded-xl cursor-pointer hover:border-amber-500/50 hover:bg-slate-800/50 transition-all"
                                >
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-slate-700/50 flex items-center justify-center">
                                            <i data-lucide="file-image" class="w-6 h-6 text-slate-400"></i>
                                        </div>
                                        <p id="id_document_label" class="text-sm text-slate-400">
                                            Click to upload or drag and drop
                                        </p>
                                        <p class="text-xs text-slate-500 mt-1">PDF, JPG or PNG (max 5MB)</p>
                                    </div>
                                </label>
                            </div>
                            @error('id_document')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Proof of Address -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">
                                Proof of Address (Utility bill, bank statement) <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input 
                                    type="file" 
                                    name="proof_of_address" 
                                    id="proof_of_address"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    required
                                    class="hidden"
                                    onchange="updateFileName(this, 'poa_label')"
                                >
                                <label 
                                    for="proof_of_address" 
                                    class="flex items-center justify-center gap-3 p-6 border-2 border-dashed border-slate-600 rounded-xl cursor-pointer hover:border-amber-500/50 hover:bg-slate-800/50 transition-all"
                                >
                                    <div class="text-center">
                                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-slate-700/50 flex items-center justify-center">
                                            <i data-lucide="file-text" class="w-6 h-6 text-slate-400"></i>
                                        </div>
                                        <p id="poa_label" class="text-sm text-slate-400">
                                            Click to upload or drag and drop
                                        </p>
                                        <p class="text-xs text-slate-500 mt-1">Not older than 3 months</p>
                                    </div>
                                </label>
                            </div>
                            @error('proof_of_address')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Crypto Wallet Section -->
                <div class="p-6 border-b border-slate-700/50">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                            <i data-lucide="wallet" class="w-4 h-4 text-green-400"></i>
                        </div>
                        <h2 class="font-semibold text-white">Crypto Wallet Address</h2>
                        <span class="text-xs text-slate-500">(For receiving USDT distributions)</span>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Network Selection -->
                            <div>
                                <label for="crypto_network" class="block text-sm font-medium text-slate-300 mb-2">
                                    Preferred Network <span class="text-red-400">*</span>
                                </label>
                                <select 
                                    id="crypto_network" 
                                    name="crypto_network" 
                                    required
                                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all"
                                >
                                    <option value="">Select network...</option>
                                    <option value="TRC20" {{ old('crypto_network', Auth::user()->crypto_network ?? 'TRC20') == 'TRC20' ? 'selected' : '' }}>TRC20 (Tron) - Recommended</option>
                                    <option value="BEP20" {{ old('crypto_network', Auth::user()->crypto_network) == 'BEP20' ? 'selected' : '' }}>BEP20 (BSC)</option>
                                    <option value="ERC20" {{ old('crypto_network', Auth::user()->crypto_network) == 'ERC20' ? 'selected' : '' }}>ERC20 (Ethereum)</option>
                                </select>
                                @error('crypto_network')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-slate-400">
                                    <i data-lucide="info" class="w-3 h-3 inline"></i>
                                    TRC20 offers lowest transaction fees
                                </p>
                            </div>

                            <!-- Wallet Address -->
                            <div class="md:col-span-2">
                                <label for="crypto_wallet_address" class="block text-sm font-medium text-slate-300 mb-2">
                                    USDT Wallet Address <span class="text-red-400">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="wallet" class="w-4 h-4 text-slate-500"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="crypto_wallet_address" 
                                        name="crypto_wallet_address" 
                                        value="{{ old('crypto_wallet_address', Auth::user()->crypto_wallet_address) }}"
                                        required
                                        class="w-full pl-11 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all font-mono text-sm @error('crypto_wallet_address') border-red-500 @enderror"
                                        placeholder="Enter your USDT wallet address"
                                    >
                                </div>
                                @error('crypto_wallet_address')
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                                <p class="mt-2 text-xs text-slate-400">
                                    <i data-lucide="alert-circle" class="w-3 h-3 inline"></i>
                                    Ensure this address matches your selected network. All profit distributions will be sent here.
                                </p>
                            </div>
                        </div>

                        <!-- Wallet Verification Notice -->
                        <div class="bg-amber-500/10 border border-amber-500/20 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <i data-lucide="shield-alert" class="w-5 h-5 text-amber-400"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-amber-300 mb-1">Verify Your Wallet Address</h3>
                                    <p class="text-xs text-slate-300">
                                        Double-check your wallet address before submitting. Incorrect addresses may result in permanent loss of funds. 
                                        We recommend sending a small test transaction first.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                        <!-- Branch Code -->
                        <div>
                            <label for="branch_code" class="block text-sm font-medium text-slate-300 mb-2">
                                Branch Code <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="git-branch" class="w-4 h-4 text-slate-500"></i>
                                </div>
                                <input 
                                    type="text" 
                                    id="branch_code" 
                                    name="branch_code" 
                                    value="{{ old('branch_code', Auth::user()->branch_code) }}"
                                    required
                                    class="w-full pl-11 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all font-mono @error('branch_code') border-red-500 @enderror"
                                    placeholder="e.g., 632005"
                                >
                            </div>
                            @error('branch_code')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Terms & Submit -->
                <div class="p-6">
                    <div class="flex items-start gap-3 mb-6">
                        <input 
                            type="checkbox" 
                            name="declaration" 
                            id="declaration" 
                            required
                            class="mt-1 w-4 h-4 rounded border-slate-600 bg-slate-800 text-amber-500 focus:ring-amber-500/50"
                        >
                        <label for="declaration" class="text-sm text-slate-400">
                            I hereby declare that all information provided is true and accurate. I understand that providing false information may result in the rejection of my application and potential legal action.
                        </label>
                    </div>

                    <button 
                        type="submit"
                        class="w-full py-3 px-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-semibold rounded-xl hover:from-amber-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transform hover:scale-[1.02] active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2"
                    >
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Submit for Verification
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- What happens next -->
    <div class="mt-8 bg-gradient-to-br from-slate-800/50 to-slate-900/50 rounded-2xl border border-slate-700/50 p-6">
        <h3 class="font-semibold text-white mb-4 flex items-center gap-2">
            <i data-lucide="help-circle" class="w-4 h-4 text-amber-400"></i>
            What happens next?
        </h3>
        <div class="space-y-3">
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-amber-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-xs font-bold text-amber-400">1</span>
                </div>
                <p class="text-sm text-slate-400">Our team will review your documents within 24-48 hours</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-amber-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-xs font-bold text-amber-400">2</span>
                </div>
                <p class="text-sm text-slate-400">You'll receive an email notification with the verification result</p>
            </div>
            <div class="flex items-start gap-3">
                <div class="w-6 h-6 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-xs font-bold text-green-400">3</span>
                </div>
                <p class="text-sm text-slate-400">Once approved, you can join cohorts and start investing</p>
            </div>
        </div>
    </div>
</div>

<script>
    function updateFileName(input, labelId) {
        const label = document.getElementById(labelId);
        if (input.files && input.files[0]) {
            label.innerHTML = `<span class="text-amber-400">${input.files[0].name}</span>`;
        }
    }
</script>
@endsection
