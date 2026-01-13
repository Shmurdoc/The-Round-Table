@extends('layouts.modern')

@section('title', 'Manage Cohorts - RoundTable')
@section('page-title', 'My Cohorts')

@section('content')
<div class="slide-up space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="layers" class="w-5 h-5 text-purple-600"></i>
                </div>
                Manage Cohorts
            </h1>
            <p class="text-slate-500 text-sm mt-2">View and manage your investment cohorts</p>
        </div>
        <a href="{{ route('admin.cohorts.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 text-slate-900 font-bold rounded-xl hover:bg-amber-400 transition shadow-lg shadow-amber-200">
            <i data-lucide="plus" class="w-5 h-5"></i>
            Create Cohort
        </a>
    </div>

    <!-- Cohorts Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($cohorts ?? [] as $cohort)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-lg transition-all group">
                <!-- Featured Image Header -->
                @if($cohort->featured_image)
                <div class="relative h-48 overflow-hidden">
                    <img src="{{ Storage::url($cohort->featured_image) }}" 
                         alt="{{ $cohort->title ?? $cohort->name }}"
                         class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
                    <div class="absolute bottom-3 left-3 right-3">
                        <span class="inline-block text-[10px] font-bold uppercase px-2 py-1 rounded backdrop-blur-md
                            {{ $cohort->status === 'operational' ? 'bg-emerald-500/30 text-emerald-100 border border-emerald-400/50' : 
                               ($cohort->status === 'funding' ? 'bg-amber-500/30 text-amber-100 border border-amber-400/50' : 
                               ($cohort->status === 'pending_approval' ? 'bg-blue-500/30 text-blue-100 border border-blue-400/50' : 'bg-slate-500/30 text-slate-100 border border-slate-400/50')) }}">
                            {{ str_replace('_', ' ', $cohort->status) }}
                        </span>
                    </div>
                </div>
                @endif
                
                <div class="p-6">
                    @if(!$cohort->featured_image)
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-xl flex items-center justify-center">
                            <i data-lucide="layers" class="w-6 h-6 text-purple-600"></i>
                        </div>
                        <span class="text-[10px] font-bold uppercase px-2 py-1 rounded
                            {{ $cohort->status === 'operational' ? 'bg-emerald-100 text-emerald-700' : 
                               ($cohort->status === 'funding' ? 'bg-amber-100 text-amber-700' : 
                               ($cohort->status === 'pending_approval' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700')) }}">
                            {{ str_replace('_', ' ', $cohort->status) }}
                        </span>
                    </div>
                    @endif
                    <h3 class="font-bold text-slate-900 text-lg mb-1 line-clamp-1">{{ $cohort->title ?? $cohort->name }}</h3>
                    <p class="text-sm text-slate-500 mb-4 line-clamp-2">{{ $cohort->description }}</p>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-400">Members</div>
                            <div class="font-bold font-mono text-slate-900">{{ $cohort->members_count ?? 0 }}</div>
                        </div>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-400">Capital</div>
                            <div class="font-bold font-mono text-slate-900">R{{ number_format(($cohort->current_capital ?? 0) / 100, 0) }}</div>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <a href="{{ route('admin.cohorts.show', $cohort) }}" class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-lg text-center hover:bg-slate-200 transition">
                            View
                        </a>
                        <a href="{{ route('admin.cohorts.edit', $cohort) }}" class="flex-1 px-4 py-2 bg-amber-100 text-amber-700 text-sm font-bold rounded-lg text-center hover:bg-amber-200 transition">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                <i data-lucide="layers" class="w-12 h-12 text-slate-300 mx-auto mb-4"></i>
                <h3 class="font-bold text-slate-900 mb-2">No cohorts yet</h3>
                <p class="text-sm text-slate-500 mb-4">Create your first investment cohort to get started.</p>
                <a href="{{ route('admin.cohorts.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 text-slate-900 font-bold rounded-xl hover:bg-amber-400 transition">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    Create Cohort
                </a>
            </div>
        @endforelse
    </div>

    @if(isset($cohorts) && $cohorts->hasPages())
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
            {{ $cohorts->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
