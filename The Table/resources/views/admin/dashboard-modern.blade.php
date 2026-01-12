@extends('layouts.modern')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Hero Stats Section -->
<section class="mb-10">
    <div class="flex items-center gap-3 mb-6">
        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/30 flex items-center justify-center">
            <i data-lucide="layout-dashboard" class="w-6 h-6 text-amber-400"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
            <p class="text-slate-400 text-sm">Manage your cohorts and members</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Members -->
        <div class="group relative bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-5 hover:border-amber-500/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs text-slate-500 uppercase tracking-wider font-medium">Total Members</span>
                <div class="w-8 h-8 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4 text-blue-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-white mb-1">{{ number_format($totalMembers ?? 0) }}</div>
            <div class="text-xs text-green-400 flex items-center gap-1">
                <i data-lucide="trending-up" class="w-3 h-3"></i>
                <span>Active across all cohorts</span>
            </div>
        </div>

        <!-- Total Invested -->
        <div class="group relative bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-5 hover:border-amber-500/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs text-slate-500 uppercase tracking-wider font-medium">Capital Under Management</span>
                <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                    <i data-lucide="wallet" class="w-4 h-4 text-green-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-white mb-1">R{{ number_format(($totalInvested ?? 0) / 100, 0) }}</div>
            <div class="text-xs text-slate-400">Pooled capital</div>
        </div>

        <!-- Active Cohorts -->
        <div class="group relative bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-5 hover:border-amber-500/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs text-slate-500 uppercase tracking-wider font-medium">Active Cohorts</span>
                <div class="w-8 h-8 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <i data-lucide="layers" class="w-4 h-4 text-purple-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-white mb-1">{{ isset($cohorts) ? $cohorts->count() : ($activeCohorts ?? 0) }}</div>
            <div class="text-xs text-slate-400">Your managed cohorts</div>
        </div>

        <!-- Pending KYC -->
        <div class="group relative bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 p-5 hover:border-amber-500/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs text-slate-500 uppercase tracking-wider font-medium">Pending KYC</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center">
                    <i data-lucide="file-check" class="w-4 h-4 text-amber-400"></i>
                </div>
            </div>
            <div class="text-3xl font-bold text-white mb-1">{{ $pendingKyc ?? 0 }}</div>
            @if(($pendingKyc ?? 0) > 0)
                <a href="{{ route('admin.kyc') }}" class="text-xs text-amber-400 hover:text-amber-300 flex items-center gap-1">
                    <span>Review now</span>
                    <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </a>
            @else
                <div class="text-xs text-green-400">All verified</div>
            @endif
        </div>
    </div>
