@extends('layouts.admin')

@section('title', 'Cohort Funds Management - Admin')
@section('page-title', 'Cohort Funds Management')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('admin.wallet.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to Wallet Management
    </a>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <p class="text-slate-500 text-sm">Total Funds</p>
            <p class="text-3xl font-bold text-slate-900">{{ $funds->total() }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <p class="text-slate-500 text-sm">Locked Funds</p>
            <p class="text-3xl font-bold text-amber-600">{{ $lockedCount }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <p class="text-slate-500 text-sm">Total Value</p>
            <p class="text-3xl font-bold text-emerald-600">R{{ number_format($totalValue / 100, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <p class="text-slate-500 text-sm">Total Earnings</p>
            <p class="text-3xl font-bold text-purple-600">R{{ number_format($totalEarnings / 100, 2) }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <form method="GET" action="{{ route('admin.wallet.cohort-funds') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <select name="status" class="w-full px-4 py-2 border border-slate-200 rounded-xl bg-white">
                    <option value="">All Status</option>
                    <option value="locked" {{ request('status') == 'locked' ? 'selected' : '' }}>Locked</option>
                    <option value="unlocked" {{ request('status') == 'unlocked' ? 'selected' : '' }}>Unlocked</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <select name="cohort_id" class="w-full px-4 py-2 border border-slate-200 rounded-xl bg-white">
                    <option value="">All Cohorts</option>
                    @foreach($cohorts as $cohort)
                        <option value="{{ $cohort->id }}" {{ request('cohort_id') == $cohort->id ? 'selected' : '' }}>
                            {{ $cohort->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition">
                Filter
            </button>
            @if(request()->hasAny(['status', 'cohort_id']))
            <a href="{{ route('admin.wallet.cohort-funds') }}" class="px-6 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Funds Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left py-4 px-6 text-sm font-bold text-slate-700">Partner</th>
                        <th class="text-left py-4 px-6 text-sm font-bold text-slate-700">Cohort</th>
                        <th class="text-right py-4 px-6 text-sm font-bold text-slate-700">Principal</th>
                        <th class="text-right py-4 px-6 text-sm font-bold text-slate-700">Current Value</th>
                        <th class="text-right py-4 px-6 text-sm font-bold text-slate-700">Earnings</th>
                        <th class="text-center py-4 px-6 text-sm font-bold text-slate-700">Status</th>
                        <th class="text-center py-4 px-6 text-sm font-bold text-slate-700">Maturity</th>
                        <th class="text-center py-4 px-6 text-sm font-bold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($funds as $fund)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-6">
                            <div>
                                <p class="font-medium text-slate-900">{{ $fund->wallet->user->full_name ?? 'Unknown' }}</p>
                                <p class="text-xs text-slate-400">{{ $fund->wallet->wallet_id ?? 'N/A' }}</p>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div>
                                <p class="font-medium text-slate-900">{{ $fund->cohort->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-slate-400">{{ $fund->fund_id }}</p>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-medium text-slate-900">{{ $fund->formatted_principal }}</span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-bold text-emerald-600">{{ $fund->formatted_current_value }}</span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-medium text-purple-600">+{{ $fund->formatted_earnings }}</span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($fund->is_locked)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                    <i data-lucide="lock" class="w-3 h-3 mr-1"></i>
                                    Locked
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                    <i data-lucide="unlock" class="w-3 h-3 mr-1"></i>
                                    Open
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($fund->maturity_date)
                                <span class="text-sm text-slate-600">{{ $fund->maturity_date->format('d M Y') }}</span>
                            @else
                                <span class="text-sm text-slate-400">Not set</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <div class="flex items-center justify-center space-x-2">
                                @if($fund->is_locked)
                                    <form action="{{ route('admin.wallet.unlock-fund', $fund) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 bg-emerald-100 text-emerald-600 rounded-lg hover:bg-emerald-200 transition" title="Unlock">
                                            <i data-lucide="unlock" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.wallet.lock-fund', $fund) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 bg-amber-100 text-amber-600 rounded-lg hover:bg-amber-200 transition" title="Lock">
                                            <i data-lucide="lock" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                                <button type="button" 
                                        onclick="openMaturityModal({{ $fund->id }}, '{{ $fund->maturity_date?->format('Y-m-d') }}')"
                                        class="p-2 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 transition" title="Set Maturity">
                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="inbox" class="w-8 h-8 text-slate-400"></i>
                            </div>
                            <p class="text-slate-500">No cohort funds found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($funds->hasPages())
        <div class="p-6 border-t border-slate-100">
            {{ $funds->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Maturity Date Modal -->
<div id="maturity-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeMaturityModal()"></div>
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl p-6">
            <h3 class="font-bold text-lg text-slate-900 mb-4">Set Maturity Date</h3>
            <form id="maturity-form" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Maturity Date</label>
                    <input type="date" 
                           name="maturity_date" 
                           id="maturity-date-input"
                           min="{{ now()->addDay()->format('Y-m-d') }}"
                           class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-purple-400 focus:ring-4 focus:ring-purple-100 transition"
                           required>
                </div>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeMaturityModal()" class="flex-1 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    function openMaturityModal(fundId, currentDate) {
        document.getElementById('maturity-form').action = `/admin/wallet/cohort-funds/${fundId}/maturity`;
        if (currentDate) {
            document.getElementById('maturity-date-input').value = currentDate;
        }
        document.getElementById('maturity-modal').classList.remove('hidden');
    }

    function closeMaturityModal() {
        document.getElementById('maturity-modal').classList.add('hidden');
    }
</script>
@endpush
