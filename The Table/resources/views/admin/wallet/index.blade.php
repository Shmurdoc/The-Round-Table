@extends('layouts.admin')

@section('title', 'Wallet Management - Admin')
@section('page-title', 'Wallet Management')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="clock" class="w-6 h-6 text-amber-600"></i>
                </div>
                <span class="text-xs font-bold text-amber-600 bg-amber-100 px-2 py-1 rounded-full">Pending</span>
            </div>
            <p class="text-3xl font-bold text-slate-900">{{ $pendingDeposits->count() }}</p>
            <p class="text-slate-500 text-sm">Pending Deposits</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="upload" class="w-6 h-6 text-purple-600"></i>
                </div>
                <span class="text-xs font-bold text-purple-600 bg-purple-100 px-2 py-1 rounded-full">Pending</span>
            </div>
            <p class="text-3xl font-bold text-slate-900">{{ $pendingWithdrawals->count() }}</p>
            <p class="text-slate-500 text-sm">Pending Withdrawals</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="lock" class="w-6 h-6 text-emerald-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900">{{ $lockedFunds }}</p>
            <p class="text-slate-500 text-sm">Locked Cohort Funds</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-6 h-6 text-slate-600"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900">{{ $profitSettings->daily_profit_rate ?? 0 }}%</p>
            <p class="text-slate-500 text-sm">Daily Profit Rate</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="flex flex-wrap gap-4">
        <a href="{{ route('admin.wallet.profit-settings') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition">
            <i data-lucide="settings" class="w-5 h-5 mr-2"></i>
            Profit Settings
        </a>
        <form action="{{ route('admin.wallet.apply-daily-profits') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition">
                <i data-lucide="play" class="w-5 h-5 mr-2"></i>
                Apply Daily Profits
            </button>
        </form>
        <form action="{{ route('admin.wallet.auto-lock') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 text-white font-bold rounded-xl hover:bg-amber-700 transition">
                <i data-lucide="lock" class="w-5 h-5 mr-2"></i>
                Run Auto-Lock
            </button>
        </form>
        <a href="{{ route('admin.wallet.cohort-funds') }}" class="inline-flex items-center px-4 py-2 bg-slate-800 text-white font-bold rounded-xl hover:bg-slate-700 transition">
            <i data-lucide="layers" class="w-5 h-5 mr-2"></i>
            View Cohort Funds
        </a>
    </div>

    <!-- Pending Deposits -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-lg">Pending Deposits</h3>
        </div>
        
        @if($pendingDeposits->count() > 0)
        <div class="divide-y divide-slate-50">
            @foreach($pendingDeposits as $deposit)
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="download" class="w-6 h-6 text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ $deposit->wallet->user->full_name ?? 'Unknown' }}</p>
                            <p class="text-sm text-slate-500">{{ $deposit->wallet->wallet_id ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-400">{{ $deposit->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-xl text-emerald-600">{{ $deposit->formatted_amount }}</p>
                        @if($deposit->metadata && isset($deposit->metadata['proof']))
                            <a href="{{ asset('storage/' . $deposit->metadata['proof']) }}" target="_blank" class="text-xs text-blue-600 hover:underline">
                                View Proof
                            </a>
                        @endif
                    </div>
                </div>

                @if($deposit->description)
                <p class="text-sm text-slate-600 mt-3 bg-slate-50 rounded-lg p-3">{{ $deposit->description }}</p>
                @endif

                <div class="flex items-center space-x-3 mt-4">
                    <form action="{{ route('admin.wallet.approve-deposit', $deposit) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition">
                            <i data-lucide="check" class="w-4 h-4 inline mr-1"></i>
                            Approve
                        </button>
                    </form>
                    <form action="{{ route('admin.wallet.reject-deposit', $deposit) }}" method="POST" class="inline" 
                          onsubmit="return confirm('Are you sure you want to reject this deposit?')">
                        @csrf
                        <input type="hidden" name="reason" value="Deposit rejected by admin">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition">
                            <i data-lucide="x" class="w-4 h-4 inline mr-1"></i>
                            Reject
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="check-circle" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500">No pending deposits</p>
        </div>
        @endif
    </div>

    <!-- Pending Withdrawals -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-lg">Pending Withdrawals</h3>
        </div>
        
        @if($pendingWithdrawals->count() > 0)
        <div class="divide-y divide-slate-50">
            @foreach($pendingWithdrawals as $withdrawal)
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="upload" class="w-6 h-6 text-amber-600"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ $withdrawal->wallet->user->full_name ?? 'Unknown' }}</p>
                            <p class="text-sm text-slate-500">{{ $withdrawal->wallet->wallet_id ?? 'N/A' }}</p>
                            <p class="text-xs text-slate-400">{{ $withdrawal->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-xl text-amber-600">{{ $withdrawal->formatted_amount }}</p>
                    </div>
                </div>

                @if($withdrawal->metadata && isset($withdrawal->metadata['bank_details']))
                <div class="mt-3 p-3 bg-slate-50 rounded-lg text-sm">
                    <p class="font-medium text-slate-700 mb-2">Bank Details:</p>
                    <div class="grid grid-cols-2 gap-2 text-slate-600">
                        <p>Bank: {{ $withdrawal->metadata['bank_details']['bank_name'] ?? '-' }}</p>
                        <p>Account: {{ $withdrawal->metadata['bank_details']['account_number'] ?? '-' }}</p>
                        <p>Branch: {{ $withdrawal->metadata['bank_details']['branch_code'] ?? '-' }}</p>
                        <p>Type: {{ ucfirst($withdrawal->metadata['bank_details']['account_type'] ?? '-') }}</p>
                    </div>
                </div>
                @endif

                <div class="flex items-center space-x-3 mt-4">
                    <form action="{{ route('admin.wallet.approve-withdrawal', $withdrawal) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 transition">
                            <i data-lucide="check" class="w-4 h-4 inline mr-1"></i>
                            Approve & Process
                        </button>
                    </form>
                    <form action="{{ route('admin.wallet.reject-withdrawal', $withdrawal) }}" method="POST" class="inline"
                          onsubmit="return confirm('Are you sure you want to reject this withdrawal?')">
                        @csrf
                        <input type="hidden" name="reason" value="Withdrawal rejected by admin">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 transition">
                            <i data-lucide="x" class="w-4 h-4 inline mr-1"></i>
                            Reject
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="check-circle" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500">No pending withdrawals</p>
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
