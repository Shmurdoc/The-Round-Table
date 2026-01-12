@extends('layouts.admin')

@section('title', 'Member Profits - ' . $cohort->name)
@section('page-title', 'Member Profit Details')

@section('content')
<div class="space-y-6">
    <!-- Back Button -->
    <a href="{{ route('admin.cohorts.profits.index', $cohort) }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to Profit Management
    </a>

    <!-- Summary Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-bold text-slate-900 mb-4">{{ $cohort->name }} - Member Profit Summary</h3>
        <div class="grid grid-cols-4 gap-4">
            <div class="bg-slate-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-slate-900">{{ $members->count() }}</p>
                <p class="text-sm text-slate-500">Active Members</p>
            </div>
            <div class="bg-emerald-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-emerald-600">R{{ number_format($members->sum('capital_committed') / 100, 2) }}</p>
                <p class="text-sm text-emerald-700">Total Capital</p>
            </div>
            <div class="bg-purple-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-purple-600">R{{ number_format($members->sum('total_profit_received') / 100, 2) }}</p>
                <p class="text-sm text-purple-700">Total Distributed</p>
            </div>
            <div class="bg-amber-50 rounded-xl p-4 text-center">
                <p class="text-2xl font-bold text-amber-600">{{ $members->where('is_special_member', true)->count() }}</p>
                <p class="text-sm text-amber-700">Special Partners</p>
            </div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-slate-900">All Members</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="text-left py-4 px-6 text-sm font-bold text-slate-700">Member</th>
                        <th class="text-center py-4 px-6 text-sm font-bold text-slate-700">Type</th>
                        <th class="text-right py-4 px-6 text-sm font-bold text-slate-700">Capital</th>
                        <th class="text-right py-4 px-6 text-sm font-bold text-slate-700">Share %</th>
                        <th class="text-right py-4 px-6 text-sm font-bold text-slate-700">Profit Received</th>
                        <th class="text-center py-4 px-6 text-sm font-bold text-slate-700">Slots</th>
                        <th class="text-right py-4 px-6 text-sm font-bold text-slate-700">Pending</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @php
                        $totalCapital = $members->sum('capital_committed');
                    @endphp
                    @foreach($members as $member)
                    @php
                        $sharePercent = $totalCapital > 0 ? ($member->capital_committed / $totalCapital) * 100 : 0;
                        $pendingAmount = $member->profitDistributions()->where('status', 'pending')->sum('amount');
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center">
                                    <span class="font-bold text-slate-600">{{ strtoupper(substr($member->user->first_name ?? 'U', 0, 1)) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">{{ $member->user->full_name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-400">{{ $member->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($member->is_special_member)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                    <i data-lucide="star" class="w-3 h-3 mr-1"></i>
                                    Special
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                                    Regular
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-bold text-slate-900">{{ $member->formatted_capital }}</span>
                            @if($member->excess_contribution > 0)
                                <p class="text-xs text-amber-600">+R{{ number_format($member->excess_contribution / 100, 2) }} excess</p>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-medium text-purple-600">{{ number_format($sharePercent, 2) }}%</span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-bold text-emerald-600">{{ $member->formatted_profit_received }}</span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <span class="font-medium text-slate-900">{{ $member->slots_occupied }}</span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            @if($pendingAmount > 0)
                                <span class="font-medium text-amber-600">R{{ number_format($pendingAmount / 100, 2) }}</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h4 class="font-bold text-slate-900 mb-4">Understanding Member Types</h4>
        <div class="grid grid-cols-2 gap-6">
            <div class="flex items-start space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                    Regular
                </span>
                <p class="text-sm text-slate-600">Members who contributed within the expected per-member target amount.</p>
            </div>
            <div class="flex items-start space-x-3">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                    <i data-lucide="star" class="w-3 h-3 mr-1"></i>
                    Special
                </span>
                <p class="text-sm text-slate-600">Members who contributed more than the expected amount. They occupy multiple "slots" and receive proportionally more profit.</p>
            </div>
        </div>
        <div class="mt-4 p-4 bg-slate-50 rounded-xl">
            <p class="text-sm text-slate-600">
                <strong>Expected per-member contribution:</strong> R{{ number_format($cohort->getExpectedPerMemberContribution() / 100, 2) }}<br>
                <strong>Slots explanation:</strong> If a member contributes 2x the expected amount, they occupy 2 slots but receive 2x the profit share.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
