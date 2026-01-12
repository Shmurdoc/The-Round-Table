@extends('layouts.modern')

@section('title', 'KYC Verification - RoundTable')
@section('page-title', 'Identity Verification')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 slide-up pb-24">
    <!-- Header Card -->
    <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-3xl p-8 text-white shadow-2xl shadow-purple-200 relative overflow-hidden">
        <div class="absolute inset-0 bg-noise opacity-20"></div>
        <div class="relative z-10">
            <div class="w-16 h-16 bg-white/20 backdrop-blur-xl rounded-2xl flex items-center justify-center mb-4">
                <i data-lucide="shield-check" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-3xl font-bold mb-2">KYC Verification</h1>
            <p class="text-white/80">Complete your identity verification to participate in investment partnerships.</p>
        </div>
    </div>

    <!-- Status Messages -->
    @if(Auth::user()->kyc_status === 'pending')
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex items-start space-x-4">
        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="clock" class="w-6 h-6 text-amber-600"></i>
        </div>
        <div>
            <h4 class="font-bold text-amber-800">Under Review</h4>
            <p class="text-amber-700 text-sm">Your KYC submission is being processed. We'll notify you once it's been reviewed.</p>
        </div>
    </div>
    @elseif(Auth::user()->kyc_status === 'approved')
    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start space-x-4">
        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600"></i>
        </div>
        <div>
            <h4 class="font-bold text-emerald-800">Verified!</h4>
            <p class="text-emerald-700 text-sm">Your KYC has been approved. You can now participate in all partnerships.</p>
        </div>
    </div>
    @elseif(Auth::user()->kyc_status === 'rejected')
    <div class="bg-red-50 border border-red-200 rounded-2xl p-5 flex items-start space-x-4">
        <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="x-circle" class="w-6 h-6 text-red-600"></i>
        </div>
        <div>
            <h4 class="font-bold text-red-800">Verification Failed</h4>
            <p class="text-red-700 text-sm">Your KYC was rejected. Please resubmit with correct information.</p>
            @if(Auth::user()->kyc_rejection_reason)
            <p class="text-red-600 text-sm mt-2"><strong>Reason:</strong> {{ Auth::user()->kyc_rejection_reason }}</p>
            @endif
        </div>
    </div>
    @endif

    @if(Auth::user()->kyc_status !== 'approved')
    <form action="{{ route('kyc.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <!-- Personal Information -->
        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center space-x-3">
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-indigo-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Personal Information</h3>
                    <p class="text-sm text-slate-500">Your basic details</p>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <!-- Read-only Fields -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Full Name</label>
                        <div class="px-4 py-3 bg-slate-50 rounded-xl text-slate-600 border border-slate-200">
                            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Email</label>
                        <div class="px-4 py-3 bg-slate-50 rounded-xl text-slate-600 border border-slate-200 truncate">
                            {{ Auth::user()->email }}
                        </div>
                    </div>
                </div>

                <!-- ID Number -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        South African ID Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="id_number" 
                           id="id_number"
                           value="{{ old('id_number', Auth::user()->kyc_id_number) }}" 
                           placeholder="0000000000000" 
                           maxlength="13"
                           class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition @error('id_number') border-red-400 @enderror"
                           required>
                    @error('id_number')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-slate-500 text-xs mt-1">13-digit South African ID number</p>
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           name="phone" 
                           id="phone"
                           value="{{ old('phone', Auth::user()->phone) }}" 
                           placeholder="+27 00 000 0000"
                           class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition @error('phone') border-red-400 @enderror"
                           required>
                    @error('phone')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Physical Address <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" 
                              rows="3"
                              placeholder="Enter your full physical address..."
                              class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition resize-none @error('address') border-red-400 @enderror"
                              required>{{ old('address', Auth::user()->address) }}</textarea>
                    @error('address')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Document Upload -->
        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="file-text" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Document Upload</h3>
                    <p class="text-sm text-slate-500">Upload clear photos or scans of your documents</p>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <!-- ID Front -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        ID Document (Front) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="file" 
                               name="id_document_front" 
                               id="id_document_front"
                               accept="image/*,application/pdf"
                               class="w-full px-4 py-4 border-2 border-dashed border-slate-200 rounded-xl focus:border-indigo-400 transition cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 @error('id_document_front') border-red-400 @enderror"
                               required>
                    </div>
                    @error('id_document_front')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-slate-500 text-xs mt-1">Max 5MB. Formats: JPG, PNG, PDF</p>
                </div>

                <!-- ID Back -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        ID Document (Back) <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="id_document_back" 
                           id="id_document_back"
                           accept="image/*,application/pdf"
                           class="w-full px-4 py-4 border-2 border-dashed border-slate-200 rounded-xl focus:border-indigo-400 transition cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 @error('id_document_back') border-red-400 @enderror"
                           required>
                    @error('id_document_back')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-slate-500 text-xs mt-1">Max 5MB. Formats: JPG, PNG, PDF</p>
                </div>

                <!-- Proof of Residence -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Proof of Residence <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="proof_of_residence" 
                           id="proof_of_residence"
                           accept="image/*,application/pdf"
                           class="w-full px-4 py-4 border-2 border-dashed border-slate-200 rounded-xl focus:border-indigo-400 transition cursor-pointer file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 @error('proof_of_residence') border-red-400 @enderror"
                           required>
                    @error('proof_of_residence')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                    <p class="text-slate-500 text-xs mt-1">Utility bill, bank statement, or lease agreement (not older than 3 months)</p>
                </div>
            </div>
        </div>

        <!-- Crypto Wallet Details -->
        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center space-x-3">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="wallet" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Crypto Wallet Address</h3>
                    <p class="text-sm text-slate-500">For receiving USDT withdrawals and profit distributions</p>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <!-- Network Selection -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Preferred Network <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach(['TRC20', 'BEP20', 'ERC20'] as $network)
                        <label class="cursor-pointer">
                            <input type="radio" name="crypto_network" value="{{ $network }}" class="peer sr-only" 
                                   {{ old('crypto_network', Auth::user()->crypto_network ?? 'TRC20') == $network ? 'checked' : '' }} required>
                            <div class="p-3 border-2 border-slate-200 rounded-xl text-center text-sm font-medium text-slate-600 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-700 transition hover:border-slate-300">
                                {{ $network }}
                                @if($network == 'TRC20')
                                <span class="block text-xs text-slate-400 mt-1">Recommended</span>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                    @error('crypto_network')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Wallet Address -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        USDT Wallet Address <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="crypto_wallet_address" 
                           value="{{ old('crypto_wallet_address', Auth::user()->crypto_wallet_address) }}" 
                           placeholder="Enter your USDT wallet address"
                           class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 transition font-mono text-sm @error('crypto_wallet_address') border-red-400 @enderror"
                           required>
                    <p class="text-xs text-slate-500 mt-2">
                        <i data-lucide="info" class="w-3 h-3 inline"></i>
                        Make sure this address matches your selected network (TRC20/BEP20/ERC20)
                    </p>
                    @error('crypto_wallet_address')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Wallet Confirmation -->
                <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                    <div class="flex items-start space-x-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5"></i>
                        <div class="flex-1">
                            <h4 class="font-semibold text-amber-900 text-sm mb-1">Important: Verify Your Wallet Address</h4>
                            <p class="text-xs text-amber-700">
                                Double-check your wallet address. All profit distributions and withdrawals will be sent to this address. 
                                Incorrect addresses may result in permanent loss of funds.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Holder Name -->
        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
            <div class="bg-slate-50 px-6 py-4 border-b border-slate-100 flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="user-check" class="w-5 h-5 text-purple-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800">Account Holder Verification</h3>
                    <p class="text-sm text-slate-500">Confirm wallet ownership</p>
                </div>
            </div>
            
            <div class="p-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">
                        Full Name (as per ID) <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="account_holder_name" 
                           value="{{ old('account_holder_name', Auth::user()->name) }}" 
                           placeholder="Enter your full name"
                           class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-purple-400 focus:ring-4 focus:ring-purple-100 transition @error('account_holder_name') border-red-400 @enderror"
                           required>
                    @error('account_holder_name')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Terms & Submit -->
        <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-6 space-y-5">
            <label class="flex items-start space-x-3 cursor-pointer">
                <input type="checkbox" id="terms" class="mt-1 w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" required>
                <span class="text-sm text-slate-600">
                    I confirm that the information provided is accurate and I agree to the 
                    <a href="/terms" class="text-indigo-600 hover:underline font-medium">Terms & Conditions</a> and 
                    <a href="/privacy" class="text-indigo-600 hover:underline font-medium">Privacy Policy</a>.
                </span>
            </label>

            <button type="submit" class="w-full py-4 bg-gradient-to-r from-indigo-500 to-purple-500 text-white font-bold rounded-2xl shadow-lg shadow-indigo-200 hover:shadow-xl hover:shadow-indigo-300 transition-all transform hover:scale-[1.02]">
                <i data-lucide="send" class="w-5 h-5 inline-block mr-2"></i>
                Submit KYC Verification
            </button>
        </div>
    </form>
    @else
    <!-- Approved State -->
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-10 text-center">
        <div class="w-24 h-24 bg-gradient-to-br from-emerald-400 to-emerald-500 rounded-3xl mx-auto mb-6 flex items-center justify-center shadow-xl shadow-emerald-200">
            <i data-lucide="check" class="w-12 h-12 text-white"></i>
        </div>
        <h3 class="text-2xl font-bold text-slate-800 mb-2">You're All Set!</h3>
        <p class="text-slate-500 mb-8">Your KYC verification is complete. You can now participate in all partnership opportunities.</p>
        <a href="{{ route('cohorts.index') }}" class="inline-block py-4 px-8 bg-gradient-to-r from-emerald-400 to-emerald-500 text-white font-bold rounded-2xl shadow-lg shadow-emerald-200 hover:shadow-xl hover:shadow-emerald-300 transition-all transform hover:scale-[1.02]">
            <i data-lucide="briefcase" class="w-5 h-5 inline-block mr-2"></i>
            Browse Partnership Cohorts
        </a>
    </div>
    @endif
</div>

@push('scripts')
<script>
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
</script>
@endpush
@endsection
