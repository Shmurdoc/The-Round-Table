@extends('layouts.modern')

@section('title', 'Platform Admin - RoundTable')
@section('page-title', 'Platform Admin')

@section('content')
<div class="slide-up space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="crown" class="w-5 h-5 text-amber-600"></i>
                </div>
                Platform Admin
            </h1>
            <p class="text-slate-500 text-sm mt-2">Manage users, cohorts, and platform settings</p>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Total Users</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($totalUsers ?? 0) }}</div>
            <div class="text-xs text-emerald-600 flex items-center gap-1 mt-1">
                <i data-lucide="trending-up" class="w-3 h-3"></i>
                <span>+{{ $newUsersThisMonth ?? 0 }} this month</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Total Cohorts</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="layers" class="w-4 h-4 text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($totalCohorts ?? 0) }}</div>
            <div class="text-xs text-slate-500">Active cohorts</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Capital Deployed</span>
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="wallet" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R{{ number_format(($totalCapital ?? 0) / 100, 0) }}</div>
            <div class="text-xs text-slate-500">Across all cohorts</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Pending KYC</span>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="file-check" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ $pendingKyc ?? 0 }}</div>
            @if(($pendingKyc ?? 0) > 0)
                <a href="{{ route('platform-admin.kyc') }}" class="text-xs text-amber-600 hover:text-amber-700 flex items-center gap-1">
                    <span>Review now</span>
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            @else
                <div class="text-xs text-emerald-600">All verified</div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Admin Actions -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Quick Actions</h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('platform-admin.users') }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <i data-lucide="users-2" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-slate-900 text-sm">Manage Users</div>
                            <div class="text-xs text-slate-500">View and manage all users</div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </a>
                    
                    <a href="{{ route('platform-admin.kyc') }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                            <i data-lucide="file-check" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-slate-900 text-sm">KYC Review</div>
                            <div class="text-xs text-slate-500">Review pending verifications</div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </a>
                    
                    <a href="{{ route('admin.cohorts.create') }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                            <i data-lucide="plus-circle" class="w-5 h-5 text-emerald-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-slate-900 text-sm">Create Cohort</div>
                            <div class="text-xs text-slate-500">Start a new investment cohort</div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </a>
                    
                    <a href="{{ route('admin.distributions.index') }}" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                            <i data-lucide="banknote" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-slate-900 text-sm">Distributions</div>
                            <div class="text-xs text-slate-500">Manage dividend payouts</div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900">Recent Activity</h3>
                    <span class="text-xs text-slate-500">Last 24 hours</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($recentActivity ?? [] as $activity)
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                    @if($activity['type'] === 'user') bg-blue-100 @elseif($activity['type'] === 'kyc') bg-amber-100 @else bg-emerald-100 @endif">
                                    <i data-lucide="{{ $activity['icon'] ?? 'activity' }}" class="w-4 h-4 
                                        @if($activity['type'] === 'user') text-blue-600 @elseif($activity['type'] === 'kyc') text-amber-600 @else text-emerald-600 @endif"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-900">{{ $activity['message'] }}</p>
                                    <p class="text-xs text-slate-500">{{ $activity['time'] }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <i data-lucide="activity" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                            <p class="text-sm text-slate-500">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Cohorts Overview -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mt-6">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900">Cohorts Overview</h3>
                    <a href="{{ route('cohorts.index') }}" class="text-xs font-bold text-amber-600 hover:text-amber-700">View All</a>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($cohorts ?? [] as $cohort)
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">
                                        <i data-lucide="layers" class="w-5 h-5 text-slate-600"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900 text-sm">{{ $cohort->title }}</div>
                                        <div class="text-xs text-slate-500">{{ $cohort->member_count ?? 0 }} members</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-mono font-bold text-slate-900">R{{ number_format($cohort->current_capital / 100, 0) }}</div>
                                    <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded
                                        {{ $cohort->status === 'operational' ? 'bg-emerald-100 text-emerald-700' : 
                                           ($cohort->status === 'funding' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                        {{ ucfirst($cohort->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center">
                            <i data-lucide="layers" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                            <p class="text-sm text-slate-500">No cohorts yet</p>
                        </div>
                    @endforelse
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
