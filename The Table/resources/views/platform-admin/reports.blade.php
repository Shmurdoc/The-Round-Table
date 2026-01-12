@extends('layouts.modern')

@section('title', 'Reports - RoundTable')
@section('page-title', 'Platform Reports')

@section('content')
<div class="slide-up space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 text-blue-600"></i>
                </div>
                Platform Reports
            </h1>
            <p class="text-slate-500 text-sm mt-2">Analytics and reporting for platform performance</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('platform-admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Report Types -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="trending-up" class="w-6 h-6 text-emerald-600"></i>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">Investment Performance</h3>
            <p class="text-sm text-slate-600 mb-4">Track overall platform investment returns and performance metrics.</p>
            <button class="text-sm font-bold text-amber-600 hover:text-amber-700">Generate Report →</button>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">User Growth</h3>
            <p class="text-sm text-slate-600 mb-4">User registration trends, retention rates, and demographics.</p>
            <button class="text-sm font-bold text-amber-600 hover:text-amber-700">Generate Report →</button>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="layers" class="w-6 h-6 text-purple-600"></i>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">Cohort Activity</h3>
            <p class="text-sm text-slate-600 mb-4">Active cohorts, funding status, and member participation.</p>
            <button class="text-sm font-bold text-amber-600 hover:text-amber-700">Generate Report →</button>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="banknote" class="w-6 h-6 text-amber-600"></i>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">Distribution History</h3>
            <p class="text-sm text-slate-600 mb-4">Complete history of profit distributions across all cohorts.</p>
            <button class="text-sm font-bold text-amber-600 hover:text-amber-700">Generate Report →</button>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="shield-alert" class="w-6 h-6 text-red-600"></i>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">KYC Compliance</h3>
            <p class="text-sm text-slate-600 mb-4">KYC verification status, rejection rates, and compliance metrics.</p>
            <button class="text-sm font-bold text-amber-600 hover:text-amber-700">Generate Report →</button>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center mb-4">
                <i data-lucide="file-text" class="w-6 h-6 text-slate-600"></i>
            </div>
            <h3 class="font-bold text-slate-900 mb-2">Tax Reports</h3>
            <p class="text-sm text-slate-600 mb-4">Generate tax-compliant reports for distributions and earnings.</p>
            <button class="text-sm font-bold text-amber-600 hover:text-amber-700">Generate Report →</button>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h3 class="font-bold text-slate-900">Recent Reports</h3>
        </div>
        <div class="p-8 text-center">
            <i data-lucide="file-text" class="w-12 h-12 text-slate-300 mx-auto mb-4"></i>
            <h4 class="font-bold text-slate-900 mb-2">No reports generated yet</h4>
            <p class="text-sm text-slate-500">Generate your first report using the options above.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
