@extends('layouts.modern')

@section('title', 'Control Panel - ' . $cohort->title)
@section('page-title', 'Cohort Control Panel')

@section('content')
<div class="slide-up space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.cohorts.show', $cohort) }}" class="text-slate-500 hover:text-slate-700">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h1 class="text-3xl font-extrabold text-slate-900">{{ $cohort->title }}</h1>
            </div>
            <p class="text-slate-500">Manage cohort operations, profits, and member distributions</p>
        </div>
        
        <!-- Status Badge -->
        @php
            $statusColors = [
                'draft' => 'bg-slate-100 text-slate-700',
                'pending_approval' => 'bg-amber-100 text-amber-700',
                'approved' => 'bg-blue-100 text-blue-700',
                'funding' => 'bg-purple-100 text-purple-700',
                'operational' => 'bg-emerald-100 text-emerald-700',
                'paused' => 'bg-red-100 text-red-700',
                'completed' => 'bg-slate-100 text-slate-700',
            ];
        @endphp
        <span class="px-4 py-2 text-sm font-bold rounded-full {{ $statusColors[$cohort->status] ?? 'bg-slate-100 text-slate-600' }}">
            {{ ucfirst(str_replace('_', ' ', $cohort->status)) }}
        </span>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <span class="text-sm text-slate-500">Total Profit</span>
            </div>
            <div class="text-2xl font-bold text-slate-900">R{{ number_format($stats['total_profit'] / 100, 2) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5 text-amber-600"></i>
                </div>
                <span class="text-sm text-slate-500">Your Share</span>
            </div>
            <div class="text-2xl font-bold text-slate-900">R{{ number_format($stats['admin_profit'] / 100, 2) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                </div>
                <span class="text-sm text-slate-500">Members Paid</span>
            </div>
            <div class="text-2xl font-bold text-slate-900">R{{ number_format($stats['members_distributed'] / 100, 2) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-purple-600"></i>
                </div>
                <span class="text-sm text-slate-500">Pending</span>
            </div>
            <div class="text-2xl font-bold text-slate-900">R{{ number_format($stats['pending_distribution'] / 100, 2) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Controls -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Cohort Operations -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="settings" class="w-5 h-5 text-slate-500"></i>
                        Cohort Operations
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($cohort->status === 'approved' || $cohort->status === 'funding')
                        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
                            <h4 class="font-bold text-emerald-900 mb-2">Ready to Launch?</h4>
                            <p class="text-sm text-emerald-700 mb-4">
                                Current capital: R{{ number_format($cohort->current_capital / 100, 0) }} / 
                                MVC: R{{ number_format($cohort->minimum_viable_capital / 100, 0) }}
                            </p>
                            @if($cohort->current_capital >= $cohort->minimum_viable_capital)
                                <form action="{{ route('admin.cohorts.activate', $cohort) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition flex items-center justify-center gap-2">
                                        <i data-lucide="rocket" class="w-5 h-5"></i>
                                        Launch Cohort
                                    </button>
                                </form>
                            @else
                                <div class="px-6 py-3 bg-slate-200 text-slate-500 font-bold rounded-xl text-center">
                                    Waiting for MVC to be reached
                                </div>
                            @endif
                        </div>
                    @elseif($cohort->status === 'operational')
                        <div class="flex gap-4">
                            <form action="{{ route('admin.cohorts.pause', $cohort) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-6 py-3 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition flex items-center justify-center gap-2">
                                    <i data-lucide="pause" class="w-5 h-5"></i>
                                    Pause Operations
                                </button>
                            </form>
                        </div>
                    @elseif($cohort->status === 'paused')
                        <form action="{{ route('admin.cohorts.resume', $cohort) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition flex items-center justify-center gap-2">
                                <i data-lucide="play" class="w-5 h-5"></i>
                                Resume Operations
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Record Profit -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="plus-circle" class="w-5 h-5 text-slate-500"></i>
                        Record Daily Profit
                    </h3>
                </div>
                <form action="{{ route('admin.cohorts.record-profit', $cohort) }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Profit Amount (ZAR)</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">R</span>
                                <input type="number" name="amount" step="0.01" min="0.01" required
                                    class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Profit Date</label>
                            <input type="date" name="profit_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="2" placeholder="Describe the source of this profit..."
                            class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500"></textarea>
                    </div>
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-amber-700">Admin Share ({{ $cohort->admin_profit_share ?? 50 }}%)</span>
                            <span class="text-amber-700">Members Share ({{ 100 - ($cohort->admin_profit_share ?? 50) }}%)</span>
                        </div>
                    </div>
                    <button type="submit" class="w-full px-6 py-3 bg-amber-500 text-white font-bold rounded-xl hover:bg-amber-600 transition">
                        Record Profit
                    </button>
                </form>
            </div>

            <!-- Distribute Profits -->
            @if($stats['pending_distribution'] > 0)
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-slate-100 bg-emerald-50">
                        <h3 class="font-bold text-emerald-900 flex items-center gap-2">
                            <i data-lucide="send" class="w-5 h-5 text-emerald-600"></i>
                            Distribute to Members
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <p class="text-slate-600">
                                You have <span class="font-bold text-emerald-600">R{{ number_format($stats['pending_distribution'] / 100, 2) }}</span> 
                                in undistributed profits ready to be sent to {{ $stats['member_count'] }} active members.
                            </p>
                        </div>
                        <form action="{{ route('admin.cohorts.distribute-profit', $cohort) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition flex items-center justify-center gap-2">
                                <i data-lucide="banknote" class="w-5 h-5"></i>
                                Distribute R{{ number_format($stats['pending_distribution'] / 100, 2) }} to Members
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Cohort Info -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-900">Cohort Info</h3>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Total Capital</span>
                        <span class="font-bold text-slate-900">R{{ number_format($stats['total_capital'] / 100, 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Active Members</span>
                        <span class="font-bold text-slate-900">{{ $stats['member_count'] }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Profit Share</span>
                        <span class="font-bold text-slate-900">{{ $cohort->admin_profit_share ?? 50 }}% Admin / {{ 100 - ($cohort->admin_profit_share ?? 50) }}% Members</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Duration</span>
                        <span class="font-bold text-slate-900">{{ $cohort->duration_months }} Months</span>
                    </div>
                    @if($cohort->launched_at)
                        <div class="flex justify-between">
                            <span class="text-slate-500">Launched</span>
                            <span class="font-bold text-slate-900">{{ $cohort->launched_at->format('d M Y') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Profits -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-900">Recent Profits</h3>
                </div>
                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                    @forelse($cohort->dailyProfits as $profit)
                        <div class="p-4 flex items-center justify-between">
                            <div>
                                <div class="font-bold text-slate-900">R{{ number_format($profit->total_profit / 100, 2) }}</div>
                                <div class="text-xs text-slate-500">{{ \Carbon\Carbon::parse($profit->profit_date)->format('d M Y') }}</div>
                            </div>
                            @if($profit->distributed)
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg">Distributed</span>
                            @else
                                <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-lg">Pending</span>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-500">
                            No profits recorded yet
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h3 class="font-bold text-slate-900">Quick Actions</h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('admin.cohorts.show', $cohort) }}" 
                       class="block w-full px-4 py-3 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition text-center">
                        View Full Details
                    </a>
                    <a href="{{ route('admin.cohorts.reports.create', $cohort) }}" 
                       class="block w-full px-4 py-3 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition text-center">
                        Create Report
                    </a>
                    <a href="{{ route('admin.cohorts.profits.index', $cohort) }}" 
                       class="block w-full px-4 py-3 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200 transition text-center">
                        Profit History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
