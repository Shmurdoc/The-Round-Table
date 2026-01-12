@extends('layouts.modern')

@section('title', 'KYC Review - RoundTable')
@section('page-title', 'KYC Review')

@section('content')
<div class="slide-up space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="file-check" class="w-5 h-5 text-amber-600"></i>
                </div>
                KYC Review
            </h1>
            <p class="text-slate-500 text-sm mt-2">Review and verify user identity documents</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('platform-admin.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Pending</span>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($pendingCount ?? 0) }}</div>
            <div class="text-xs text-slate-500">Awaiting review</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Approved</span>
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($approvedCount ?? 0) }}</div>
            <div class="text-xs text-slate-500">Verified users</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Rejected</span>
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-4 h-4 text-red-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($rejectedCount ?? 0) }}</div>
            <div class="text-xs text-slate-500">Failed verification</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Approval Rate</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="percent" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($approvalRate ?? 0, 1) }}%</div>
            <div class="text-xs text-slate-500">Success rate</div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="bg-white rounded-2xl border border-slate-200 p-2 shadow-sm">
        <div class="flex gap-2">
            <a href="{{ route('platform-admin.kyc') }}?status=pending" 
               class="flex-1 px-4 py-3 text-center rounded-xl text-sm font-bold transition
                   {{ request('status', 'pending') === 'pending' ? 'bg-amber-100 text-amber-700' : 'text-slate-500 hover:bg-slate-50' }}">
                Pending ({{ $pendingCount ?? 0 }})
            </a>
            <a href="{{ route('platform-admin.kyc') }}?status=approved" 
               class="flex-1 px-4 py-3 text-center rounded-xl text-sm font-bold transition
                   {{ request('status') === 'approved' ? 'bg-emerald-100 text-emerald-700' : 'text-slate-500 hover:bg-slate-50' }}">
                Approved
            </a>
            <a href="{{ route('platform-admin.kyc') }}?status=rejected" 
               class="flex-1 px-4 py-3 text-center rounded-xl text-sm font-bold transition
                   {{ request('status') === 'rejected' ? 'bg-red-100 text-red-700' : 'text-slate-500 hover:bg-slate-50' }}">
                Rejected
            </a>
        </div>
    </div>

    <!-- KYC Submissions -->
    <div class="space-y-4">
        @forelse($submissions ?? [] as $submission)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <!-- User Info -->
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center">
                                <span class="text-lg font-bold text-slate-600">{{ strtoupper(substr($submission->first_name ?? $submission->name ?? 'U', 0, 1)) }}</span>
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">{{ $submission->first_name && $submission->last_name ? $submission->first_name . ' ' . $submission->last_name : ($submission->name ?? 'Unknown User') }}</div>
                                <div class="text-sm text-slate-500">{{ $submission->email ?? '' }}</div>
                                <div class="text-xs text-slate-400 mt-1">{{ $submission->kyc_submitted_at ? 'Submitted ' . $submission->kyc_submitted_at->diffForHumans() : 'Joined ' . $submission->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        
                        <!-- Status Badge -->
                        <span class="text-xs font-bold uppercase px-3 py-1.5 rounded-lg self-start md:self-center
                            {{ $submission->kyc_status === 'approved' || $submission->kyc_status === 'verified' ? 'bg-emerald-100 text-emerald-700' : 
                               ($submission->kyc_status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                            {{ ucfirst($submission->kyc_status) }}
                        </span>
                    </div>
                    
                    <!-- Basic Info -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-slate-50 rounded-xl p-4">
                            <div class="text-[10px] uppercase font-bold text-slate-400 mb-2">Phone</div>
                            <div class="text-sm font-bold text-slate-900">{{ $submission->phone ?? 'Not specified' }}</div>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4">
                            <div class="text-[10px] uppercase font-bold text-slate-400 mb-2">ID Number</div>
                            <div class="text-sm font-mono text-slate-900">{{ $submission->kyc_id_number ?? 'N/A' }}</div>
                        </div>
                        <div class="bg-slate-50 rounded-xl p-4">
                            <div class="text-[10px] uppercase font-bold text-slate-400 mb-2">KYC Submitted</div>
                            <div class="text-sm text-slate-900">{{ $submission->kyc_submitted_at ? $submission->kyc_submitted_at->format('d M Y H:i') : 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <!-- Document Display -->
                    @if($submission->kyc_id_document_front || $submission->kyc_id_document_back || $submission->kyc_proof_of_residence)
                        <div class="mt-6 border-t border-slate-100 pt-6 space-y-6">
                            <div class="flex items-center gap-2 text-sm font-bold text-slate-700">
                                <i data-lucide="file-text" class="w-4 h-4 text-slate-500"></i>
                                Uploaded Documents
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- ID Document Front -->
                                @if($submission->kyc_id_document_front)
                                <div>
                                    <label class="text-xs font-bold text-slate-600 mb-2 block">ID Document (Front)</label>
                                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-200">
                                        <a href="{{ Storage::url($submission->kyc_id_document_front) }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 text-amber-600 hover:text-amber-700 font-medium text-sm mb-3">
                                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                            View Full Size
                                        </a>
                                        <img src="{{ Storage::url($submission->kyc_id_document_front) }}" 
                                             alt="ID Front" 
                                             class="w-full rounded-lg border border-slate-200 object-cover max-h-48 hover:max-h-none transition-all cursor-pointer">
                                    </div>
                                </div>
                                @endif

                                <!-- ID Document Back -->
                                @if($submission->kyc_id_document_back)
                                <div>
                                    <label class="text-xs font-bold text-slate-600 mb-2 block">ID Document (Back)</label>
                                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-200">
                                        <a href="{{ Storage::url($submission->kyc_id_document_back) }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 text-amber-600 hover:text-amber-700 font-medium text-sm mb-3">
                                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                            View Full Size
                                        </a>
                                        <img src="{{ Storage::url($submission->kyc_id_document_back) }}" 
                                             alt="ID Back" 
                                             class="w-full rounded-lg border border-slate-200 object-cover max-h-48 hover:max-h-none transition-all cursor-pointer">
                                    </div>
                                </div>
                                @endif

                                <!-- Proof of Residence -->
                                @if($submission->kyc_proof_of_residence)
                                <div>
                                    <label class="text-xs font-bold text-slate-600 mb-2 block">Proof of Residence</label>
                                    <div class="bg-slate-50 rounded-xl p-3 border border-slate-200">
                                        <a href="{{ Storage::url($submission->kyc_proof_of_residence) }}" target="_blank" 
                                           class="inline-flex items-center gap-1.5 text-amber-600 hover:text-amber-700 font-medium text-sm mb-3">
                                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                            View Full Size
                                        </a>
                                        <img src="{{ Storage::url($submission->kyc_proof_of_residence) }}" 
                                             alt="Proof of Residence" 
                                             class="w-full rounded-lg border border-slate-200 object-cover max-h-48 hover:max-h-none transition-all cursor-pointer">
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="mt-6 border-t border-slate-100 pt-6">
                            <div class="bg-amber-50 text-amber-700 p-4 rounded-xl text-sm flex items-center gap-2">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                <span>No documents have been uploaded yet.</span>
                            </div>
                        </div>
                    @endif
                    
                    @if($submission->kyc_status === 'pending')
                        <!-- Action Buttons -->
                        <div class="mt-6 flex flex-col md:flex-row gap-3 border-t border-slate-100 pt-6">
                            <form action="{{ route('platform-admin.kyc.approve', $submission->id) }}" method="POST" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                                    <i data-lucide="check" class="w-5 h-5"></i>
                                    Approve
                                </button>
                            </form>
                            <button onclick="showRejectForm({{ $submission->id }})" class="flex-1 px-6 py-3 bg-red-100 text-red-700 font-bold rounded-xl hover:bg-red-200 transition flex items-center justify-center gap-2">
                                <i data-lucide="x" class="w-5 h-5"></i>
                                Reject
                            </button>
                        </div>
                    @elseif($submission->kyc_status === 'rejected' && $submission->kyc_rejection_reason)
                        <div class="mt-4 p-4 bg-red-50 rounded-xl">
                            <div class="text-[10px] uppercase font-bold text-red-400 mb-1">Rejection Reason</div>
                            <p class="text-sm text-red-700">{{ $submission->kyc_rejection_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-slate-200 p-12 text-center shadow-sm">
                <i data-lucide="file-check" class="w-12 h-12 text-slate-300 mx-auto mb-4"></i>
                <h3 class="font-bold text-slate-900 mb-2">No KYC submissions</h3>
                <p class="text-sm text-slate-500">
                    @if(request('status') === 'pending')
                        There are no pending KYC submissions to review
                    @else
                        No {{ request('status') }} submissions found
                    @endif
                </p>
            </div>
        @endforelse
    </div>
    
    @if(isset($submissions) && $submissions->hasPages())
        <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
            {{ $submissions->links() }}
        </div>
    @endif
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-slate-900/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg text-slate-900">Reject KYC Submission</h3>
            <button onclick="closeRejectModal()" class="p-2 hover:bg-slate-100 rounded-lg transition">
                <i data-lucide="x" class="w-5 h-5 text-slate-500"></i>
            </button>
        </div>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold text-slate-700 mb-2">Reason for Rejection</label>
                <textarea name="rejection_reason" rows="3" required
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                    placeholder="Please provide a reason for rejecting this submission..."></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition">
                    Reject
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
    
    function showRejectForm(submissionId) {
        const form = document.getElementById('rejectForm');
        form.action = '/platform-admin/kyc/' + submissionId + '/reject';
        document.getElementById('rejectModal').classList.remove('hidden');
        document.getElementById('rejectModal').classList.add('flex');
    }
    
    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('flex');
    }
</script>
@endpush
