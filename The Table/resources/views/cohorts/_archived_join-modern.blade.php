@extends('layouts.modern')

@section('title', 'Join ' . $cohort->title . ' - RoundTable')
@section('page-title', 'Join Cohort')

@section('content')
<div class="slide-up max-w-4xl mx-auto">
    <!-- Back Button -->
    <div class="mb-8">
        <a href="{{ route('cohorts.show', $cohort) }}" class="flex items-center space-x-2 text-slate-500 hover:text-slate-700 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="text-sm font-medium">Back to {{ $cohort->title }}</span>
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form Area -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-900 p-6 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/20 rounded-full blur-3xl -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="flex items-center space-x-2 mb-3">
                            <span class="px-2.5 py-1 bg-amber-500/20 text-amber-400 text-[10px] font-bold uppercase tracking-wider rounded-md border border-amber-500/30">
                                {{ ucfirst($cohort->cohort_class ?? 'Standard') }}
                            </span>
                            <span class="text-xs text-slate-400">•</span>
                            <span class="text-xs text-slate-400 font-mono">{{ $cohort->duration_months ?? 6 }} Months</span>
                        </div>
                        <h1 class="text-2xl font-bold mb-2">Reserve Your Seat</h1>
                        <p class="text-slate-400 text-sm">{{ $cohort->title }}</p>
                    </div>
                </div>
                
                <div class="p-6">
                    @if(!in_array(Auth::user()->kyc_status, ['approved', 'verified']))
                        <!-- KYC Required Alert -->
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
                            <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="shield-alert" class="w-7 h-7 text-amber-600"></i>
                            </div>
                            <h3 class="font-bold text-amber-900 text-lg mb-2">KYC Verification Required</h3>
                            <p class="text-sm text-amber-700 mb-6">You must complete identity verification before joining any cohort.</p>
                            <a href="{{ route('kyc.form') }}" class="inline-flex items-center space-x-2 px-6 py-3 bg-amber-600 text-white rounded-xl font-bold hover:bg-amber-700 transition-colors">
                                <i data-lucide="shield-check" class="w-4 h-4"></i>
                                <span>Complete KYC Now</span>
                            </a>
                        </div>
                    @else
                        <!-- Payment Form -->
                        <form action="{{ route('cohorts.process-payment', $cohort) }}" method="POST" enctype="multipart/form-data" id="paymentForm" class="space-y-6">
                            @csrf
                            
                            <!-- Contribution Amount -->
                            <div>
                                <label class="block text-sm font-bold text-slate-900 mb-3">Contribution Amount</label>
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="text-3xl font-mono font-bold text-slate-900">
                                                R{{ number_format($cohort->min_contribution / 100, 0) }}
                                            </div>
                                            <div class="text-xs text-slate-500 mt-1">Minimum contribution</div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm text-slate-500">Max Available</div>
                                            <div class="font-mono font-bold text-slate-700">
                                                R{{ number_format(($cohort->hard_cap - $cohort->current_capital) / 100, 0) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="amount" value="{{ $cohort->min_contribution }}">
                            </div>

                            <!-- Payment Method Selection -->
                            <div>
                                <label class="block text-sm font-bold text-slate-900 mb-3">Payment Method</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- PayFast Option -->
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="payment_method" value="payfast" class="peer sr-only" checked>
                                        <div class="p-4 rounded-xl border-2 border-slate-200 bg-white peer-checked:border-amber-500 peer-checked:bg-amber-50/50 transition-all hover:border-slate-300">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                                                    <i data-lucide="credit-card" class="w-5 h-5 text-emerald-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900">PayFast</div>
                                                    <div class="text-xs text-slate-500">Card, EFT, Instant EFT</div>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex items-center space-x-2 text-xs text-emerald-600">
                                                <i data-lucide="shield-check" class="w-3 h-3"></i>
                                                <span>Secure & Instant</span>
                                            </div>
                                        </div>
                                        <div class="absolute top-4 right-4 hidden peer-checked:block">
                                            <div class="w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center">
                                                <i data-lucide="check" class="w-3 h-3 text-white"></i>
                                            </div>
                                        </div>
                                    </label>
                                    
                                    <!-- Manual Transfer Option -->
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="payment_method" value="manual" class="peer sr-only">
                                        <div class="p-4 rounded-xl border-2 border-slate-200 bg-white peer-checked:border-amber-500 peer-checked:bg-amber-50/50 transition-all hover:border-slate-300">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                                    <i data-lucide="building-2" class="w-5 h-5 text-blue-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-bold text-slate-900">Bank Transfer</div>
                                                    <div class="text-xs text-slate-500">Manual EFT</div>
                                                </div>
                                            </div>
                                            <div class="mt-3 flex items-center space-x-2 text-xs text-slate-500">
                                                <i data-lucide="clock" class="w-3 h-3"></i>
                                                <span>1-3 Business Days</span>
                                            </div>
                                        </div>
                                        <div class="absolute top-4 right-4 hidden peer-checked:block">
                                            <div class="w-5 h-5 bg-amber-500 rounded-full flex items-center justify-center">
                                                <i data-lucide="check" class="w-3 h-3 text-white"></i>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- PayFast Info -->
                            <div id="payfastSection" class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-emerald-900 text-sm">Secure Payment via PayFast</h4>
                                        <p class="text-xs text-emerald-700 mt-1">You will be redirected to PayFast's secure payment gateway to complete your transaction.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Manual Transfer Info -->
                            <div id="manualSection" class="hidden">
                                <div class="bg-slate-50 border border-slate-200 rounded-xl p-5 space-y-4">
                                    <h4 class="font-bold text-slate-900 flex items-center space-x-2">
                                        <i data-lucide="building-2" class="w-4 h-4 text-slate-600"></i>
                                        <span>Bank Transfer Details</span>
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <div class="text-[10px] text-slate-400 uppercase font-bold">Bank</div>
                                            <div class="font-mono text-slate-900">FNB</div>
                                        </div>
                                        <div>
                                            <div class="text-[10px] text-slate-400 uppercase font-bold">Branch Code</div>
                                            <div class="font-mono text-slate-900">250655</div>
                                        </div>
                                        <div>
                                            <div class="text-[10px] text-slate-400 uppercase font-bold">Account Name</div>
                                            <div class="font-mono text-slate-900">RoundTable Platform</div>
                                        </div>
                                        <div>
                                            <div class="text-[10px] text-slate-400 uppercase font-bold">Account Number</div>
                                            <div class="font-mono text-slate-900">62847291049</div>
                                        </div>
                                    </div>
                                    <div class="pt-4 border-t border-slate-200">
                                        <div class="text-[10px] text-slate-400 uppercase font-bold mb-1">Your Reference</div>
                                        <div class="bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 font-mono font-bold text-amber-800">
                                            {{ Auth::user()->id }}-COH{{ $cohort->id }}
                                        </div>
                                    </div>
                                    
                                    <div class="pt-4 border-t border-slate-200">
                                        <label class="block text-sm font-bold text-slate-900 mb-2">
                                            Upload Proof of Payment
                                        </label>
                                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:border-amber-400 transition-colors">
                                            <i data-lucide="upload-cloud" class="w-8 h-8 text-slate-400 mx-auto mb-2"></i>
                                            <p class="text-sm text-slate-500 mb-2">Drop your file here or click to browse</p>
                                            <input type="file" name="proof_of_payment" class="hidden" id="proofUpload" accept="image/*,application/pdf">
                                            <button type="button" onclick="document.getElementById('proofUpload').click()" class="text-sm font-bold text-amber-600 hover:text-amber-700">
                                                Select File
                                            </button>
                                            <p class="text-xs text-slate-400 mt-2">PNG, JPG or PDF up to 10MB</p>
                                        </div>
                                        <div id="filePreview" class="hidden mt-3 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2 flex items-center justify-between">
                                            <span class="text-sm text-emerald-700 font-medium" id="fileName"></span>
                                            <button type="button" onclick="clearFile()" class="text-emerald-600 hover:text-emerald-800">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms Agreement -->
                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <label class="flex items-start space-x-3 cursor-pointer">
                                    <input type="checkbox" name="terms" id="termsCheck" required class="mt-1 w-4 h-4 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                                    <span class="text-sm text-slate-600">
                                        I agree to the <a href="#" class="text-amber-600 hover:text-amber-700 font-medium">cohort terms and conditions</a> and understand that contributions are non-refundable once the cohort reaches MVC and becomes active.
                                    </span>
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="w-full bg-slate-900 text-white font-bold py-4 rounded-xl text-lg hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20 active:scale-[0.99] flex items-center justify-center space-x-2 group">
                                <i data-lucide="lock" class="w-5 h-5"></i>
                                <span>Proceed to Payment</span>
                                <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Summary -->
        <div class="space-y-6">
            <!-- Cohort Summary -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-24">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Cohort Summary</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Cohort</span>
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
                        <span class="text-sm text-slate-500">Est. Yield</span>
                        <span class="text-sm font-bold text-emerald-600">{{ $cohort->projected_annual_return ?? '12-18' }}%</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-500">Members</span>
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
                            <div class="bg-slate-900 h-full rounded-full" style="width: {{ min($percent, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="bg-slate-50 rounded-xl border border-slate-200 p-4">
                <div class="flex items-center space-x-2 text-xs text-emerald-600 font-mono">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span>AES-256 Encrypted</span>
                </div>
                <p class="text-[10px] text-slate-500 mt-2 leading-relaxed">
                    All payments are processed securely. Your financial information is never stored on our servers.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Toggle payment sections
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'payfast') {
                document.getElementById('payfastSection').classList.remove('hidden');
                document.getElementById('manualSection').classList.add('hidden');
            } else {
                document.getElementById('payfastSection').classList.add('hidden');
                document.getElementById('manualSection').classList.remove('hidden');
            }
            lucide.createIcons();
        });
    });
    
    // File upload preview
    document.getElementById('proofUpload')?.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('filePreview').classList.remove('hidden');
        }
    });
    
    function clearFile() {
        document.getElementById('proofUpload').value = '';
        document.getElementById('filePreview').classList.add('hidden');
    }
</script>
@endpush
