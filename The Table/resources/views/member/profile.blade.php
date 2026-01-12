@extends('layouts.modern')

@section('title', 'Profile - RoundTable')
@section('page-title', 'Profile')

@section('content')
<div class="slide-up space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Member Profile</h1>
            <p class="text-slate-500 text-sm mt-2">Manage your profile and account settings</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-900 p-8 text-center relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-amber-500/20 rounded-full blur-3xl -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="h-20 w-20 rounded-full bg-gradient-to-br from-amber-400 to-amber-600 text-slate-900 flex items-center justify-center text-2xl font-bold mx-auto mb-4 ring-4 ring-white/20">
                            {{ strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? 'N', 0, 1)) }}
                        </div>
                        <h2 class="text-xl font-bold text-white">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h2>
                        <p class="text-sm text-slate-400 font-mono">ID: #{{ str_pad(auth()->user()->id, 4, '0', STR_PAD_LEFT) }}</p>
                        
                        <div class="mt-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                @if(in_array(auth()->user()->kyc_status, ['approved', 'verified'])) bg-emerald-500/20 text-emerald-400
                                @elseif(auth()->user()->kyc_status === 'pending') bg-amber-500/20 text-amber-400
                                @else bg-slate-500/20 text-slate-400 @endif">
                                <i data-lucide="shield-check" class="w-3 h-3 mr-1.5"></i>
                                KYC: {{ ucfirst(auth()->user()->kyc_status ?? 'Not Started') }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="p-5 space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="mail" class="w-4 h-4 text-slate-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Email</div>
                            <div class="text-sm text-slate-900 truncate">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="phone" class="w-4 h-4 text-slate-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Phone</div>
                            <div class="text-sm text-slate-900">{{ auth()->user()->phone ?? 'Not provided' }}</div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Member Since</div>
                            <div class="text-sm text-slate-900">{{ auth()->user()->created_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="p-5 border-t border-slate-100">
                    <a href="{{ route('kyc.form') }}" class="w-full flex items-center justify-center space-x-2 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition-colors">
                        <i data-lucide="shield-check" class="w-4 h-4"></i>
                        <span>Update KYC</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Quick Actions</h3>
                </div>
                <div class="p-5 grid grid-cols-2 md:grid-cols-3 gap-4">
                    <a href="{{ route('cohorts.index') }}" class="p-4 rounded-xl border border-slate-200 hover:border-amber-300 hover:bg-amber-50/50 transition-all group">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-amber-200 transition-colors">
                            <i data-lucide="users" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div class="font-bold text-slate-900 text-sm">Browse Cohorts</div>
                        <div class="text-xs text-slate-500 mt-1">Find partnership opportunities</div>
                    </a>
                    <a href="{{ route('member.portfolio') }}" class="p-4 rounded-xl border border-slate-200 hover:border-amber-300 hover:bg-amber-50/50 transition-all group">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-emerald-200 transition-colors">
                            <i data-lucide="wallet" class="w-5 h-5 text-emerald-600"></i>
                        </div>
                        <div class="font-bold text-slate-900 text-sm">My Portfolio</div>
                        <div class="text-xs text-slate-500 mt-1">View your partnerships</div>
                    </a>
                    <a href="{{ route('member.notifications') }}" class="p-4 rounded-xl border border-slate-200 hover:border-amber-300 hover:bg-amber-50/50 transition-all group">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-blue-200 transition-colors">
                            <i data-lucide="bell" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="font-bold text-slate-900 text-sm">Notifications</div>
                        <div class="text-xs text-slate-500 mt-1">Stay updated</div>
                    </a>
                </div>
            </div>

            <!-- Partnership Summary -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Partnership Summary</h3>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <div class="text-[10px] text-slate-400 uppercase font-bold mb-1">Total Invested</div>
                            <div class="font-mono text-lg font-bold text-slate-900">
                                R{{ number_format(auth()->user()->totalInvestedCapital() / 100, 0) }}
                            </div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <div class="text-[10px] text-slate-400 uppercase font-bold mb-1">Active Cohorts</div>
                            <div class="font-mono text-lg font-bold text-slate-900">
                                {{ auth()->user()->activeCohorts()->count() }}
                            </div>
                        </div>
                        <div class="bg-emerald-50 p-4 rounded-xl border border-emerald-100">
                            <div class="text-[10px] text-emerald-500 uppercase font-bold mb-1">Distributions</div>
                            <div class="font-mono text-lg font-bold text-emerald-600">
                                R{{ number_format(auth()->user()->cohortMemberships->sum('total_distributions_received') / 100, 0) }}
                            </div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <div class="text-[10px] text-slate-400 uppercase font-bold mb-1">Role</div>
                            <div class="font-mono text-lg font-bold text-slate-900 capitalize">
                                {{ auth()->user()->role ?? 'Member' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KYC Documents Section -->
            @if(auth()->user()->kyc_id_document_front || auth()->user()->kyc_id_document_back || auth()->user()->kyc_proof_of_residence)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="file-text" class="w-4 h-4 text-slate-500"></i>
                        Your KYC Documents
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">View your uploaded identity verification documents</p>
                </div>
                <div class="p-6 space-y-6">
                    <!-- ID Document Front -->
                    @if(auth()->user()->kyc_id_document_front)
                    <div>
                        <label class="text-sm font-bold text-slate-700 mb-2 block flex items-center gap-2">
                            <i data-lucide="credit-card" class="w-4 h-4 text-slate-400"></i>
                            ID Document (Front)
                        </label>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <a href="{{ Storage::url(auth()->user()->kyc_id_document_front) }}" target="_blank" 
                               class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-medium mb-3">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                                View Document
                            </a>
                            <img src="{{ Storage::url(auth()->user()->kyc_id_document_front) }}" 
                                 alt="ID Front" 
                                 class="mt-3 max-h-64 rounded-lg border border-slate-200 w-full object-cover">
                        </div>
                    </div>
                    @endif

                    <!-- ID Document Back -->
                    @if(auth()->user()->kyc_id_document_back)
                    <div>
                        <label class="text-sm font-bold text-slate-700 mb-2 block flex items-center gap-2">
                            <i data-lucide="credit-card" class="w-4 h-4 text-slate-400"></i>
                            ID Document (Back)
                        </label>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <a href="{{ Storage::url(auth()->user()->kyc_id_document_back) }}" target="_blank" 
                               class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-medium mb-3">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                                View Document
                            </a>
                            <img src="{{ Storage::url(auth()->user()->kyc_id_document_back) }}" 
                                 alt="ID Back" 
                                 class="mt-3 max-h-64 rounded-lg border border-slate-200 w-full object-cover">
                        </div>
                    </div>
                    @endif

                    <!-- Proof of Residence -->
                    @if(auth()->user()->kyc_proof_of_residence)
                    <div>
                        <label class="text-sm font-bold text-slate-700 mb-2 block flex items-center gap-2">
                            <i data-lucide="home" class="w-4 h-4 text-slate-400"></i>
                            Proof of Residence
                        </label>
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <a href="{{ Storage::url(auth()->user()->kyc_proof_of_residence) }}" target="_blank" 
                               class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-medium mb-3">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                                View Document
                            </a>
                            <img src="{{ Storage::url(auth()->user()->kyc_proof_of_residence) }}" 
                                 alt="Proof of Residence" 
                                 class="mt-3 max-h-64 rounded-lg border border-slate-200 w-full object-cover">
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Security Section -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Security</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 text-sm">Two-Factor Authentication</div>
                                <div class="text-xs text-slate-500">Add an extra layer of security</div>
                            </div>
                        </div>
                        <button class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition-colors">
                            Coming Soon
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl border border-slate-100">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="key" class="w-4 h-4 text-amber-600"></i>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 text-sm">Change Password</div>
                                <div class="text-xs text-slate-500">Update your account password</div>
                            </div>
                        </div>
                        <button class="px-4 py-2 bg-slate-200 text-slate-600 rounded-lg text-xs font-bold hover:bg-slate-300 transition-colors">
                            Coming Soon
                        </button>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-white rounded-2xl border border-red-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-red-100 bg-red-50/50">
                    <h3 class="font-bold text-red-900">Danger Zone</h3>
                </div>
                <div class="p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="font-bold text-slate-900 text-sm">Sign Out</div>
                            <div class="text-xs text-slate-500">Sign out of your account on this device</div>
                        </div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg text-xs font-bold hover:bg-red-200 transition-colors flex items-center space-x-2">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                <span>Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
