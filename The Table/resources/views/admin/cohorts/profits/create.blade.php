@extends('layouts.admin')

@section('title', 'Record Daily Profit - ' . $cohort->name)
@section('page-title', 'Record Daily Profit')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <!-- Back Button -->
    <a href="{{ route('admin.cohorts.profits.index', $cohort) }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to Profit Management
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-8 py-6">
            <h2 class="text-white font-bold text-2xl">Record Daily Profit</h2>
            <p class="text-emerald-100 mt-1">{{ $cohort->name }}</p>
        </div>

        <form action="{{ route('admin.cohorts.profits.store', $cohort) }}" method="POST" class="p-8 space-y-6">
            @csrf

            <!-- Current Settings -->
            <div class="bg-slate-50 rounded-2xl p-4">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-slate-500">Admin Share Rate:</span>
                        <span class="font-bold text-slate-900">{{ $cohort->admin_profit_share }}%</span>
                    </div>
                    <div>
                        <span class="text-slate-500">Members Get:</span>
                        <span class="font-bold text-emerald-600">{{ 100 - $cohort->admin_profit_share }}%</span>
                    </div>
                </div>
            </div>

            <!-- Date -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Profit Date</label>
                <input type="date" 
                       name="profit_date" 
                       value="{{ old('profit_date', now()->toDateString()) }}"
                       max="{{ now()->toDateString() }}"
                       class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 transition"
                       required>
                @error('profit_date')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Amount -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Total Profit Made Today (ZAR)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl font-bold">R</span>
                    <input type="number" 
                           name="amount" 
                           id="amount"
                           min="0.01" 
                           step="0.01"
                           placeholder="0.00"
                           value="{{ old('amount') }}"
                           class="w-full pl-10 pr-4 py-4 text-2xl font-bold border-2 border-slate-200 rounded-2xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 transition"
                           required>
                </div>
                @error('amount')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Quick Amount Buttons -->
            <div class="grid grid-cols-5 gap-2">
                @foreach([100, 500, 1000, 5000, 10000] as $quickAmount)
                <button type="button" 
                        onclick="document.getElementById('amount').value = {{ $quickAmount }}"
                        class="py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg transition text-sm">
                    R{{ number_format($quickAmount) }}
                </button>
                @endforeach
            </div>

            <!-- Preview -->
            <div id="preview" class="bg-emerald-50 rounded-2xl p-5 hidden">
                <h4 class="font-bold text-emerald-800 mb-3">Distribution Preview</h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-emerald-600">Total Profit:</span>
                        <span class="font-bold text-emerald-900" id="preview-total">R0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-emerald-600">Admin Share ({{ $cohort->admin_profit_share }}%):</span>
                        <span class="font-bold text-amber-600" id="preview-admin">R0.00</span>
                    </div>
                    <div class="flex justify-between border-t border-emerald-200 pt-2">
                        <span class="text-emerald-600">Members Share ({{ 100 - $cohort->admin_profit_share }}%):</span>
                        <span class="font-bold text-emerald-900" id="preview-members">R0.00</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" 
                          rows="3"
                          placeholder="Any notes about today's profit..."
                          class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100 transition resize-none">{{ old('notes') }}</textarea>
            </div>

            <!-- Submit -->
            <button type="submit" 
                    class="w-full py-4 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-bold text-lg rounded-2xl shadow-lg shadow-emerald-200 hover:shadow-xl hover:shadow-emerald-300 transition-all transform hover:scale-[1.02]">
                Record Profit
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    const amountInput = document.getElementById('amount');
    const adminRate = {{ $cohort->admin_profit_share }};

    amountInput.addEventListener('input', updatePreview);

    function updatePreview() {
        const amount = parseFloat(amountInput.value) || 0;
        const preview = document.getElementById('preview');

        if (amount > 0) {
            const adminShare = amount * (adminRate / 100);
            const membersShare = amount - adminShare;

            document.getElementById('preview-total').textContent = 'R' + amount.toLocaleString('en-ZA', {minimumFractionDigits: 2});
            document.getElementById('preview-admin').textContent = 'R' + adminShare.toLocaleString('en-ZA', {minimumFractionDigits: 2});
            document.getElementById('preview-members').textContent = 'R' + membersShare.toLocaleString('en-ZA', {minimumFractionDigits: 2});
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    }
</script>
@endpush
