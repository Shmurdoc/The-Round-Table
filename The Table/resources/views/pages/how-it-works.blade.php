@extends('layouts.modern-guest')

@section('title', 'How It Works - RoundTable')

@section('content')
<div class="slide-up space-y-10">
    <!-- Hero Section -->
    <div class="text-center max-w-3xl mx-auto">
        <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 tracking-tight mb-6">
            How
            <span class="bg-gradient-to-r from-amber-500 to-amber-600 bg-clip-text text-transparent">RoundTable</span>
            Works
        </h1>
        <p class="text-lg text-slate-600">
            A simple, transparent process to help you start building wealth with your community.
        </p>
    </div>

    <!-- Steps -->
    <div class="space-y-6">
        <!-- Step 1 -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm flex flex-col md:flex-row items-start gap-6">
            <div class="flex-shrink-0 w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center">
                <span class="text-2xl font-bold text-amber-600">1</span>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-slate-900 mb-2">Create Your Account</h2>
                <p class="text-slate-600 mb-4">
                    Sign up for a free RoundTable account. You'll need to complete KYC verification 
                    to ensure the security and legitimacy of our community.
                </p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Provide basic personal information
                    </li>
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Upload ID document for verification
                    </li>
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Add banking details for distributions
                    </li>
                </ul>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm flex flex-col md:flex-row items-start gap-6">
            <div class="flex-shrink-0 w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center">
                <span class="text-2xl font-bold text-blue-600">2</span>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-slate-900 mb-2">Find Your Cohort</h2>
                <p class="text-slate-600 mb-4">
                    Browse available cohorts and find one that matches your partnership goals and budget. 
                    Each cohort has a specific contribution amount and partnership strategy.
                </p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Review cohort goals and partnership thesis
                    </li>
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Check the contribution amount required
                    </li>
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Understand the expected timeline and returns
                    </li>
                </ul>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm flex flex-col md:flex-row items-start gap-6">
            <div class="flex-shrink-0 w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center">
                <span class="text-2xl font-bold text-purple-600">3</span>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-slate-900 mb-2">Make Your Contribution</h2>
                <p class="text-slate-600 mb-4">
                    Join your chosen cohort by making your contribution. We support multiple payment 
                    methods including PayFast and manual EFT transfers.
                </p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Secure payment processing
                    </li>
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Funds held in trust until cohort is operational
                    </li>
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Full refund if cohort doesn't reach target
                    </li>
                </ul>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm flex flex-col md:flex-row items-start gap-6">
            <div class="flex-shrink-0 w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center">
                <span class="text-2xl font-bold text-emerald-600">4</span>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-slate-900 mb-2">Watch Your Partnership Grow</h2>
                <p class="text-slate-600 mb-4">
                    Once the cohort is operational, partnerships are managed according to the strategy. 
                    Track your portfolio, receive updates, and vote on major decisions.
                </p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Real-time portfolio tracking
                    </li>
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Regular performance reports
                    </li>
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Democratic voting on key decisions
                    </li>
                </ul>
            </div>
        </div>

        <!-- Step 5 -->
        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm flex flex-col md:flex-row items-start gap-6">
            <div class="flex-shrink-0 w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center">
                <span class="text-2xl font-bold text-amber-600">5</span>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-slate-900 mb-2">Receive Distributions</h2>
                <p class="text-slate-600 mb-4">
                    As investments generate returns, profits are distributed to members proportionally. 
                    Distributions are sent directly to your linked bank account.
                </p>
                <ul class="space-y-2">
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Automatic distribution processing
                    </li>
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Complete transaction history
                    </li>
                    <li class="flex items-center gap-2 text-sm text-slate-600">
                        <i data-lucide="check" class="w-4 h-4 text-emerald-600"></i>
                        Tax-compliant reporting
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- FAQ -->
    <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
        <h2 class="text-2xl font-bold text-slate-900 mb-6">Frequently Asked Questions</h2>
        <div class="space-y-4">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="font-bold text-slate-900 mb-2">What is the minimum contribution?</h3>
                <p class="text-sm text-slate-600">Each cohort sets its own contribution amount, starting from as low as R500 per partner.</p>
            </div>
            <div class="border-b border-slate-100 pb-4">
                <h3 class="font-bold text-slate-900 mb-2">How are returns calculated?</h3>
                <p class="text-sm text-slate-600">Returns are calculated based on partnership performance and distributed proportionally to each partner's contribution.</p>
            </div>
            <div class="border-b border-slate-100 pb-4">
                <h3 class="font-bold text-slate-900 mb-2">Is my contribution safe?</h3>
                <p class="text-sm text-slate-600">All partnerships carry risk. However, we implement strict KYC verification, transparent governance, and regular audits to minimize risk.</p>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 mb-2">Can I withdraw early?</h3>
                <p class="text-sm text-slate-600">Early withdrawal options depend on the cohort's rules. Some cohorts allow early exit with notice, while others have lock-in periods.</p>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl p-8 text-center">
        <h2 class="text-2xl font-bold text-white mb-4">Ready to Get Started?</h2>
        <p class="text-slate-400 mb-6">Join RoundTable today and start building wealth with your community.</p>
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
