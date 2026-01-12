@extends('layouts.app')

@section('title', $vote->title . ' - ' . $cohort->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-24">
    <!-- Back Button -->
    <a href="{{ route('member.cohorts.show', $cohort) }}" class="inline-flex items-center text-slate-500 hover:text-slate-700 transition">
        <i data-lucide="arrow-left" class="w-5 h-5 mr-2"></i>
        Back to {{ $cohort->name }}
    </a>

    <!-- Vote Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-500 px-6 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        @php
                            $statusColors = [
                                'active' => 'bg-emerald-400',
                                'closed' => 'bg-slate-400',
                                'passed' => 'bg-green-400',
                                'rejected' => 'bg-red-400',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold text-white {{ $statusColors[$vote->status] ?? 'bg-slate-400' }}">
                            {{ ucfirst($vote->status) }}
                        </span>
                        <span class="text-indigo-200 text-sm">
                            {{ $vote->vote_type === 'supermajority' ? 'Supermajority (66.67%)' : ($vote->vote_type === 'unanimous' ? 'Unanimous (100%)' : 'Standard (50%)') }}
                        </span>
                    </div>
                    <h1 class="text-2xl font-bold text-white">{{ $vote->title }}</h1>
                </div>
                <div class="text-right text-white">
                    @if($vote->status === 'active')
                        <p class="text-sm text-indigo-200">Deadline</p>
                        <p class="font-bold">{{ $vote->deadline->format('M d, Y H:i') }}</p>
                        @if($vote->deadline->isFuture())
                            <p class="text-sm text-indigo-200">{{ $vote->deadline->diffForHumans() }}</p>
                        @else
                            <p class="text-sm text-red-300 font-bold">Deadline passed</p>
                        @endif
                    @else
                        <p class="text-sm text-indigo-200">Closed</p>
                        <p class="font-bold">{{ $vote->closed_at?->format('M d, Y') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6">
            <p class="text-slate-600 leading-relaxed">{{ $vote->description }}</p>
        </div>
    </div>

    <!-- Statistics -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 text-center">
            <p class="text-3xl font-bold text-indigo-600">{{ number_format($stats['participation_rate'], 1) }}%</p>
            <p class="text-sm text-slate-500">Participation</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 text-center">
            <p class="text-3xl font-bold text-slate-800">{{ $stats['total_responses'] }}/{{ $stats['total_members'] }}</p>
            <p class="text-sm text-slate-500">Votes Cast</p>
        </div>
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 text-center">
            <p class="text-3xl font-bold {{ $stats['leading_option'] ? 'text-emerald-600' : 'text-slate-400' }}">
                {{ $stats['leading_option'] ?? '—' }}
            </p>
            <p class="text-sm text-slate-500">Leading</p>
        </div>
    </div>

    <!-- Vote Distribution -->
    @if(count($stats['vote_distribution']) > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-bold text-slate-800 mb-4">Current Results</h3>
        <div class="space-y-4">
            @php
                $totalVotes = array_sum($stats['vote_distribution']);
            @endphp
            @foreach($stats['vote_distribution'] as $option => $count)
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="font-medium text-slate-700">{{ $option }}</span>
                    <span class="text-slate-500">{{ $count }} votes ({{ $totalVotes > 0 ? number_format(($count / $totalVotes) * 100, 1) : 0 }}%)</span>
                </div>
                <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div class="h-full {{ $option === $stats['leading_option'] ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' : 'bg-slate-300' }} rounded-full transition-all duration-500"
                         style="width: {{ $totalVotes > 0 ? ($count / $totalVotes) * 100 : 0 }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Cast Vote Form (only if active and user is member) -->
    @if($vote->status === 'active' && $membership && $vote->deadline->isFuture())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">
                @if($userResponse)
                    Change Your Vote
                @else
                    Cast Your Vote
                @endif
            </h3>
            @if($userResponse)
            <p class="text-sm text-slate-500">Your current vote: <span class="font-bold text-indigo-600">{{ $userResponse->vote_option }}</span></p>
            @endif
        </div>

        <form action="{{ route('member.votes.cast', [$cohort, $vote]) }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div class="space-y-3">
                @foreach($vote->voting_options as $option)
                <label class="cursor-pointer block">
                    <input type="radio" 
                           name="vote_option" 
                           value="{{ $option }}" 
                           class="peer sr-only"
                           {{ $userResponse && $userResponse->vote_option === $option ? 'checked' : '' }}
                           required>
                    <div class="p-4 border-2 border-slate-200 rounded-xl peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition flex items-center justify-between">
                        <span class="font-medium text-slate-800">{{ $option }}</span>
                        <div class="w-5 h-5 rounded-full border-2 border-slate-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-500 flex items-center justify-center">
                            <div class="w-2 h-2 bg-white rounded-full hidden peer-checked:block"></div>
                        </div>
                    </div>
                </label>
                @endforeach
            </div>

            <!-- Optional Comment -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Comment (optional)</label>
                <textarea name="comment" 
                          rows="2" 
                          placeholder="Share your thoughts..."
                          class="w-full px-4 py-3 border-2 border-slate-200 rounded-xl focus:border-indigo-400 focus:ring-4 focus:ring-indigo-100 transition text-sm">{{ $userResponse?->comment }}</textarea>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-indigo-500 to-indigo-600 text-white py-4 rounded-xl font-bold hover:from-indigo-600 hover:to-indigo-700 transition shadow-lg shadow-indigo-200">
                @if($userResponse)
                    <i data-lucide="refresh-cw" class="w-5 h-5 inline mr-2"></i>
                    Update Vote
                @else
                    <i data-lucide="check-circle" class="w-5 h-5 inline mr-2"></i>
                    Submit Vote
                @endif
            </button>
        </form>
    </div>
    @elseif($userResponse)
    <div class="bg-emerald-50 rounded-2xl p-6 text-center">
        <div class="w-16 h-16 bg-emerald-100 rounded-full mx-auto mb-4 flex items-center justify-center">
            <i data-lucide="check" class="w-8 h-8 text-emerald-600"></i>
        </div>
        <h3 class="font-bold text-emerald-800 text-lg">You Voted!</h3>
        <p class="text-emerald-600">Your vote: <span class="font-bold">{{ $userResponse->vote_option }}</span></p>
        @if($userResponse->comment)
        <p class="text-sm text-emerald-500 mt-2">"{{ $userResponse->comment }}"</p>
        @endif
    </div>
    @endif

    <!-- Admin Actions -->
    @if($cohort->admin_id === auth()->id() && $vote->status === 'active')
    <div class="bg-amber-50 rounded-2xl p-6">
        <h3 class="font-bold text-amber-800 mb-4">Admin Actions</h3>
        <form action="{{ route('admin.votes.close', [$cohort, $vote]) }}" method="POST" onsubmit="return confirm('Are you sure you want to close this vote? This action cannot be undone.')">
            @csrf
            <button type="submit" class="w-full bg-gradient-to-r from-amber-500 to-amber-600 text-white py-3 rounded-xl font-bold hover:from-amber-600 hover:to-amber-700 transition">
                <i data-lucide="lock" class="w-5 h-5 inline mr-2"></i>
                Close Vote & Determine Result
            </button>
        </form>
    </div>
    @endif

    <!-- Final Result (if closed) -->
    @if($vote->status === 'closed')
    <div class="bg-gradient-to-r {{ $vote->result === 'passed' ? 'from-green-500 to-emerald-500' : ($vote->result === 'rejected' ? 'from-red-500 to-rose-500' : 'from-slate-500 to-slate-600') }} rounded-2xl p-6 text-white text-center">
        <div class="text-4xl mb-2">
            {{ $vote->result === 'passed' ? '✅' : ($vote->result === 'rejected' ? '❌' : '⚠️') }}
        </div>
        <h3 class="text-2xl font-bold mb-2">Vote {{ ucfirst($vote->result) }}</h3>
        @if($vote->winning_option)
        <p class="opacity-90">Winning Option: <span class="font-bold">{{ $vote->winning_option }}</span></p>
        @endif
        <p class="text-sm opacity-75 mt-2">Final count: {{ $vote->final_vote_count }} votes</p>
    </div>
    @endif

    <!-- All Responses (visible to admin) -->
    @if($cohort->admin_id === auth()->id() && $vote->responses->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="bg-slate-50 px-6 py-4 border-b border-slate-100">
            <h3 class="font-bold text-slate-800">All Votes</h3>
        </div>
        <div class="divide-y divide-slate-100">
            @foreach($vote->responses as $response)
            <div class="p-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center">
                        <span class="font-bold text-slate-600">{{ substr($response->user->first_name, 0, 1) }}{{ substr($response->user->last_name, 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-slate-800">{{ $response->user->first_name }} {{ $response->user->last_name }}</p>
                        @if($response->comment)
                        <p class="text-sm text-slate-500">"{{ $response->comment }}"</p>
                        @endif
                    </div>
                </div>
                <span class="px-3 py-1 rounded-full text-sm font-bold {{ $response->vote_option === $stats['leading_option'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700' }}">
                    {{ $response->vote_option }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
