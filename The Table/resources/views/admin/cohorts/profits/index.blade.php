@extends('layouts.admin')

@section('title', 'Profit Management - ' . $cohort->name)
@section('page-title', 'Profit Management')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.cohorts.show', $cohort) }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
            <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
            Back to {{ $cohort->name }}
        </a>
        <a href="{{ route('admin.cohorts.profits.create', $cohort) }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition">
            <i data-lucide="plus" class="w-5 h-5 mr-2"></i>
            Record Daily Profit
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-emerald-600"></i>
                </div>
            </div>
            <p class="text-slate-500 text-sm">Total Profit Generated</p>
            <p class="text-3xl font-bold text-emerald-600">R{{ number_format($stats['total_profit'] / 100, 2) }}</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="user" class="w-6 h-6 text-amber-600"></i>
                </div>
            </div>
            <p class="text-slate-500 text-sm">Admin Share ({{ $stats['admin_share_rate'] }}%)</p>
            <p class="text-3xl font-bold text-amber-600">R{{ number_format($stats['admin_taken'] / 100, 2) }}</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
            <p class="text-slate-500 text-sm">Members Distributed</p>
            <p class="text-3xl font-bold text-purple-600">R{{ number_format($stats['members_distributed'] / 100, 2) }}</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-rose-600"></i>
                </div>
            </div>
            <p class="text-slate-500 text-sm">Pending Distribution</p>
            <p class="text-3xl font-bold text-rose-600">R{{ number_format($stats['pending_distribution'] / 100, 2) }}</p>
        </div>
    </div>

    <!-- Settings Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Admin Share Rate -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-bold text-slate-900 mb-4">Admin Profit Share</h3>
            <form action="{{ route('admin.cohorts.profits.share-rate', $cohort) }}" method="POST">
                @csrf
                <div class="flex items-center space-x-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="number" 
                                   name="admin_profit_share" 
                                   value="{{ $cohort->admin_profit_share }}"
                                   min="0" 
                                   max="100"
                                   step="0.01"
                                   class="w-full px-4 py-3 text-2xl font-bold border-2 border-slate-200 rounded-xl focus:border-amber-400 focus:ring-4 focus:ring-amber-100 transition">
                            <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">%</span>
                        </div>
                    </div>
                    <button type="submit" class="px-6 py-3 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition">
                        Update
                    </button>
                </div>
                <p class="text-sm text-slate-400 mt-2">Percentage of daily profit kept by admin</p>
            </form>
        </div>

        <!-- Cohort Targets -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="font-bold text-slate-900 mb-4">Cohort Targets</h3>
            <form action="{{ route('admin.cohorts.profits.targets', $cohort) }}" method="POST">
                @csrf
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Target Amount (R)</label>
                            <input type="number" 
                                   name="target_amount" 
                                   value="{{ $cohort->target_amount / 100 }}"
                                   min="0"
                                   step="0.01"
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-600 mb-1">Target Members</label>
                            <input type="number" 
                                   name="target_member_count" 
                                   value="{{ $cohort->target_member_count ?: $cohort->max_members }}"
                                   min="1"
                                   class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-600 mb-1">Per Member Target (R)</label>
                        <input type="number" 
                               name="per_member_target" 
                               value="{{ $cohort->per_member_target / 100 }}"
                               min="0"
                               step="0.01"
                               placeholder="Auto-calculated if empty"
                               class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:border-purple-400 focus:ring-2 focus:ring-purple-100">
                    </div>
                    <button type="submit" class="w-full py-2 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 transition">
                        Update Targets
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Member Stats -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-slate-900">Member Overview</h3>
            <a href="{{ route('admin.cohorts.profits.members', $cohort) }}" class="text-sm font-bold text-amber-600 hover:text-amber-700">
                View Details →
            </a>
        </div>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-2xl font-bold text-slate-900">{{ $stats['active_members'] }}</p>
                <p class="text-sm text-slate-500">Active Members</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-4">
                <p class="text-2xl font-bold text-amber-600">{{ $stats['special_members'] }}</p>
                <p class="text-sm text-amber-700">Special Partners</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-4">
                <p class="text-2xl font-bold text-emerald-600">{{ $stats['active_members'] - $stats['special_members'] }}</p>
                <p class="text-sm text-emerald-700">Regular Partners</p>
            </div>
        </div>
    </div>

    <!-- Daily Profits Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900">Daily Profit Records</h3>
            @if($stats['pending_distribution'] > 0)
            <form action="{{ route('admin.cohorts.profits.credit-wallets', $cohort) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition">
                    <i data-lucide="wallet" class="w-4 h-4 inline mr-1"></i>
                    Credit All to Wallets
                </button>
            </form>
            @endif
        </div>

        @if($dailyProfits->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left py-4 px-6 text-sm font-bold text-slate-700">Date</th>
                        <th class="text-right py-4 px-6 text-sm font-bold text-slate-700">Total Profit</th>
                        <th class="text-right py-4 px-6 text-sm font-bold text-slate-700">Admin Share</th>
                        <th class="text-right py-4 px-6 text-sm font-bold text-slate-700">Members Share</th>
                        <th class="text-center py-4 px-6 text-sm font-bold text-slate-700">Status</th>
                        <th class="text-center py-4 px-6 text-sm font-bold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($dailyProfits as $profit)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-6">
                            <span class="font-medium text-slate-900">{{ $profit->profit_date->format('d M Y') }}</span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-bold text-emerald-600">{{ $profit->formatted_total_profit }}</span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-medium text-amber-600">{{ $profit->formatted_admin_share }}</span>
                            <span class="text-xs text-slate-400">({{ $profit->admin_share_percentage }}%)</span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-medium text-purple-600">{{ $profit->formatted_members_share }}</span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($profit->distributed)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                    <i data-lucide="check" class="w-3 h-3 mr-1"></i>
                                    Distributed
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    Pending
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if(!$profit->distributed)
                            <form action="{{ route('admin.daily-profits.distribute', $profit) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-purple-600 text-white text-sm font-bold rounded-lg hover:bg-purple-700 transition">
                                    Distribute
                                </button>
                            </form>
                            @else
                                <span class="text-sm text-slate-400">
                                    {{ $profit->distributed_at->format('d M H:i') }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($dailyProfits->hasPages())
        <div class="p-6 border-t border-slate-100">
            {{ $dailyProfits->links() }}
        </div>
        @endif
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="bar-chart-3" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500">No profit records yet</p>
            <a href="{{ route('admin.cohorts.profits.create', $cohort) }}" class="inline-block mt-4 text-sm font-bold text-amber-600">
                Record your first profit
            </a>
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
