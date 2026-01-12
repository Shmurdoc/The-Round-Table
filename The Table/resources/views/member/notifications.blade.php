@extends('layouts.modern')

@section('title', 'Notifications - RoundTable')
@section('page-title', 'Notifications')

@section('content')
<div class="slide-up space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Notifications</h1>
            <p class="text-slate-500 text-sm mt-2">Stay updated on your partnerships and cohort activity</p>
        </div>
        @if($notifications->where('read', false)->count() > 0)
            <form action="{{ route('member.notifications.mark-all-read') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-bold rounded-lg transition-colors flex items-center space-x-2">
                    <i data-lucide="check-check" class="w-4 h-4"></i>
                    <span>Mark All Read</span>
                </button>
            </form>
        @endif
    </div>

    <!-- Notifications List -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @forelse($notifications as $notification)
            <div class="p-5 border-b border-slate-100 {{ !$notification->read ? 'bg-amber-50/30' : '' }} hover:bg-slate-50 transition-colors group">
                <div class="flex items-start space-x-4">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        @php
                            $iconClass = match($notification->notification_type ?? $notification->type ?? 'info') {
                                'vote_created', 'vote' => 'bg-purple-100 text-purple-600',
                                'distribution', 'payout' => 'bg-emerald-100 text-emerald-600',
                                'cohort_update', 'cohort' => 'bg-blue-100 text-blue-600',
                                'kyc_approved', 'kyc' => 'bg-green-100 text-green-600',
                                'kyc_rejected' => 'bg-red-100 text-red-600',
                                'warning' => 'bg-amber-100 text-amber-600',
                                default => 'bg-slate-100 text-slate-600',
                            };
                            $iconName = match($notification->notification_type ?? $notification->type ?? 'info') {
                                'vote_created', 'vote' => 'vote',
                                'distribution', 'payout' => 'banknote',
                                'cohort_update', 'cohort' => 'users',
                                'kyc_approved', 'kyc' => 'shield-check',
                                'kyc_rejected' => 'shield-x',
                                'warning' => 'alert-triangle',
                                default => 'bell',
                            };
                        @endphp
                        <div class="w-10 h-10 rounded-xl {{ $iconClass }} flex items-center justify-center">
                            <i data-lucide="{{ $iconName }}" class="w-5 h-5"></i>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="font-bold text-slate-900 {{ !$notification->read ? '' : 'font-medium' }}">
                                    {{ $notification->title }}
                                </p>
                                <p class="text-sm text-slate-600 mt-1 leading-relaxed">
                                    {{ $notification->message }}
                                </p>
                                <div class="flex items-center space-x-3 mt-2">
                                    <span class="text-xs text-slate-400 font-mono">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                    @if(!$notification->read)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">
                                            NEW
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center space-x-2 ml-4">
                                @if($notification->action_url)
                                    <a href="{{ $notification->action_url }}" class="text-amber-600 hover:text-amber-700 transition-colors">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>
                                @endif
                                @if(!$notification->read)
                                    <form action="{{ route('member.notifications.read', $notification) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-slate-400 hover:text-emerald-600 transition-colors" title="Mark as read">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="bell-off" class="w-10 h-10 text-slate-400"></i>
                </div>
                <h3 class="font-bold text-slate-900 text-xl mb-2">No Notifications</h3>
                <p class="text-slate-500 max-w-md mx-auto">
                    You're all caught up! Check back later for updates on your partnerships and cohort activity.
                </p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($notifications->hasPages())
        <div class="flex justify-center">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
