<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cohort;
use App\Models\Transaction;
use App\Models\ActivityLog as ActivityLogModel;
use App\Models\Setting;
use App\Models\Notification;
use App\Helpers\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PlatformAdminController extends Controller
{
    /**
     * Show platform dashboard
     */
    public function dashboard()
    {
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();
        $totalCohorts = Cohort::count();
        $activeCohorts = Cohort::where('status', 'operational')->count();
        $totalInvested = Cohort::sum('current_capital');
        $platformFees = 0; // Calculate from transactions
        $pendingKyc = User::where('kyc_status', 'pending')->count();
        $kycApprovalRate = User::where('kyc_status', 'approved')->count() / max(User::whereIn('kyc_status', ['approved', 'rejected'])->count(), 1) * 100;
        $investmentRate = 75; // Placeholder
        
        $cohorts = Cohort::withCount('members')->with('admin')->latest()->paginate(10);
        $recentActivity = collect(); // ActivityLog collection
        
        // Chart data
        $chartLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $userGrowth = [5, 10, 15, 25, 30, $totalUsers];
        $cohortGrowth = [1, 2, 3, 5, 8, $totalCohorts];

        $totalCapital = Cohort::sum('current_capital');
        
        return view('platform-admin.dashboard-modern', compact(
            'totalUsers', 'newUsersThisMonth', 'totalCohorts', 'activeCohorts',
            'totalInvested', 'platformFees', 'pendingKyc', 'kycApprovalRate',
            'investmentRate', 'cohorts', 'recentActivity', 'chartLabels',
            'userGrowth', 'cohortGrowth', 'totalCapital'
        ));
    }

    public function manageUsers()
    {
        $query = User::query();
        
        if (request('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        if (request('status')) {
            $query->where('kyc_status', request('status'));
        }
        
        if (request('role')) {
            $query->where('role', request('role'));
        }
        
        $users = $query->withCount('cohorts')->latest()->paginate(20);
        
        $verifiedCount = User::where('kyc_status', 'approved')->count();
        $pendingCount = User::where('kyc_status', 'pending')->count();
        $adminCount = User::whereIn('role', ['admin', 'platform_admin'])->count();
        
        return view('platform-admin.users', compact('users', 'verifiedCount', 'pendingCount', 'adminCount'));
    }

    public function reviewAllKYC()
    {
        $status = request('status', 'pending');
        
        // Handle approved/verified as the same status
        $query = User::whereNotNull('kyc_status');
        
        if ($status === 'approved') {
            $query->whereIn('kyc_status', ['approved', 'verified']);
        } else {
            $query->where('kyc_status', $status);
        }
        
        $submissions = $query->latest()->paginate(10);
        
        $pendingCount = User::where('kyc_status', 'pending')->count();
        $approvedCount = User::whereIn('kyc_status', ['approved', 'verified'])->count();
        $rejectedCount = User::where('kyc_status', 'rejected')->count();
        $totalReviewed = $approvedCount + $rejectedCount;
        $approvalRate = $totalReviewed > 0 ? ($approvedCount / $totalReviewed) * 100 : 0;
        
        return view('platform-admin.kyc', compact('submissions', 'pendingCount', 'approvedCount', 'rejectedCount', 'approvalRate'));
    }

    public function reports()
    {
        return view('platform-admin.reports');
    }

    public function settings()
    {
        $settings = [
            'platform_name' => Setting::get('platform_name', config('app.name', 'RoundTable')),
            'support_email' => Setting::get('support_email', config('mail.from.address', 'support@roundtable.co.za')),
            'currency' => Setting::get('default_currency', 'ZAR'),
            'min_contribution' => Setting::get('min_contribution', 500),
            'max_members' => Setting::get('max_members_per_cohort', 100),
            'platform_fee' => Setting::get('platform_fee_percentage', 2.5),
            'require_kyc' => Setting::get('require_kyc_for_cohorts', true),
            'require_approval' => Setting::get('require_cohort_approval', true),
            'enable_payfast' => Setting::get('enable_payfast', true),
            'enable_manual' => Setting::get('enable_manual_payments', true),
            'email_notifications' => Setting::get('email_notifications_enabled', true),
            'notify_new_user' => Setting::get('notify_admin_new_user', true),
            'notify_kyc' => Setting::get('notify_admin_kyc_pending', true),
        ];
        
        return view('platform-admin.settings', compact('settings'));
    }

    /**
     * Show pending cohorts
     */
    public function pendingCohorts()
    {
        $cohorts = Cohort::with('admin')
            ->where('status', 'pending_approval')
            ->orderBy('submitted_for_approval_at', 'desc')
            ->paginate(20);

        return view('platform.cohorts.pending', compact('cohorts'));
    }

    /**
     * Approve cohort
     */
    public function approveCohort(Request $request, Cohort $cohort)
    {
        if ($cohort->status !== 'pending_approval') {
            return redirect()->back()
                ->with('error', 'This cohort is not pending approval.');
        }

        $cohort->update([
            'status' => 'funding',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        // Notify cohort admin
        Notification::send(
            $cohort->admin_id,
            'cohort_approved',
            'Cohort Approved: ' . $cohort->name,
            'Your cohort has been approved and is now open for funding!',
            $cohort->id,
            route('admin.cohorts.show', $cohort),
            'View Cohort',
            'high'
        );

        ActivityLog::log('cohort_approved', Auth::id(), $cohort->id, "Approved cohort: {$cohort->name}");

        return redirect()->back()
            ->with('success', 'Cohort approved successfully!');
    }

    /**
     * Reject cohort
     */
    public function rejectCohort(Request $request, Cohort $cohort)
    {
        if ($cohort->status !== 'pending_approval') {
            return redirect()->back()
                ->with('error', 'This cohort is not pending approval.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $cohort->update([
            'status' => 'draft',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Notify cohort admin
        Notification::send(
            $cohort->admin_id,
            'cohort_rejected',
            'Cohort Requires Changes: ' . $cohort->name,
            $validated['rejection_reason'],
            $cohort->id,
            route('admin.cohorts.edit', $cohort),
            'Edit Cohort',
            'high'
        );

        ActivityLog::log('cohort_rejected', Auth::id(), $cohort->id, "Rejected cohort: {$cohort->name}");

        return redirect()->back()
            ->with('success', 'Cohort returned to draft with feedback.');
    }

    /**
     * Show pending KYC
     */
    public function pendingKYC()
    {
        $users = User::where('kyc_status', 'pending')
            ->orderBy('kyc_submitted_at', 'desc')
            ->paginate(20);

        return view('platform.kyc.pending', compact('users'));
    }

    /**
     * Approve KYC
     */
    public function approveKYC(Request $request, User $user)
    {
        if ($user->kyc_status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This user is not pending KYC approval.');
        }

        $user->update([
            'kyc_status' => 'verified',
            'kyc_verified_at' => now(),
            'kyc_verified_by' => Auth::id(),
        ]);

        // Notify user
        Notification::send(
            $user->id,
            'kyc_status',
            'KYC Verification Complete',
            'Your identity has been verified! You can now participate in cohorts.',
            null,
            route('member.dashboard'),
            'Explore Cohorts',
            'high'
        );

        ActivityLog::log('kyc_approved', Auth::id(), null, "Approved KYC for user: {$user->first_name} {$user->last_name}");

        return redirect()->back()
            ->with('success', 'KYC approved successfully!');
    }

    /**
     * Reject KYC
     */
    public function rejectKYC(Request $request, User $user)
    {
        if ($user->kyc_status !== 'pending') {
            return redirect()->back()
                ->with('error', 'This user is not pending KYC approval.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $user->update([
            'kyc_status' => 'rejected',
            'kyc_rejection_reason' => $validated['rejection_reason'],
        ]);

        // Notify user
        Notification::send(
            $user->id,
            'kyc_status',
            'KYC Verification Failed',
            $validated['rejection_reason'],
            null,
            route('kyc.form'),
            'Resubmit Documents',
            'high'
        );

        ActivityLog::log('kyc_rejected', Auth::id(), null, "Rejected KYC for user: {$user->first_name} {$user->last_name}");

        return redirect()->back()
            ->with('success', 'KYC rejected with feedback sent to user.');
    }

    /**
     * Show all users
     */
    public function users(Request $request)
    {
        $query = User::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by KYC status
        if ($request->filled('kyc_status')) {
            $query->where('kyc_status', $request->kyc_status);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('platform.users.index', compact('users'));
    }

    /**
     * Show user details
     */
    public function showUser(User $user)
    {
        $user->load(['cohortMemberships.cohort', 'adminedCohorts']);

        $stats = [
            'total_invested' => $user->totalInvestedCapital(),
            'active_cohorts' => $user->activeCohorts()->count(),
            'total_distributions' => $user->cohortMemberships->sum('total_distributions_received'),
        ];

        return view('platform.users.show', compact('user', 'stats'));
    }

    /**
     * Suspend user
     */
    public function suspendUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'suspension_reason' => 'required|string|max:1000',
        ]);

        $user->update([
            'status' => 'suspended',
            'suspension_reason' => $validated['suspension_reason'],
            'suspended_at' => now(),
        ]);

        // Notify user
        Notification::send(
            $user->id,
            'account_suspended',
            'Account Suspended',
            $validated['suspension_reason'],
            null,
            null,
            null,
            'high'
        );

        ActivityLog::log('user_suspended', Auth::id(), null, "Suspended user: {$user->first_name} {$user->last_name}");

        return redirect()->back()
            ->with('success', 'User suspended successfully.');
    }

    /**
     * Activate user
     */
    public function activateUser(User $user)
    {
        $user->update([
            'status' => 'active',
            'suspension_reason' => null,
            'suspended_at' => null,
        ]);

        // Notify user
        Notification::send(
            $user->id,
            'account_activated',
            'Account Activated',
            'Your account has been reactivated. Welcome back!',
            null,
            route('member.dashboard'),
            'Go to Dashboard',
            'high'
        );

        ActivityLog::log('user_activated', Auth::id(), null, "Activated user: {$user->first_name} {$user->last_name}");

        return redirect()->back()
            ->with('success', 'User activated successfully.');
    }

    /**
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'platform_name' => 'nullable|string|max:255',
            'support_email' => 'nullable|email|max:255',
            'currency' => 'nullable|string|in:ZAR,USD,EUR',
            'min_contribution' => 'nullable|numeric|min:0',
            'max_members' => 'nullable|integer|min:1',
            'platform_fee' => 'nullable|numeric|min:0|max:100',
        ]);

        // Map form fields to settings
        $settingsMap = [
            'platform_name' => ['key' => 'platform_name', 'type' => 'string', 'group' => 'general'],
            'support_email' => ['key' => 'support_email', 'type' => 'string', 'group' => 'general'],
            'currency' => ['key' => 'default_currency', 'type' => 'string', 'group' => 'general'],
            'min_contribution' => ['key' => 'min_contribution', 'type' => 'number', 'group' => 'cohort'],
            'max_members' => ['key' => 'max_members_per_cohort', 'type' => 'number', 'group' => 'cohort'],
            'platform_fee' => ['key' => 'platform_fee_percentage', 'type' => 'number', 'group' => 'payment'],
        ];

        foreach ($settingsMap as $field => $config) {
            if (isset($validated[$field])) {
                Setting::set($config['key'], $validated[$field], $config['type'], $config['group']);
            }
        }

        // Handle checkbox settings (not in validated, check request directly)
        $checkboxSettings = [
            'require_kyc' => ['key' => 'require_kyc_for_cohorts', 'type' => 'boolean', 'group' => 'cohort'],
            'require_approval' => ['key' => 'require_cohort_approval', 'type' => 'boolean', 'group' => 'cohort'],
            'enable_payfast' => ['key' => 'enable_payfast', 'type' => 'boolean', 'group' => 'payment'],
            'enable_manual' => ['key' => 'enable_manual_payments', 'type' => 'boolean', 'group' => 'payment'],
            'email_notifications' => ['key' => 'email_notifications_enabled', 'type' => 'boolean', 'group' => 'notification'],
            'notify_new_user' => ['key' => 'notify_admin_new_user', 'type' => 'boolean', 'group' => 'notification'],
            'notify_kyc' => ['key' => 'notify_admin_kyc_pending', 'type' => 'boolean', 'group' => 'notification'],
        ];

        foreach ($checkboxSettings as $field => $config) {
            Setting::set($config['key'], $request->has($field) ? '1' : '0', $config['type'], $config['group']);
        }

        ActivityLog::log('settings_updated', Auth::id(), null, 'Updated platform settings');

        return redirect()->back()
            ->with('success', 'Settings updated successfully!');
    }

    /**
     * Show analytics
     */
    public function analytics()
    {
        // User analytics
        $userAnalytics = [
            'total_users' => User::count(),
            'new_users_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'kyc_verified' => User::where('kyc_status', 'verified')->count(),
            'kyc_pending' => User::where('kyc_status', 'pending')->count(),
        ];

        // Cohort analytics
        $cohortAnalytics = [
            'total_cohorts' => Cohort::count(),
            'operational' => Cohort::where('status', 'operational')->count(),
            'funding' => Cohort::where('status', 'funding')->count(),
            'completed' => Cohort::where('status', 'completed')->count(),
            'total_capital' => Cohort::sum('total_capital_raised'),
        ];

        // Financial analytics
        $financialAnalytics = [
            'total_transactions' => Transaction::count(),
            'total_revenue' => Transaction::where('type', 'revenue_income')->sum('amount'),
            'total_expenses' => Transaction::where('type', 'operating_expense')->sum('amount'),
            'platform_fees' => Transaction::where('type', 'platform_fee')->sum('amount'),
        ];

        return view('platform.analytics.index', compact('userAnalytics', 'cohortAnalytics', 'financialAnalytics'));
    }

    /**
     * Show activity logs
     */
    public function activityLogs(Request $request)
    {
        $query = ActivityLogModel::with(['user', 'cohort']);

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by cohort
        if ($request->filled('cohort_id')) {
            $query->where('cohort_id', $request->cohort_id);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(100);

        return view('platform.activity-logs.index', compact('logs'));
    }
}