</section>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Left Column: Cohorts & Transactions -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Your Cohorts -->
        <div class="bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 overflow-hidden">
            <div class="p-5 border-b border-slate-700/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center">
                        <i data-lucide="briefcase" class="w-4 h-4 text-amber-400"></i>
                    </div>
                    <h2 class="font-semibold text-white">Your Cohorts</h2>
                </div>
                <a href="{{ route('admin.cohorts.create') }}" class="px-3 py-1.5 text-xs font-medium text-white bg-amber-500/20 border border-amber-500/30 rounded-lg hover:bg-amber-500/30 transition-colors">
                    <i data-lucide="plus" class="w-3 h-3 inline mr-1"></i>
                    New Cohort
                </a>
            </div>
            <div class="divide-y divide-slate-700/50">
                @forelse($myCohorts ?? [] as $cohort)
                    <div class="p-4 hover:bg-slate-700/20 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-700 to-slate-800 flex items-center justify-center">
                                    <span class="text-lg font-bold text-amber-400">{{ substr($cohort->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    <a href="{{ route('admin.cohorts.show', $cohort) }}" class="font-medium text-white hover:text-amber-400 transition-colors">
                                        {{ $cohort->name }}
                                    </a>
                                    <p class="text-xs text-slate-400">{{ $cohort->members_count ?? 0 }}/{{ $cohort->max_members }} members</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @php
                                    $statusColors = [
                                        'open' => 'bg-green-500/20 text-green-400',
                                        'active' => 'bg-blue-500/20 text-blue-400',
                                        'closed' => 'bg-slate-500/20 text-slate-400',
                                        'completed' => 'bg-purple-500/20 text-purple-400',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full {{ $statusColors[$cohort->status] ?? 'bg-slate-500/20 text-slate-400' }}">
                                    {{ ucfirst($cohort->status) }}
                                </span>
                                <span class="text-sm font-mono text-slate-300">R{{ number_format(($cohort->contribution_amount ?? 0) / 100, 0) }}</span>
                            </div>
                        </div>
                        <!-- Progress Bar -->
                        <div class="mt-3">
                            @php
                                $progress = $cohort->max_members > 0 ? (($cohort->members_count ?? 0) / $cohort->max_members) * 100 : 0;
                            @endphp
                            <div class="w-full h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-amber-500 to-amber-400 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-slate-700/50 flex items-center justify-center">
                            <i data-lucide="folder-open" class="w-6 h-6 text-slate-500"></i>
                        </div>
                        <p class="text-slate-400 text-sm mb-3">No cohorts yet</p>
                        <a href="{{ route('admin.cohorts.create') }}" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-amber-500/20 border border-amber-500/30 rounded-lg hover:bg-amber-500/30 transition-colors">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Create Your First Cohort
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 overflow-hidden">
            <div class="p-5 border-b border-slate-700/50 flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center">
                    <i data-lucide="arrow-left-right" class="w-4 h-4 text-green-400"></i>
                </div>
                <h2 class="font-semibold text-white">Recent Transactions</h2>
            </div>
            <div class="divide-y divide-slate-700/50">
                @forelse($recentTransactions ?? [] as $transaction)
                    <div class="p-4 hover:bg-slate-700/20 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                @php
                                    $typeIcons = [
                                        'contribution' => ['icon' => 'arrow-up-right', 'color' => 'text-green-400 bg-green-500/20'],
                                        'distribution' => ['icon' => 'arrow-down-left', 'color' => 'text-blue-400 bg-blue-500/20'],
                                        'withdrawal' => ['icon' => 'arrow-down', 'color' => 'text-red-400 bg-red-500/20'],
                                    ];
                                    $typeInfo = $typeIcons[$transaction->type] ?? ['icon' => 'circle', 'color' => 'text-slate-400 bg-slate-500/20'];
                                @endphp
                                <div class="w-8 h-8 rounded-lg {{ $typeInfo['color'] }} flex items-center justify-center">
                                    <i data-lucide="{{ $typeInfo['icon'] }}" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ $transaction->user->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-400">{{ ucfirst($transaction->type) }} • {{ $transaction->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="font-mono text-sm {{ $transaction->type === 'contribution' ? 'text-green-400' : 'text-slate-300' }}">
                                    {{ $transaction->type === 'contribution' ? '+' : '-' }}R{{ number_format(($transaction->amount ?? 0) / 100, 2) }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-slate-700/50 flex items-center justify-center">
                            <i data-lucide="receipt" class="w-6 h-6 text-slate-500"></i>
                        </div>
                        <p class="text-slate-400 text-sm">No transactions yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Actions & Pending -->
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 overflow-hidden">
            <div class="p-5 border-b border-slate-700/50">
                <h2 class="font-semibold text-white flex items-center gap-2">
                    <i data-lucide="zap" class="w-4 h-4 text-amber-400"></i>
                    Quick Actions
                </h2>
            </div>
            <div class="p-4 space-y-2">
                <a href="{{ route('admin.cohorts.create') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-700/30 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center group-hover:bg-amber-500/30 transition-colors">
                        <i data-lucide="plus-circle" class="w-5 h-5 text-amber-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">Create Cohort</p>
                        <p class="text-xs text-slate-400">Start a new investment round</p>
                    </div>
                </a>
                <a href="{{ route('admin.distributions.index') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-700/30 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center group-hover:bg-green-500/30 transition-colors">
                        <i data-lucide="banknote" class="w-5 h-5 text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">Manage Distributions</p>
                        <p class="text-xs text-slate-400">Process member payouts</p>
                    </div>
                </a>
                <a href="{{ route('admin.members') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-700/30 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center group-hover:bg-blue-500/30 transition-colors">
                        <i data-lucide="users" class="w-5 h-5 text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">Manage Members</p>
                        <p class="text-xs text-slate-400">View and edit member details</p>
                    </div>
                </a>
                <a href="{{ route('admin.kyc') }}" class="flex items-center gap-3 p-3 rounded-xl hover:bg-slate-700/30 transition-colors group">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center group-hover:bg-purple-500/30 transition-colors">
                        <i data-lucide="shield-check" class="w-5 h-5 text-purple-400"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-white">KYC Review</p>
                        <p class="text-xs text-slate-400">Verify member identities</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Pending KYC Verifications -->
        <div class="bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl rounded-2xl border border-slate-700/50 overflow-hidden">
            <div class="p-5 border-b border-slate-700/50 flex items-center justify-between">
                <h2 class="font-semibold text-white flex items-center gap-2">
                    <i data-lucide="user-check" class="w-4 h-4 text-amber-400"></i>
                    Pending KYC
                </h2>
                @if(($pendingKyc ?? 0) > 0)
                    <span class="px-2 py-0.5 text-xs font-medium bg-amber-500/20 text-amber-400 rounded-full">{{ $pendingKyc }}</span>
                @endif
            </div>
            <div class="divide-y divide-slate-700/50">
                @forelse($pendingKycUsers ?? [] as $user)
                    <div class="p-4 hover:bg-slate-700/20 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-600 to-slate-700 flex items-center justify-center">
                                    <span class="text-sm font-medium text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-white">{{ $user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $user->email }}</p>
                                </div>
                            </div>
                            <a href="{{ route('admin.kyc.review', $user) }}" class="px-3 py-1 text-xs font-medium text-amber-400 bg-amber-500/20 rounded-lg hover:bg-amber-500/30 transition-colors">
                                Review
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center">
                        <div class="w-10 h-10 mx-auto mb-2 rounded-xl bg-green-500/20 flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-green-400"></i>
                        </div>
                        <p class="text-sm text-slate-400">All members verified</p>
                    </div>
                @endforelse
            </div>
            @if(($pendingKyc ?? 0) > 5)
                <div class="p-4 border-t border-slate-700/50">
                    <a href="{{ route('admin.kyc') }}" class="block text-center text-sm text-amber-400 hover:text-amber-300 transition-colors">
                        View all {{ $pendingKyc }} pending →
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
