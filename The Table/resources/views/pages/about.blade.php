@extends('layouts.modern-guest')

@section('title', 'About - RoundTable')

@section('content')
<div class="slide-up space-y-10">
    <!-- Hero Section -->
    <div class="text-center max-w-3xl mx-auto">
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-6">
            Building Wealth Through
            <span class="bg-gradient-to-r from-amber-500 to-amber-600 bg-clip-text text-transparent">Community</span>
        </h1>
        <p class="text-lg text-slate-600">
            RoundTable is a cooperative partnership platform that empowers South Africans to pool resources 
            and partner together in income-generating assets.
        </p>
    </div>

    <!-- Mission & Vision -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="target" class="w-6 h-6 text-amber-600"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-900 mb-3">Our Mission</h2>
            <p class="text-slate-600">
                To democratize wealth creation by enabling everyday South Africans to participate in 
                partnership opportunities previously reserved for the wealthy, through the power of 
                collective partnership and transparent governance.
            </p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="eye" class="w-6 h-6 text-purple-600"></i>
            </div>
            <h2 class="text-xl font-bold text-slate-900 mb-3">Our Vision</h2>
            <p class="text-slate-600">
                A future where every South African has access to partnership opportunities that create 
                generational wealth, supported by a community of like-minded individuals working 
                together toward financial freedom.
            </p>
        </div>
    </div>

    <!-- Values -->
    <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
        <h2 class="text-2xl font-bold text-slate-900 mb-8 text-center">Our Core Values</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="shield-check" class="w-8 h-8 text-emerald-600"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Transparency</h3>
                <p class="text-sm text-slate-600">Every transaction, decision, and report is visible to all members. No hidden fees, no surprises.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="users" class="w-8 h-8 text-blue-600"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Community</h3>
                <p class="text-sm text-slate-600">We believe in the power of collective action. Together, we achieve more than we could alone.</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="trending-up" class="w-8 h-8 text-amber-600"></i>
                </div>
                <h3 class="font-bold text-slate-900 mb-2">Growth</h3>
                <p class="text-sm text-slate-600">We focus on sustainable, long-term wealth creation through careful investment strategies.</p>
            </div>
        </div>
    </div>

    <!-- How It Works Summary -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-8 text-center">
        <h2 class="text-2xl font-bold text-white mb-4">Ready to Start Your Partnership Journey?</h2>
        <p class="text-slate-400 mb-6">Join thousands of South Africans already building wealth through RoundTable partnerships.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-amber-500 text-slate-900 font-bold rounded-xl hover:bg-amber-400 transition">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
                Create Account
            </a>
            <a href="{{ route('cohorts.index') }}" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-slate-700 text-white font-bold rounded-xl hover:bg-slate-600 transition">
                <i data-lucide="layers" class="w-5 h-5"></i>
                Browse Cohorts
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
