@extends('layouts.modern')

@section('title', 'Settings - RoundTable')
@section('page-title', 'Platform Settings')

@section('content')
<div class="slide-up space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="settings" class="w-5 h-5 text-slate-600"></i>
                </div>
                Platform Settings
            </h1>
            <p class="text-slate-500 text-sm mt-2">Configure platform-wide settings and preferences</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('platform-admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back
            </a>
        </div>
    </div>

    <form action="{{ route('platform-admin.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- General Settings -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900">General Settings</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Platform Name</label>
                    <input type="text" name="platform_name" value="{{ $settings['platform_name'] }}" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Support Email</label>
                    <input type="email" name="support_email" value="{{ $settings['support_email'] }}" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Default Currency</label>
                    <select name="currency" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="ZAR" {{ $settings['currency'] === 'ZAR' ? 'selected' : '' }}>South African Rand (ZAR)</option>
                        <option value="USD" {{ $settings['currency'] === 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                        <option value="EUR" {{ $settings['currency'] === 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Cohort Settings -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900">Cohort Settings</h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Minimum Contribution (ZAR)</label>
                    <input type="number" name="min_contribution" value="{{ $settings['min_contribution'] }}" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Maximum Members per Cohort</label>
                    <input type="number" name="max_members" value="{{ $settings['max_members'] }}" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="require_kyc" id="require_kyc" {{ $settings['require_kyc'] ? 'checked' : '' }} 
                        class="w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                    <label for="require_kyc" class="text-sm font-bold text-slate-700">Require KYC verification to join cohorts</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="require_approval" id="require_approval" {{ $settings['require_approval'] ? 'checked' : '' }} 
                        class="w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                    <label for="require_approval" class="text-sm font-bold text-slate-700">Require platform admin approval for new cohorts</label>
                </div>
            </div>
        </div>

        <!-- Payment Settings -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900">Payment Settings</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="enable_crypto" id="enable_crypto" {{ $settings['enable_crypto'] ?? true ? 'checked' : '' }} 
                        class="w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                    <label for="enable_crypto" class="text-sm font-bold text-slate-700">Enable Cryptocurrency payments (USDT)</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="enable_nowpayments" id="enable_nowpayments" {{ $settings['enable_nowpayments'] ?? true ? 'checked' : '' }} 
                        class="w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                    <label for="enable_nowpayments" class="text-sm font-bold text-slate-700">Enable NOWPayments integration</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="enable_wallet_deposits" id="enable_wallet_deposits" {{ $settings['enable_wallet_deposits'] ?? true ? 'checked' : '' }} 
                        class="w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                    <label for="enable_wallet_deposits" class="text-sm font-bold text-slate-700">Enable wallet deposits</label>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Minimum Deposit Amount (USD)</label>
                    <input type="number" name="min_deposit" value="{{ $settings['min_deposit'] ?? 10 }}" step="0.01" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <p class="text-xs text-slate-500 mt-1">Minimum amount for crypto deposits</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Withdrawal Fee (USD)</label>
                    <input type="number" name="withdrawal_fee" value="{{ $settings['withdrawal_fee'] ?? 2 }}" step="0.01" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <p class="text-xs text-slate-500 mt-1">Fixed fee for crypto withdrawals</p>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Platform Fee (%)</label>
                    <input type="number" name="platform_fee" value="{{ $settings['platform_fee'] }}" step="0.1" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <p class="text-xs text-slate-500 mt-1">Percentage fee charged on distributions</p>
                </div>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h3 class="font-bold text-slate-900">Notification Settings</h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="email_notifications" id="email_notifications" {{ $settings['email_notifications'] ? 'checked' : '' }} 
                        class="w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                    <label for="email_notifications" class="text-sm font-bold text-slate-700">Send email notifications</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="notify_new_user" id="notify_new_user" {{ $settings['notify_new_user'] ? 'checked' : '' }} 
                        class="w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                    <label for="notify_new_user" class="text-sm font-bold text-slate-700">Notify admins of new user registrations</label>
                </div>
                <div class="flex items-center gap-3">
                    <input type="checkbox" name="notify_kyc" id="notify_kyc" {{ $settings['notify_kyc'] ? 'checked' : '' }} 
                        class="w-5 h-5 rounded border-slate-300 text-amber-500 focus:ring-amber-500">
                    <label for="notify_kyc" class="text-sm font-bold text-slate-700">Notify admins of pending KYC submissions</label>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-amber-500 text-slate-900 font-bold rounded-xl hover:bg-amber-400 transition shadow-lg shadow-amber-200">
                <i data-lucide="save" class="w-5 h-5"></i>
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
