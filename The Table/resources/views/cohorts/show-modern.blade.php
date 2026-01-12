@extends('layouts.modern')

@section('title', $cohort->title . ' - RoundTable')
@section('page-title', 'Cohort Details')

@section('content')
<div class="slide-up space-y-8">
    <!-- Back Button & Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('cohorts.index') }}" class="flex items-center space-x-2 text-slate-500 hover:text-slate-700 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span class="text-sm font-medium">Back to Cohorts</span>
        </a>
        <div class="flex items-center space-x-2">
            <button class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="Download Prospectus">
                <i data-lucide="file-text" class="w-5 h-5"></i>
            </button>
            <button class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors" title="Share">
                <i data-lucide="share-2" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    <!-- Hero Section -->
    <div class="space-y-6">
        <!-- Featured Image Hero -->
        @if($cohort->featured_image)
        <div class="relative h-[400px] rounded-3xl overflow-hidden shadow-2xl group">
            <img src="{{ asset('storage/' . $cohort->featured_image) }}" 
                 alt="{{ $cohort->title }}"
                 class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
            
            <!-- Gradient Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
            
            <!-- Info Overlay -->
            <div class="absolute bottom-0 left-0 right-0 p-8 text-white z-10">
                <div class="flex flex-wrap gap-3 mb-4">
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold rounded-md uppercase tracking-wider flex items-center">
                        <i data-lucide="zap" class="w-3 h-3 mr-1.5"></i>
                        {{ ucfirst($cohort->cohort_class ?? 'Standard') }}
                    </span>
                    <span class="px-3 py-1 bg-white/20 backdrop-blur-md border border-white/30 text-white text-xs font-bold rounded-md uppercase tracking-wider">
                        {{ $cohort->duration_months ?? 6 }} Months
                    </span>
                    @php
                        $riskColor = match($cohort->risk_level ?? 'moderate') {
                            'low' => 'bg-emerald-500/30 border-emerald-400/50 text-emerald-100',
                            'high' => 'bg-rose-500/30 border-rose-400/50 text-rose-100',
                            default => 'bg-amber-500/30 border-amber-400/50 text-amber-100',
                        };
                    @endphp
                    <span class="px-3 py-1 {{ $riskColor }} backdrop-blur-md text-xs font-bold rounded-md uppercase tracking-wider">
                        {{ ucfirst($cohort->risk_level ?? 'Moderate') }} Risk
                    </span>
                </div>
                
                <h1 class="text-3xl md:text-4xl font-extrabold mb-3 drop-shadow-lg">
                    {{ $cohort->title }}
                </h1>
                
                <p class="text-white/90 text-lg leading-relaxed max-w-3xl drop-shadow">
                    {{ Str::limit($cohort->description, 200) }}
                </p>
            </div>
        </div>
        @endif

        <!-- Original Badges (hidden if featured image exists) -->
        @if(!$cohort->featured_image)
        <div class="flex flex-wrap gap-3">
            <span class="px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 text-xs font-bold rounded-md uppercase tracking-wider flex items-center">
                <i data-lucide="zap" class="w-3 h-3 mr-1.5"></i>
                {{ ucfirst($cohort->cohort_class ?? 'Standard') }}
            </span>
            <span class="px-3 py-1 bg-slate-50 text-slate-600 border border-slate-200 text-xs font-bold rounded-md uppercase tracking-wider">
                {{ $cohort->duration_months ?? 6 }} Months
            </span>
            @php
                $riskColor = match($cohort->risk_level ?? 'moderate') {
                    'low' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                    'high' => 'bg-rose-50 text-rose-700 border-rose-200',
                    default => 'bg-amber-50 text-amber-700 border-amber-200',
                };
            @endphp
            <span class="px-3 py-1 {{ $riskColor }} text-xs font-bold rounded-md uppercase tracking-wider">
                {{ ucfirst($cohort->risk_level ?? 'Moderate') }} Risk
            </span>
        </div>
        
        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight">
            {{ $cohort->title }}
        </h1>
        
        <p class="text-slate-600 text-lg leading-relaxed max-w-3xl">
            {{ $cohort->description }}
        </p>
        @endif
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 py-6 border-y border-slate-100">
            <div class="flex items-center space-x-3">
                <div class="bg-slate-100 p-2.5 rounded-lg">
                    <i data-lucide="monitor" class="w-5 h-5 text-slate-500"></i>
                </div>
                <div>
                    <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-0.5">Asset Class</div>
                    <div class="text-slate-900 font-bold text-sm leading-tight">{{ $cohort->asset_type ?? 'Real Estate' }}</div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="bg-slate-100 p-2.5 rounded-lg">
                    <i data-lucide="ticket" class="w-5 h-5 text-slate-500"></i>
                </div>
                <div>
                    <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-0.5">Min Entry</div>
                    <div class="text-slate-900 font-bold text-sm leading-tight">R{{ number_format($cohort->min_contribution / 100, 0) }}</div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="bg-slate-100 p-2.5 rounded-lg">
                    <i data-lucide="trending-up" class="w-5 h-5 text-slate-500"></i>
                </div>
                <div>
                    <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-0.5">Projected Yield</div>
                    <div class="text-emerald-600 font-bold text-sm leading-tight">{{ $cohort->projected_annual_return ?? '12-18' }}%</div>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <div class="bg-slate-100 p-2.5 rounded-lg">
                    <i data-lucide="users" class="w-5 h-5 text-slate-500"></i>
                </div>
                <div>
                    <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-0.5">Members</div>
                    <div class="text-slate-900 font-bold text-sm leading-tight">{{ $cohort->member_count ?? 0 }}/50</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Funding State -->
    @php
        $percent = $cohort->hard_cap > 0 ? ($cohort->current_capital / $cohort->hard_cap) * 100 : 0;
        $mvcPercent = $cohort->hard_cap > 0 ? ($cohort->minimum_viable_capital / $cohort->hard_cap) * 100 : 0;
    @endphp
    <div class="bg-slate-900 rounded-3xl p-8 text-white relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-48 h-48 bg-amber-500/20 rounded-full blur-3xl -mr-16 -mt-16"></div>
        <div class="absolute bottom-0 left-0 w-32 h-32 bg-emerald-500/10 rounded-full blur-2xl -ml-10 -mb-10"></div>
        
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
            <div>
                <div class="text-4xl font-mono font-bold tracking-tighter">R{{ number_format($cohort->current_capital / 100, 0) }}</div>
                <div class="text-slate-400 text-xs font-mono mt-1">RAISED OF R{{ number_format($cohort->hard_cap / 100, 0) }} GOAL</div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-amber-400">{{ $cohort->member_count ?? 0 }}/50</div>
                <div class="text-slate-400 text-xs font-bold uppercase">Seats Filled</div>
            </div>
        </div>

        <div class="w-full bg-slate-800 rounded-full h-3 mb-3 relative overflow-hidden">
            <div class="bg-gradient-to-r from-amber-500 to-amber-400 h-full rounded-full shadow-[0_0_15px_rgba(245,158,11,0.5)] transition-all duration-1000" style="width: {{ min($percent, 100) }}%"></div>
            <!-- MVC Marker -->
            <div class="absolute top-0 bottom-0 w-0.5 bg-slate-600" style="left: {{ $mvcPercent }}%"></div>
        </div>
        
        <div class="flex justify-between text-[10px] text-slate-500 font-mono font-bold">
            <span>0%</span>
            <span class="text-slate-300">MVC THRESHOLD: {{ number_format($mvcPercent, 0) }}%</span>
            <span>100%</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Financial Details -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50/50 p-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center">
                        <i data-lucide="bar-chart-3" class="w-4 h-4 mr-2 text-amber-600"></i>
                        Financial Structure
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Minimum Viable Capital</div>
                            <div class="font-mono text-lg font-bold text-slate-900">R{{ number_format($cohort->minimum_viable_capital / 100, 0) }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Ideal Target</div>
                            <div class="font-mono text-lg font-bold text-slate-900">R{{ number_format($cohort->ideal_target / 100, 0) }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Hard Cap</div>
                            <div class="font-mono text-lg font-bold text-slate-900">R{{ number_format($cohort->hard_cap / 100, 0) }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Setup Fee</div>
                            <div class="font-mono text-lg font-bold text-slate-900">{{ $cohort->setup_fee_percent ?? 2 }}%</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Management Fee</div>
                            <div class="font-mono text-lg font-bold text-slate-900">{{ $cohort->management_fee_percent ?? 1 }}%</div>
                        </div>
                        <div>
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Performance Fee</div>
                            <div class="font-mono text-lg font-bold text-slate-900">{{ $cohort->performance_fee_percent ?? 10 }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gallery Images -->
            @if($cohort->images && is_array($cohort->images) && count($cohort->images) > 0)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50/50 p-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center">
                        <i data-lucide="image" class="w-4 h-4 mr-2 text-amber-600"></i>
                        Gallery <span class="ml-2 text-xs text-slate-400 font-normal">({{ count($cohort->images) }} images)</span>
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($cohort->images as $index => $galleryImage)
                        <div class="group relative aspect-video rounded-xl overflow-hidden bg-slate-100 cursor-pointer hover:shadow-xl transition-all duration-300">
                            <img src="{{ asset('storage/' . $galleryImage) }}" 
                                 alt="{{ $cohort->title }} - Image {{ $index + 1 }}"
                                 class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-500">
                            
                            <!-- Hover Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <div class="absolute bottom-3 left-3 flex items-center space-x-2">
                                    <div class="bg-white/20 backdrop-blur-md px-2 py-1 rounded text-white text-xs font-bold">
                                        {{ $index + 1 }}/{{ count($cohort->images) }}
                                    </div>
                                </div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <div class="bg-white/20 backdrop-blur-md p-3 rounded-full">
                                        <i data-lucide="maximize-2" class="w-5 h-5 text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50/50 p-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center">
                        <i data-lucide="calendar" class="w-4 h-4 mr-2 text-amber-600"></i>
                        Timeline
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                            <div class="flex-1">
                                <div class="text-sm font-bold text-slate-900">Funding Period</div>
                                <div class="text-xs text-slate-500">{{ $cohort->funding_start_date?->format('d M Y') ?? 'TBD' }} - {{ $cohort->funding_end_date?->format('d M Y') ?? 'TBD' }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-3 h-3 rounded-full bg-amber-500"></div>
                            <div class="flex-1">
                                <div class="text-sm font-bold text-slate-900">Expected Deployment</div>
                                <div class="text-xs text-slate-500">{{ $cohort->expected_deployment_date?->format('d M Y') ?? 'TBD' }}</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="w-3 h-3 rounded-full bg-slate-300"></div>
                            <div class="flex-1">
                                <div class="text-sm font-bold text-slate-900">Expected Exit</div>
                                <div class="text-xs text-slate-500">{{ $cohort->expected_exit_date?->format('d M Y') ?? 'TBD' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- IMPACT SIMULATOR -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xl overflow-hidden">
                <div class="bg-slate-50/50 p-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center">
                        <i data-lucide="activity" class="w-4 h-4 mr-2 text-amber-600"></i> Impact Simulator
                    </h3>
                    <span id="sim-status-badge" class="text-[10px] font-bold px-2 py-1 rounded border bg-white">Loading...</span>
                </div>

                <div class="p-6">
                    <p class="text-sm text-slate-500 mb-6">
                        See how the <span class="font-bold text-slate-900">Safety Buffer Algorithm</span> adjusts returns based on total capital deployed.
                    </p>
                    
                    <!-- GRAPH CONTAINER -->
                    <div class="h-40 w-full mb-6 relative">
                        <svg id="yield-graph" class="w-full h-full overflow-visible" preserveAspectRatio="none">
                            <!-- Path injected by JS -->
                        </svg>
                        <!-- Markers -->
                        <div class="absolute bottom-0 left-0 w-full h-[1px] bg-slate-200"></div>
                        <div class="absolute top-0 bottom-0 border-l border-dashed border-red-300" style="left: {{ $mvcPercent }}%">
                            <span class="text-[9px] font-bold text-red-400 absolute -top-4 -left-2">MVC</span>
                        </div>
                        <div class="absolute top-0 bottom-0 border-l border-dashed border-emerald-300" style="left: 100%">
                            <span class="text-[9px] font-bold text-emerald-500 absolute -top-4 -left-3">TARGET</span>
                        </div>
                    </div>

                    <!-- SLIDER -->
                    <div class="relative mb-8 pt-6">
                        <input type="range" 
                            id="capital-slider"
                            min="{{ ($cohort->hard_cap ?? 100000) * 0.5 }}" 
                            max="{{ ($cohort->hard_cap ?? 100000) * 1.1 }}" 
                            step="5000" 
                            value="{{ $cohort->current_capital ?? 0 }}"
                            class="w-full h-2 bg-slate-100 rounded-full appearance-none cursor-pointer focus:outline-none focus:ring-2 focus:ring-amber-500/20 relative z-20">
                    </div>

                    <!-- DATA READOUTS -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Simulated Capital</div>
                            <div id="sim-raise-val" class="font-mono text-lg font-bold text-slate-900">R0</div>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 transition-colors" id="sim-mult-box">
                            <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider mb-1">Profit Multiplier</div>
                            <div id="sim-mult-val" class="font-mono text-lg font-bold">0%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Risk Factors -->
            @if($cohort->risk_factors)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="bg-slate-50/50 p-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider flex items-center">
                        <i data-lucide="alert-triangle" class="w-4 h-4 mr-2 text-amber-600"></i>
                        Risk Factors
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-sm text-slate-600 leading-relaxed">{{ $cohort->risk_factors }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Join Card -->
            @if($cohort->status === 'funding')
                @php
                    $isMember = auth()->check() && $cohort->users()->where('user_id', auth()->id())->exists();
                    $canJoin = auth()->check() && in_array(auth()->user()->kyc_status, ['approved', 'verified']) && !$isMember;
                @endphp
                
                <div class="bg-white rounded-2xl border border-slate-200 shadow-lg overflow-hidden sticky top-24">
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Available:</span>
                            <span class="font-bold text-slate-900">R{{ number_format(($cohort->hard_cap - $cohort->current_capital) / 100, 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-slate-500">Min Contribution:</span>
                            <span class="font-bold text-slate-900">R{{ number_format($cohort->min_contribution / 100, 0) }}</span>
                        </div>
                        
                        @if($isMember)
                            <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 text-center">
                                <i data-lucide="check-circle" class="w-6 h-6 text-emerald-600 mx-auto mb-2"></i>
                                <p class="text-sm font-bold text-emerald-800">You're a Member</p>
                                <p class="text-xs text-emerald-600 mt-1">You've already joined this cohort</p>
                            </div>
                        @elseif($canJoin)
                            <a href="{{ route('cohorts.join', $cohort) }}" 
                               class="block w-full bg-slate-900 text-white font-bold py-4 rounded-xl text-center text-lg hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20 active:scale-[0.99]">
                                Reserve Seat in Cohort
                            </a>
                        @elseif(!auth()->check())
                            <a href="{{ route('login') }}" 
                               class="block w-full bg-slate-900 text-white font-bold py-4 rounded-xl text-center text-lg hover:bg-slate-800 transition-all shadow-xl shadow-slate-900/20">
                                Login to Join
                            </a>
                        @else
                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-center">
                                <i data-lucide="shield-alert" class="w-6 h-6 text-amber-600 mx-auto mb-2"></i>
                                <p class="text-sm font-bold text-amber-800">KYC Required</p>
                                <p class="text-xs text-amber-600 mt-1">Complete verification to join</p>
                                <a href="{{ route('kyc.form') }}" class="inline-block mt-3 text-sm font-bold text-amber-700 hover:text-amber-800">
                                    Complete KYC →
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Legal Disclaimers -->
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 space-y-4">
                <div class="flex items-start">
                    <div class="bg-emerald-100 p-1 rounded-full mr-3 mt-0.5">
                        <i data-lucide="check" class="w-3 h-3 text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">Zero Guarantees</p>
                        <p class="text-xs text-slate-500 mt-1">Returns are tied directly to asset performance. Past performance ≠ future results.</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-slate-200 p-1 rounded-full mr-3 mt-0.5">
                        <i data-lucide="lock" class="w-3 h-3 text-slate-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-slate-900">No Early Exit Protocol</p>
                        <p class="text-xs text-slate-500 mt-1">Capital is locked for {{ $cohort->duration_months ?? 6 }} months. Secondary market trading available after 30 days.</p>
                    </div>
                </div>
            </div>

            <!-- Administrator -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h4 class="text-sm font-bold text-slate-900 mb-4">Cohort Administrator</h4>
                <div class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr($cohort->admin->first_name ?? 'A', 0, 1)) }}{{ strtoupper(substr($cohort->admin->last_name ?? 'D', 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-900">{{ $cohort->admin->first_name ?? 'Admin' }} {{ $cohort->admin->last_name ?? '' }}</div>
                        <div class="text-xs text-slate-500">Verified Administrator</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Partnership Timeline (if production mode active) -->
    @if($cohort->production_mode)
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-purple-50 to-purple-100 border-b border-purple-200 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="timeline" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-purple-900">Partnership Timeline</h3>
                        <p class="text-sm text-purple-700">Daily updates from your operational partner</p>
                    </div>
                </div>
                <span class="text-xs font-bold uppercase px-3 py-1 bg-purple-200 text-purple-800 rounded-full">
                    Production Active
                </span>
            </div>
        </div>

        <div class="divide-y divide-slate-100 max-h-[600px] overflow-y-auto">
            @forelse($cohort->timelines()->latest('event_date')->latest('created_at')->limit(30)->get() as $timeline)
                <div class="p-6 hover:bg-slate-50 transition-colors">
                    <div class="flex items-start gap-4">
                        <!-- Event Icon -->
                        <div class="w-12 h-12 {{ $timeline->event_type_color }} rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="{{ $timeline->event_type_icon }}" class="w-6 h-6 text-white"></i>
                        </div>

                        <!-- Event Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4 mb-2">
                                <div class="flex-1">
                                    <h4 class="font-bold text-slate-900 text-base mb-1">{{ $timeline->title }}</h4>
                                    <div class="flex items-center gap-2 text-xs text-slate-500">
                                        <i data-lucide="calendar" class="w-3 h-3"></i>
                                        <span>{{ $timeline->event_date->format('l, F d, Y') }}</span>
                                        @if($timeline->is_business_day)
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold">Business Day</span>
                                        @endif
                                        <span>•</span>
                                        <span>{{ $timeline->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                
                                <!-- Event Type Badge -->
                                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded flex-shrink-0 {{ $timeline->event_type === 'profit' ? 'bg-emerald-100 text-emerald-700' : ($timeline->event_type === 'alert' ? 'bg-red-100 text-red-700' : ($timeline->event_type === 'milestone' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700')) }}">
                                    {{ str_replace('_', ' ', $timeline->event_type) }}
                                </span>
                            </div>

                            <!-- Description -->
                            <p class="text-sm text-slate-600 leading-relaxed mb-3">{{ $timeline->description }}</p>

                            <!-- Profit Amount (if applicable) -->
                            @if($timeline->profit_amount > 0)
                                <div class="bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-2 inline-flex items-center gap-2 mb-3">
                                    <i data-lucide="trending-up" class="w-4 h-4 text-emerald-600"></i>
                                    <span class="text-sm font-bold text-emerald-700">
                                        Profit Recorded: R{{ number_format($timeline->profit_amount / 100, 2) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Proof Document Link -->
                            @if($timeline->proof_document)
                                <a href="{{ Storage::url($timeline->proof_document) }}" target="_blank" class="inline-flex items-center gap-2 text-xs text-purple-600 hover:text-purple-700 font-bold hover:underline">
                                    <i data-lucide="paperclip" class="w-3 h-3"></i>
                                    View Proof Document
                                </a>
                            @endif

                            <!-- Posted By -->
                            <div class="mt-3 pt-3 border-t border-slate-100 flex items-center gap-2 text-xs text-slate-500">
                                <i data-lucide="user" class="w-3 h-3"></i>
                                <span>Posted by <span class="font-bold text-slate-700">{{ $timeline->user->first_name ?? 'Admin' }} {{ $timeline->user->last_name ?? '' }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="calendar-x" class="w-8 h-8 text-slate-400"></i>
                    </div>
                    <p class="text-sm text-slate-500 font-bold mb-1">No timeline updates yet</p>
                    <p class="text-xs text-slate-400">Updates will appear here once the partnership begins operations</p>
                </div>
            @endforelse
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Impact Simulator Logic
    const simulatorConfig = {
        mvc: {{ $cohort->minimum_viable_capital ?? 0 }},
        target: {{ $cohort->hard_cap ?? 100000 }},
        currentRaised: {{ $cohort->current_capital ?? 0 }}
    };

    function calculateMultiplier(raised, target, mvc) {
        if (raised < mvc) return 0;
        const result = (raised - mvc) / (target - mvc);
        return Math.min(Math.max(result, 0), 1);
    }

    function updateSimulator(val) {
        const raised = Number(val);
        const { mvc, target } = simulatorConfig;
        const multiplier = calculateMultiplier(raised, target, mvc);
        
        const els = {
            badge: document.getElementById('sim-status-badge'),
            raiseVal: document.getElementById('sim-raise-val'),
            multBox: document.getElementById('sim-mult-box'),
            multVal: document.getElementById('sim-mult-val'),
            graph: document.getElementById('yield-graph')
        };

        if (!els.raiseVal) return;

        // 1. Update Text Data
        els.raiseVal.innerText = `R${(raised/100).toLocaleString('en-ZA', {maximumFractionDigits: 0})}`;
        
        if (raised < mvc) {
            els.badge.innerText = 'BELOW MVC • REFUND TRIGGERED';
            els.badge.className = 'text-[10px] font-bold px-2 py-1 rounded border text-red-600 border-red-200 bg-red-50';
            els.multVal.innerText = '0.0%';
            els.multVal.className = 'font-mono text-lg font-bold text-red-500';
            els.multBox.className = 'bg-red-50/50 p-4 rounded-xl border border-red-100';
        } else {
            const isOptimal = raised >= target;
            els.badge.innerText = isOptimal ? 'OPTIMAL EFFICIENCY' : 'PARTIAL YIELD';
            els.badge.className = `text-[10px] font-bold px-2 py-1 rounded border ${isOptimal ? 'text-emerald-600 border-emerald-200 bg-emerald-50' : 'text-amber-600 border-amber-200 bg-amber-50'}`;
            els.multVal.innerText = `${(multiplier * 100).toFixed(1)}%`;
            els.multVal.className = `font-mono text-lg font-bold ${isOptimal ? 'text-emerald-600' : 'text-amber-600'}`;
            els.multBox.className = isOptimal ? 'bg-emerald-50/50 p-4 rounded-xl border border-emerald-100' : 'bg-amber-50/50 p-4 rounded-xl border border-amber-100';
        }

        // 2. Draw SVG Graph (The "J-Curve")
        const width = 100;
        const height = 100;
        const mvcX = (mvc / target) * 100;
        
        let pathD = `M 0,${height} L ${mvcX},${height}`;
        pathD += ` L ${mvcX},${height} C ${mvcX + 10},${height} ${100 - 10},0 100,0`;

        const currentX = (raised / target) * 100;
        let currentY = height;
        if (raised >= mvc) {
            currentY = height - (multiplier * height);
        }

        const displayY = Math.max(0, Math.min(height, currentY));
        const displayX = Math.min(110, currentX);

        els.graph.innerHTML = `
            <defs>
                <linearGradient id="lineGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" style="stop-color:#cbd5e1;stop-opacity:1" />
                    <stop offset="${mvcX}%" style="stop-color:#cbd5e1;stop-opacity:1" />
                    <stop offset="${mvcX}%" style="stop-color:#f59e0b;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#10b981;stop-opacity:1" />
                </linearGradient>
            </defs>
            <path d="${pathD}" stroke="url(#lineGrad)" stroke-width="3" fill="none" stroke-linecap="round" />
            <circle cx="${displayX}%" cy="${displayY}" r="6" fill="#0f172a" stroke="white" stroke-width="2" class="transition-all duration-75" />
            <line x1="${displayX}%" y1="${displayY}" x2="${displayX}%" y2="${height}" stroke="#0f172a" stroke-width="1" stroke-dasharray="2,2" opacity="0.2" />
        `;
    }

    // Initialize slider
    document.addEventListener('DOMContentLoaded', () => {
        const slider = document.getElementById('capital-slider');
        if (slider) {
            slider.addEventListener('input', (e) => updateSimulator(e.target.value));
            updateSimulator(slider.value);
        }
        lucide.createIcons();
    });
</script>
@endpush
