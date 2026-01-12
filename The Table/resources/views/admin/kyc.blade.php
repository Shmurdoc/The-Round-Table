@extends('layouts.modern')

@section('title', 'KYC Review - RoundTable')
@section('page-title', 'KYC Review')

@section('content')
<div class="slide-up space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="file-check" class="w-5 h-5 text-amber-600"></i>
                </div>
                KYC Review
            </h1>
            <p class="text-slate-500 text-sm mt-2">Review and verify member identity documents</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Dashboard
        </a>
    </div>

    <!-- Pending KYC Submissions -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
                <h3 class="font-bold text-slate-900">Pending KYC Submissions</h3>
            </div>
            <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-full">
                {{ $pendingUsers->count() ?? 0 }} pending
            </span>
        </div>
        
        <div class="divide-y divide-slate-100">
            @forelse($pendingUsers ?? [] as $user)
                <div class="p-6 hover:bg-slate-50 transition">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <!-- User Info -->
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center">
                                <span class="text-lg font-bold text-slate-600">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-900">{{ $user->name ?? 'Unknown' }}</h4>
                                <p class="text-sm text-slate-500">{{ $user->email ?? 'No email' }}</p>
                                <p class="text-xs text-slate-400">
                                    Submitted {{ $user->kyc_submitted_at?->diffForHumans() ?? 'recently' }}
                                </p>
                            </div>
                        </div>
                        
                        <!-- ID Number -->
                        <div class="text-center hidden md:block">
                            <div class="text-xs text-slate-400 uppercase font-bold">ID Number</div>
                            <div class="text-sm font-mono text-slate-700">{{ $user->kyc_id_number ?? 'Not provided' }}</div>
                        </div>
                        
                        <!-- Documents -->
                        <div class="flex items-center gap-2">
                            @if($user->kyc_id_document_front)
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-lg">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    ID Front
                                </span>
                            @endif
                            @if($user->kyc_id_document_back)
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-lg">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    ID Back
                                </span>
                            @endif
                            @if($user->kyc_proof_of_residence)
                                <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-100 text-emerald-700 text-xs rounded-lg">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    Proof
                                </span>
                            @endif
                        </div>
                        
                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="{{ route('admin.kyc.review', $user) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-700 text-sm font-bold rounded-xl hover:bg-amber-200 transition">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                Review
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="check-circle" class="w-8 h-8 text-emerald-500"></i>
                    </div>
                    <h3 class="text-slate-600 font-semibold">All Caught Up!</h3>
                    <p class="text-slate-400 text-sm mt-1">No pending KYC submissions to review.</p>
                </div>
            @endforelse
        </div>
        
        @if(isset($pendingUsers) && $pendingUsers->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pendingUsers->links() }}
            </div>
        @endif
    </div>

    <!-- Quick Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
            </div>
            <div>
                <h4 class="font-bold text-blue-900">KYC Verification Requirements</h4>
                <ul class="mt-2 space-y-1 text-sm text-blue-700">
                    <li class="flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-blue-500"></i>
                        Valid South African ID document (front and back)
                    </li>
                    <li class="flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-blue-500"></i>
                        Proof of residence (not older than 3 months)
                    </li>
                    <li class="flex items-center gap-2">
                        <i data-lucide="check" class="w-4 h-4 text-blue-500"></i>
                        Clear, legible images with all corners visible
                    </li>
                </ul>
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
