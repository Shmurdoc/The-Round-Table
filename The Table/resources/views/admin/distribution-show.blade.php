@extends('layouts.modern')

@section('title', 'Distribution Details - RoundTable')
@section('page-title', 'Distribution Details')

@section('content')
<div class="slide-up space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="banknote" class="w-5 h-5 text-purple-600"></i>
                </div>
                Distribution Details
            </h1>
            <p class="text-slate-500 text-sm mt-2">{{ $cohort->title ?? $cohort->name }} • {{ $distribution->distribution_date->format('M d, Y') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.distributions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back
            </a>
            @if($isAdmin && $distribution->status === 'pending')
                <form action="{{ route('admin.distributions.complete', $distribution) }}" method="POST" class="inline" onsubmit="return confirm('Process this distribution? This will trigger payments.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Complete Distribution
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Total Amount</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="wallet" class="w-4 h-4 text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R{{ number_format($distribution->total_amount, 2) }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Per Member</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R{{ number_format($distribution->amount_per_member, 2) }}</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Paid</span>
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R{{ number_format($stats['total_paid'] ?? 0, 2) }}</div>
            <div class="text-xs text-slate-500">{{ $stats['completed_count'] ?? 0 }} payments</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Pending</span>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R{{ number_format($stats['total_pending'] ?? 0, 2) }}</div>
            <div class="text-xs text-slate-500">{{ $stats['pending_count'] ?? 0 }} payments</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Distribution Info -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900">Distribution Info</h3>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Status</div>
                    <span class="text-xs font-bold uppercase px-2 py-1 rounded
                        {{ $distribution->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 
                           ($distribution->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                        {{ ucfirst($distribution->status) }}
                    </span>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Type</div>
                    <div class="text-sm text-slate-900 font-bold">{{ ucfirst($distribution->type) }}</div>
                </div>
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Distribution Date</div>
                    <div class="text-sm text-slate-900">{{ $distribution->distribution_date->format('F d, Y') }}</div>
                </div>
                @if($distribution->notes)
                    <div>
                        <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Notes</div>
                        <div class="text-sm text-slate-600">{{ $distribution->notes }}</div>
                    </div>
                @endif
                @if($distribution->creator)
                    <div>
                        <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Created By</div>
                        <div class="text-sm text-slate-900">{{ $distribution->creator->name }}</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Payments List -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900">Member Payments</h3>
            </div>
            <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                @forelse($distribution->payments ?? [] as $payment)
                    <div class="p-4 hover:bg-slate-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-slate-600">
                                        {{ strtoupper(substr($payment->cohortMember->user->name ?? 'U', 0, 1)) }}
                                    </span>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900 text-sm">{{ $payment->cohortMember->user->name ?? 'Unknown' }}</div>
                                    <div class="text-xs text-slate-500">{{ $payment->cohortMember->user->email ?? '' }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold font-mono text-slate-900">R{{ number_format($payment->amount, 2) }}</div>
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded
                                    {{ $payment->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $payment->status }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <i data-lucide="users" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-500">No payments recorded yet</p>
                    </div>
                @endforelse
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
