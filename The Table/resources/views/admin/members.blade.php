@extends('layouts.modern')

@section('title', 'Manage Members - RoundTable')
@section('page-title', 'Manage Members')

@section('content')
<div class="slide-up space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                </div>
                Manage Members
            </h1>
            <p class="text-slate-500 text-sm mt-2">View and manage members across your cohorts</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Dashboard
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Total Members</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($totalMembers ?? 0) }}</div>
            <div class="text-xs text-slate-500">In your cohorts</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Verified</span>
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($verifiedMembers ?? 0) }}</div>
            <div class="text-xs text-slate-500">KYC completed</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Pending KYC</span>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($pendingMembers ?? 0) }}</div>
            <div class="text-xs text-slate-500">Awaiting verification</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Total Invested</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="wallet" class="w-4 h-4 text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R{{ number_format(($totalInvested ?? 0) / 100, 0) }}</div>
            <div class="text-xs text-slate-500">From members</div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900">All Members</h3>
            <div class="flex gap-2">
                <a href="{{ route('admin.kyc') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-amber-100 text-amber-700 text-xs font-bold rounded-lg hover:bg-amber-200 transition">
                    <i data-lucide="file-check" class="w-3 h-3"></i>
                    Review KYC
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Member</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Cohorts</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Total Invested</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">KYC Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Joined</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($members ?? [] as $member)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-slate-600">{{ strtoupper(substr($member->name ?? 'U', 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-900">{{ $member->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-slate-500">{{ $member->phone ?? 'No phone' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $member->email ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($member->cohortMemberships ?? [] as $membership)
                                        <span class="inline-flex px-2 py-1 text-xs font-bold rounded bg-purple-100 text-purple-700">
                                            {{ $membership->cohort->title ?? 'Unknown' }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-slate-400">—</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm font-bold text-slate-900">
                                    R{{ number_format(($member->cohortMemberships->sum('capital_paid') ?? 0) / 100, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $kycStatus = $member->kyc_status ?? 'not_started';
                                    $statusColors = [
                                        'verified' => 'bg-emerald-100 text-emerald-700',
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        'not_started' => 'bg-slate-100 text-slate-600',
                                    ];
                                @endphp
                                <span class="inline-flex px-2 py-1 text-xs font-bold rounded-lg {{ $statusColors[$kycStatus] ?? $statusColors['not_started'] }}">
                                    {{ ucfirst(str_replace('_', ' ', $kycStatus)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-slate-500">{{ $member->created_at?->format('M d, Y') ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($member->kyc_status === 'pending')
                                    <a href="{{ route('admin.kyc.review', $member) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-lg hover:bg-amber-200 transition">
                                        <i data-lucide="eye" class="w-3 h-3"></i>
                                        Review
                                    </a>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="users" class="w-8 h-8 text-slate-300"></i>
                                </div>
                                <h3 class="text-slate-600 font-semibold">No Members Found</h3>
                                <p class="text-slate-400 text-sm mt-1">Members will appear here once they join your cohorts.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($members) && $members instanceof \Illuminate\Pagination\LengthAwarePaginator && $members->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
