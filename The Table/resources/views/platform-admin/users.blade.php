@extends('layouts.modern')

@section('title', 'Manage Users - RoundTable')
@section('page-title', 'User Management')

@section('content')
<div class="slide-up space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="users-2" class="w-5 h-5 text-blue-600"></i>
                </div>
                User Management
            </h1>
            <p class="text-slate-500 text-sm mt-2">View and manage all registered users</p>
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
                <span class="text-[10px] text-slate-400 uppercase font-bold">Total Users</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($users->total() ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">KYC Verified</span>
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($verifiedCount ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Pending KYC</span>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($pendingCount ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Admins</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="crown" class="w-4 h-4 text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">{{ number_format($adminCount ?? 0) }}</div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or email..." 
                    class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent">
            </div>
            <select name="status" class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">All KYC Status</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Verified</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="role" class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">All Roles</option>
                <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Member</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="platform_admin" {{ request('role') === 'platform_admin' ? 'selected' : '' }}>Platform Admin</option>
            </select>
            <button type="submit" class="px-6 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-6 py-4 text-[10px] uppercase font-bold text-slate-400">User</th>
                        <th class="text-left px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Role</th>
                        <th class="text-left px-6 py-4 text-[10px] uppercase font-bold text-slate-400">KYC Status</th>
                        <th class="text-left px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Cohorts</th>
                        <th class="text-left px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Joined</th>
                        <th class="text-right px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users ?? [] as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-slate-600">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $user->name }}</div>
                                        <div class="text-xs text-slate-500">{{ $user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold uppercase px-2 py-1 rounded
                                    {{ $user->role === 'platform_admin' ? 'bg-purple-100 text-purple-700' : 
                                       ($user->role === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700') }}">
                                    {{ str_replace('_', ' ', $user->role) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold uppercase px-2 py-1 rounded
                                    {{ in_array($user->kyc_status, ['approved', 'verified']) ? 'bg-emerald-100 text-emerald-700' : 
                                       ($user->kyc_status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $user->kyc_status ?? 'Not Started' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-600 font-mono">{{ $user->cohorts_count ?? 0 }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-500">{{ $user->created_at->format('M d, Y') }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button onclick="viewUser({{ $user->id }})" class="p-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition" title="View">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                    @if($user->kyc_status === 'pending')
                                        <a href="{{ route('platform-admin.kyc') }}?user={{ $user->id }}" class="p-2 bg-amber-100 text-amber-600 rounded-lg hover:bg-amber-200 transition" title="Review KYC">
                                            <i data-lucide="file-check" class="w-4 h-4"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <i data-lucide="users" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                                <p class="text-slate-500">No users found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(isset($users) && $users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- User Detail Modal -->
<div id="userModal" class="fixed inset-0 bg-slate-900/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg text-slate-900">User Details</h3>
            <button onclick="closeModal()" class="p-2 hover:bg-slate-100 rounded-lg transition">
                <i data-lucide="x" class="w-5 h-5 text-slate-500"></i>
            </button>
        </div>
        <div id="userModalContent">
            <!-- Content loaded via JS -->
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
    
    function viewUser(userId) {
        // Show modal with user details
        document.getElementById('userModal').classList.remove('hidden');
        document.getElementById('userModal').classList.add('flex');
        document.getElementById('userModalContent').innerHTML = '<p class="text-slate-500 text-center">Loading...</p>';
        
        // In production, fetch user details via API
        // fetch('/api/users/' + userId).then(...)
    }
    
    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
        document.getElementById('userModal').classList.remove('flex');
    }
</script>
@endpush
