@extends('layouts.modern')

@section('title', 'Cohorts - RoundTable')
@section('page-title', 'Cohorts')

@section('content')
<div class="slide-up">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 space-y-4 md:space-y-0">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Active Cohorts</h1>
            <p class="text-slate-500 text-sm mt-2 max-w-lg">
                Join verified asset-backed projects. Capital is locked in escrow until the MVC (Minimum Viable Capital) threshold is met.
            </p>
        </div>
        <div class="flex space-x-2 bg-white p-1 rounded-lg border border-slate-200 shadow-sm">
            <a href="{{ route('cohorts.index') }}" 
               class="px-3 py-1.5 text-xs font-bold {{ !request('status') ? 'bg-slate-900 text-white' : 'text-slate-500 hover:bg-slate-50' }} rounded shadow-sm transition-colors">
                All
            </a>
            <a href="{{ route('cohorts.index', ['status' => 'funding']) }}" 
               class="px-3 py-1.5 text-xs font-bold {{ request('status') === 'funding' ? 'bg-slate-900 text-white' : 'text-slate-500 hover:bg-slate-50' }} rounded transition-colors">
                Open
            </a>
            <a href="{{ route('cohorts.index', ['status' => 'operational']) }}" 
               class="px-3 py-1.5 text-xs font-bold {{ request('status') === 'operational' ? 'bg-slate-900 text-white' : 'text-slate-500 hover:bg-slate-50' }} rounded transition-colors">
                Active
            </a>
        </div>
    </div>

    <!-- Cohorts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 pb-20">
        @forelse($cohorts as $cohort)
            @php
                $percent = $cohort->hard_cap > 0 ? ($cohort->current_capital / $cohort->hard_cap) * 100 : 0;
                $mvcPercent = $cohort->hard_cap > 0 ? ($cohort->minimum_viable_capital / $cohort->hard_cap) * 100 : 0;
                $memberCount = $cohort->member_count ?? $cohort->members()->count();
                $maxMembers = 50; // Default max members
                
                // Risk level determination
                $riskLevel = match($cohort->risk_level ?? 'moderate') {
                    'low' => ['color' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'label' => 'Low Risk'],
                    'high' => ['color' => 'bg-rose-100 text-rose-700 border-rose-200', 'label' => 'High Volatility'],
                    default => ['color' => 'bg-amber-100 text-amber-700 border-amber-200', 'label' => 'Moderate'],
                };
                
                // Cohort type
                $cohortType = ucfirst($cohort->cohort_class ?? 'Standard');
            @endphp
            
            <a href="{{ route('cohorts.show', $cohort) }}" 
               class="group bg-white rounded-2xl border border-slate-200 hover:border-amber-300 shadow-sm hover:shadow-xl hover:shadow-amber-900/5 hover:-translate-y-1 transition-all duration-300 cursor-pointer overflow-hidden flex flex-col h-full relative">
                
                <!-- Featured Image Header -->
                <div class="relative h-48 overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900">
                    @if($cohort->featured_image)
                        <img src="{{ asset('storage/' . $cohort->featured_image) }}" 
                             alt="{{ $cohort->title }}" 
                             class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent"></div>
                    @else
                        <img src="{{ asset('assets/img/showcase/' . ['inv5.jpg', 'inv7.jpg', 'mb1.jpg', 'mb2.jpg', 'mb3.jpg'][($cohort->id ?? 0) % 5]) }}" 
                             alt="{{ $cohort->title }}" 
                             class="w-full h-full object-cover opacity-40 transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/90 via-slate-900/40 to-transparent"></div>
                    @endif
                    
                    <!-- Overlay Badges -->
                    <div class="absolute top-4 left-4 right-4 flex justify-between items-start z-10">
                        <span class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider rounded-lg border backdrop-blur-sm {{ $riskLevel['color'] }} bg-white/90">
                            {{ $riskLevel['label'] }}
                        </span>
                        <div class="flex items-center space-x-2 px-3 py-1.5 bg-slate-900/80 backdrop-blur-sm rounded-lg border border-white/10">
                            <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-400"></i>
                            <span class="text-xs font-mono text-white">{{ $cohort->duration_months ?? 6 }}M</span>
                        </div>
                    </div>
                    
                    <!-- Asset Type Badge -->
                    <div class="absolute bottom-4 left-4 z-10">
                        <span class="px-3 py-1 text-xs font-bold text-white bg-amber-500/90 backdrop-blur-sm rounded-lg">
                            {{ ucfirst(str_replace('_', ' ', $cohort->asset_type ?? 'real_estate')) }}
                        </span>
                    </div>
                </div>

                <div class="p-6 flex flex-col flex-1">
                    <div class="mb-1">
                        <span class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">{{ $cohortType }}</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 leading-tight mb-3 group-hover:text-amber-600 transition-colors">
                        {{ $cohort->title }}
                    </h3>
                    <p class="text-sm text-slate-500 line-clamp-3 mb-6 flex-1 leading-relaxed">
                        {{ Str::limit($cohort->description, 150) }}
                    </p>
                    
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Target</div>
                            <div class="text-sm font-mono font-bold text-slate-900">R{{ number_format($cohort->ideal_target / 100, 0) }}k</div>
                        </div>
                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Est. Yield</div>
                            <div class="text-sm font-mono font-bold text-emerald-600">{{ $cohort->projected_annual_return ?? '12-18' }}%</div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs items-end">
                            <div>
                                <span class="text-slate-900 font-bold">{{ number_format($percent, 0) }}%</span>
                                <span class="text-slate-400">funded</span>
                            </div>
                            <span class="text-slate-400 text-[10px]">{{ $memberCount }}/{{ $maxMembers }} seats</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden relative">
                            <!-- MVC Marker -->
                            <div class="absolute top-0 bottom-0 w-0.5 bg-slate-300 z-10" style="left: {{ $mvcPercent }}%"></div>
                            <div class="bg-slate-900 h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ min($percent, 100) }}%"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Hover Action -->
                <div class="bg-slate-900 p-4 translate-y-full group-hover:translate-y-0 transition-transform duration-300 absolute bottom-0 left-0 right-0 flex justify-between items-center text-white">
                    <span class="text-sm font-bold">View Projections</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </div>
            </a>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                <div class="relative mb-6">
                    <div class="absolute inset-0 bg-amber-500/20 blur-2xl rounded-full"></div>
                    <div class="relative h-24 w-24 bg-white rounded-2xl border border-slate-100 shadow-xl flex items-center justify-center text-slate-800">
                        <i data-lucide="users" class="w-10 h-10"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-2">No Active Cohorts</h3>
                <p class="text-slate-500 max-w-md leading-relaxed">
                    There are no cohorts available at the moment. Check back soon for new investment opportunities.
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($cohorts->hasPages())
        <div class="flex justify-center pb-10">
            {{ $cohorts->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Re-initialize icons after page load
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
@endpush
