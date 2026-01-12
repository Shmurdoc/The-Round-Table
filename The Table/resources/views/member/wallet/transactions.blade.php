@extends('layouts.modern')

@section('title', 'Transaction History - RoundTable')
@section('page-title', 'Transaction History')

@section('content')
<div class="space-y-6 slide-up">
    <!-- Back Button -->
    <a href="{{ route('wallet.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to Wallet
    </a>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
        <form method="GET" action="{{ route('wallet.transactions') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <select name="type" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition bg-white">
                    <option value="">All Types</option>
                    <option value="deposit" {{ request('type') == 'deposit' ? 'selected' : '' }}>Deposits</option>
                    <option value="withdrawal" {{ request('type') == 'withdrawal' ? 'selected' : '' }}>Withdrawals</option>
                    <option value="transfer_to_cohort" {{ request('type') == 'transfer_to_cohort' ? 'selected' : '' }}>Cohort Transfers</option>
                    <option value="transfer_from_cohort" {{ request('type') == 'transfer_from_cohort' ? 'selected' : '' }}>Cohort Withdrawals</option>
                    <option value="profit" {{ request('type') == 'profit' ? 'selected' : '' }}>Profits</option>
                </select>
            </div>
            <div class="flex-1 min-w-[200px]">
                <select name="status" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition bg-white">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition">
                Filter
            </button>
            @if(request()->hasAny(['type', 'status']))
            <a href="{{ route('wallet.transactions') }}" class="px-6 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                Clear
            </a>
            @endif
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-100">
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="download" class="w-5 h-5 text-emerald-600"></i>
                </div>
            </div>
            <p class="text-slate-500 text-sm">Total Deposited</p>
            <p class="font-bold text-xl text-slate-900">R{{ number_format($wallet->total_deposited / 100, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100">
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="upload" class="w-5 h-5 text-amber-600"></i>
                </div>
            </div>
            <p class="text-slate-500 text-sm">Total Withdrawn</p>
            <p class="font-bold text-xl text-slate-900">R{{ number_format($wallet->total_withdrawn / 100, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100">
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
            <p class="text-slate-500 text-sm">Total Earnings</p>
            <p class="font-bold text-xl text-emerald-600">+R{{ number_format($wallet->total_earnings / 100, 2) }}</p>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-100">
            <div class="flex items-center space-x-3 mb-2">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="wallet" class="w-5 h-5 text-slate-600"></i>
                </div>
            </div>
            <p class="text-slate-500 text-sm">Current Balance</p>
            <p class="font-bold text-xl text-slate-900">R{{ number_format($wallet->balance / 100, 2) }}</p>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-slate-900 text-lg">All Transactions</h3>
        </div>
        
        @if($transactions->count() > 0)
        <div class="divide-y divide-slate-50">
            @foreach($transactions as $txn)
            <div class="p-5 hover:bg-slate-50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center
                            {{ in_array($txn->type, ['deposit', 'transfer_from_cohort', 'profit', 'refund']) ? 'bg-emerald-100' : 'bg-amber-100' }}">
                            <i data-lucide="{{ $txn->type_icon }}" class="w-6 h-6 
                                {{ in_array($txn->type, ['deposit', 'transfer_from_cohort', 'profit', 'refund']) ? 'text-emerald-600' : 'text-amber-600' }}"></i>
                        </div>
                        <div>
                            <p class="font-bold text-slate-900">{{ ucfirst(str_replace('_', ' ', $txn->type)) }}</p>
                            <p class="text-sm text-slate-400">{{ $txn->created_at->format('d M Y, H:i') }}</p>
                            @if($txn->description)
                                <p class="text-sm text-slate-500 mt-1">{{ $txn->description }}</p>
                            @endif
                            @if($txn->payment_reference)
                                <p class="text-xs text-slate-400 mt-1 font-mono">Ref: {{ $txn->payment_reference }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="font-bold text-lg {{ in_array($txn->type, ['deposit', 'transfer_from_cohort', 'profit', 'refund']) ? 'text-emerald-600' : 'text-slate-900' }}">
                            {{ in_array($txn->type, ['deposit', 'transfer_from_cohort', 'profit', 'refund']) ? '+' : '-' }}{{ $txn->formatted_amount }}
                        </p>
                        <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-full mt-1 {{ $txn->status_badge }}">
                            {{ ucfirst($txn->status) }}
                        </span>
                        @if($txn->balance_after)
                            <p class="text-xs text-slate-400 mt-1">Balance: R{{ number_format($txn->balance_after / 100, 2) }}</p>
                        @endif
                    </div>
                </div>

                @if($txn->status === 'rejected' && $txn->admin_notes)
                <div class="mt-3 p-3 bg-red-50 rounded-xl">
                    <p class="text-sm text-red-700">
                        <i data-lucide="x-circle" class="w-4 h-4 inline mr-1"></i>
                        {{ $txn->admin_notes }}
                    </p>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="p-6 border-t border-slate-100">
            {{ $transactions->withQueryString()->links() }}
        </div>
        @endif
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="receipt" class="w-8 h-8 text-slate-400"></i>
            </div>
            <p class="text-slate-500">No transactions found</p>
            <p class="text-slate-400 text-sm mt-1">
                @if(request()->hasAny(['type', 'status']))
                    Try adjusting your filters
                @else
                    Your transactions will appear here
                @endif
            </p>
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
