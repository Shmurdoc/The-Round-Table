@extends('layouts.admin')

@section('title', 'Create Vote - ' . $cohort->name)
@section('page-title', 'Create New Vote')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Back Button -->
    <a href="{{ route('admin.cohorts.show', $cohort) }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to {{ $cohort->name }}
    </a>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-8 py-6">
            <h2 class="text-white font-bold text-2xl">Create New Vote</h2>
            <p class="text-indigo-100 mt-1">{{ $cohort->name }} • {{ $cohort->members()->count() }} members will be notified</p>
        </div>

        <form action="{{ route('admin.votes.store', $cohort) }}" method="POST" class="p-8 space-y-6" id="vote-form">
            @csrf

            <!-- Vote Type -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-3">Vote Type</label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="cursor-pointer">
                        <input type="radio" name="vote_type" value="standard" class="peer sr-only" {{ old('vote_type', 'standard') === 'standard' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-slate-200 rounded-xl text-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition">
                            <div class="text-2xl mb-1">⚖️</div>
                            <div class="font-bold text-slate-800">Standard</div>
                            <div class="text-xs text-slate-500">50%+ to pass</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="vote_type" value="supermajority" class="peer sr-only" {{ old('vote_type') === 'supermajority' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-slate-200 rounded-xl text-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition">
                            <div class="text-2xl mb-1">✌️</div>
                            <div class="font-bold text-slate-800">Supermajority</div>
                            <div class="text-xs text-slate-500">66.67%+ to pass</div>
                        </div>
                    </label>
                    <label class="cursor-pointer">
                        <input type="radio" name="vote_type" value="unanimous" class="peer sr-only" {{ old('vote_type') === 'unanimous' ? 'checked' : '' }}>
                        <div class="p-4 border-2 border-slate-200 rounded-xl text-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition">
                            <div class="text-2xl mb-1">✅</div>
                            <div class="font-bold text-slate-800">Unanimous</div>
                            <div class="text-xs text-slate-500">100% to pass</div>
                        </div>
                    </label>
                </div>
                @error('vote_type')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Title -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Vote Title</label>
                <input type="text" 
                       name="title" 
                       value="{{ old('title') }}"
                       placeholder="e.g., Approve new investment opportunity"
                       class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition"
                       required>
                @error('title')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Description</label>
                <textarea name="description" 
                          rows="4"
                          placeholder="Provide details about what members are voting on..."
                          class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition"
                          required>{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Voting Options -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Voting Options</label>
                <div id="options-container" class="space-y-2">
                    @if(old('voting_options'))
                        @foreach(old('voting_options') as $index => $option)
                        <div class="flex items-center gap-2 option-row">
                            <input type="text" 
                                   name="voting_options[]" 
                                   value="{{ $option }}"
                                   placeholder="Option {{ $index + 1 }}"
                                   class="flex-1 px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition"
                                   required>
                            @if($index > 1)
                            <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-700 p-2">
                                <i data-lucide="x-circle" class="w-5 h-5"></i>
                            </button>
                            @endif
                        </div>
                        @endforeach
                    @else
                        <div class="flex items-center gap-2 option-row">
                            <input type="text" 
                                   name="voting_options[]" 
                                   placeholder="e.g., Yes, Approve"
                                   class="flex-1 px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition"
                                   required>
                        </div>
                        <div class="flex items-center gap-2 option-row">
                            <input type="text" 
                                   name="voting_options[]" 
                                   placeholder="e.g., No, Reject"
                                   class="flex-1 px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition"
                                   required>
                        </div>
                    @endif
                </div>
                <button type="button" 
                        onclick="addOption()" 
                        class="mt-3 inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                    <i data-lucide="plus-circle" class="w-4 h-4 mr-1"></i>
                    Add Another Option
                </button>
                @error('voting_options')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deadline -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Voting Deadline</label>
                <input type="datetime-local" 
                       name="deadline" 
                       value="{{ old('deadline') }}"
                       min="{{ now()->addHour()->format('Y-m-d\TH:i') }}"
                       class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition"
                       required>
                @error('deadline')
                    <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                @enderror
            </div>

            <!-- Minimum Participation -->
            <div class="bg-slate-50 rounded-2xl p-5">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-slate-800">Require Minimum Participation</h4>
                        <p class="text-sm text-slate-500">Vote only passes if enough members participate</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                               name="requires_minimum_participation" 
                               value="1" 
                               id="min-participation-toggle"
                               {{ old('requires_minimum_participation') ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-100 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-500"></div>
                    </label>
                </div>
                <div id="min-participation-input" class="{{ old('requires_minimum_participation') ? '' : 'hidden' }}">
                    <div class="flex items-center gap-3">
                        <input type="number" 
                               name="minimum_participation_percent" 
                               min="1" 
                               max="100" 
                               value="{{ old('minimum_participation_percent', 50) }}"
                               class="w-24 px-4 py-2 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition">
                        <span class="text-slate-600">% of members must vote</span>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white py-4 rounded-xl font-bold hover:from-indigo-600 hover:to-indigo-700 transition shadow-lg shadow-indigo-200">
                    <i data-lucide="vote" class="w-5 h-5 inline mr-2"></i>
                    Create Vote & Notify Members
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Toggle minimum participation input
    document.getElementById('min-participation-toggle').addEventListener('change', function() {
        document.getElementById('min-participation-input').classList.toggle('hidden', !this.checked);
    });

    // Add option
    function addOption() {
        const container = document.getElementById('options-container');
        const optionCount = container.querySelectorAll('.option-row').length + 1;
        
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2 option-row';
        row.innerHTML = `
            <input type="text" 
                   name="voting_options[]" 
                   placeholder="Option ${optionCount}"
                   class="flex-1 px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition"
                   required>
            <button type="button" onclick="removeOption(this)" class="text-red-500 hover:text-red-700 p-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </button>
        `;
        container.appendChild(row);
    }

    // Remove option
    function removeOption(button) {
        const rows = document.querySelectorAll('.option-row');
        if (rows.length > 2) {
            button.closest('.option-row').remove();
        }
    }
</script>
@endpush
@endsection
