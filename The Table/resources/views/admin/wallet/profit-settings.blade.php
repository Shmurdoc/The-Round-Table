@extends('layouts.admin')

@section('title', 'Profit Settings - Admin')
@section('page-title', 'Profit Settings')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Back Button -->
    <a href="{{ route('admin.wallet.index') }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to Wallet Management
    </a>

    <!-- Current Settings Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-8 py-6">
            <h2 class="text-white font-bold text-2xl">Profit Settings</h2>
            <p class="text-emerald-100 mt-1">Configure daily profit rates for partner returns</p>
        </div>

        <form action="{{ route('admin.wallet.update-profit-settings') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <!-- Current Rate Display -->
            <div class="bg-emerald-50 rounded-2xl p-6 text-center">
                <p class="text-emerald-600 text-sm font-medium mb-2">Current Daily Profit Rate</p>
                <p class="text-5xl font-bold text-emerald-700">{{ $settings->daily_profit_rate ?? 0 }}%</p>
                @if($settings)
                    <p class="text-emerald-500 text-sm mt-2">Last updated: {{ $settings->updated_at->format('d M Y, H:i') }}</p>
                @endif
            </div>

            <!-- Daily Profit Rate -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Daily Profit Rate (%)</label>
                <div class="relative">
                    <input type="number" 
                           name="daily_profit_rate" 
                           id="daily_profit_rate"
                           min="0" 
                           max="10"
                           step="0.01"
                           value="{{ old('daily_profit_rate', $settings->daily_profit_rate ?? 0.5) }}"
                           class="w-full px-4 py-4 text-2xl font-bold border-2 border-slate-200 rounded-2xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 transition"
                           required>
                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl font-bold">%</span>
                </div>
                @error('daily_profit_rate')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
                <p class="text-slate-400 text-sm mt-2">This rate is applied daily to all locked cohort funds</p>
            </div>

            <!-- Quick Rate Buttons -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Quick Set Rate</label>
                <div class="grid grid-cols-5 gap-3">
                    @foreach([0.25, 0.5, 0.75, 1.0, 1.5] as $rate)
                    <button type="button" 
                            onclick="document.getElementById('daily_profit_rate').value = {{ $rate }}"
                            class="py-3 {{ $settings && $settings->daily_profit_rate == $rate ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }} font-bold rounded-xl transition">
                        {{ $rate }}%
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Auto Apply Toggle -->
            <div class="bg-slate-50 rounded-2xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-slate-900">Auto-Apply Daily Profits</h4>
                        <p class="text-sm text-slate-500 mt-1">Automatically apply profits at midnight each day</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="auto_apply" 
                               value="1" 
                               class="sr-only peer"
                               {{ old('auto_apply', $settings->auto_apply ?? false) ? 'checked' : '' }}>
                        <div class="w-14 h-8 bg-slate-300 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-100 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:start-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                    </label>
                </div>
            </div>

            <!-- Calculation Preview -->
            <div class="bg-purple-50 rounded-2xl p-6">
                <h4 class="font-bold text-purple-800 mb-4">Profit Calculation Preview</h4>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-purple-600">Example Principal:</span>
                        <span class="font-bold text-purple-900">R10,000.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-purple-600">Daily Rate:</span>
                        <span class="font-bold text-purple-900" id="preview-rate">{{ $settings->daily_profit_rate ?? 0.5 }}%</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-purple-600">Daily Profit:</span>
                        <span class="font-bold text-emerald-600" id="preview-daily">R{{ number_format(10000 * (($settings->daily_profit_rate ?? 0.5) / 100), 2) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-purple-200 pt-3 mt-3">
                        <span class="text-purple-600">Monthly Estimate (30 days):</span>
                        <span class="font-bold text-emerald-600" id="preview-monthly">R{{ number_format(10000 * (($settings->daily_profit_rate ?? 0.5) / 100) * 30, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
                <h4 class="font-bold text-amber-800 mb-3 flex items-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 mr-2"></i>
                    Important Notes
                </h4>
                <ul class="space-y-2 text-sm text-amber-700">
                    <li class="flex items-start">
                        <i data-lucide="info" class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5"></i>
                        <span>Profits are only applied to <strong>locked</strong> cohort funds.</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="info" class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5"></i>
                        <span>The rate change takes effect immediately for the next profit calculation.</span>
                    </li>
                    <li class="flex items-start">
                        <i data-lucide="info" class="w-4 h-4 mr-2 flex-shrink-0 mt-0.5"></i>
                        <span>If auto-apply is disabled, you must manually apply profits from the dashboard.</span>
                    </li>
                </ul>
            </div>

            <!-- Submit -->
            <div class="flex space-x-4">
                <button type="submit" 
                        class="flex-1 py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold text-lg rounded-2xl shadow-lg shadow-emerald-200 hover:shadow-xl hover:shadow-emerald-300 transition-all transform hover:scale-[1.02]">
                    Save Profit Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Manual Apply Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-bold text-slate-900 mb-4">Manual Profit Application</h3>
        <p class="text-slate-500 text-sm mb-4">Apply the current daily profit rate to all locked cohort funds immediately.</p>
        <form action="{{ route('admin.wallet.apply-daily-profits') }}" method="POST">
            @csrf
            <button type="submit" class="w-full py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition">
                <i data-lucide="play" class="w-5 h-5 inline mr-2"></i>
                Apply Daily Profits Now
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    // Update preview when rate changes
    document.getElementById('daily_profit_rate').addEventListener('input', function() {
        const rate = parseFloat(this.value) || 0;
        const principal = 10000;
        const daily = principal * (rate / 100);
        const monthly = daily * 30;

        document.getElementById('preview-rate').textContent = rate + '%';
        document.getElementById('preview-daily').textContent = 'R' + daily.toLocaleString('en-ZA', {minimumFractionDigits: 2});
        document.getElementById('preview-monthly').textContent = 'R' + monthly.toLocaleString('en-ZA', {minimumFractionDigits: 2});
    });
</script>
@endpush
