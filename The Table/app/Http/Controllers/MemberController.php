<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\CohortServiceInterface;
use App\DTOs\CohortFilterData;
use App\Models\Cohort;
use App\Models\Transaction;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

final class MemberController extends Controller
{
    public function __construct(
        private readonly CohortServiceInterface $cohortService,
    ) {}

    /**
     * Display member dashboard
     */
    public function dashboard(): View|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Redirect admins to their appropriate dashboards
        if ($user->isPlatformAdmin()) {
            return redirect()->route('platform-admin.dashboard');
        }
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        $portfolio = $this->cohortService->getUserPortfolioSummary($user);

        $recentTransactions = Transaction::query()
            ->where('user_id', $user->id)
            ->with('cohort:id,title')
            ->latest()
            ->limit(10)
            ->get();

        $notifications = Notification::query()
            ->where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        $pendingDistributions = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'distribution')
            ->where('status', 'pending')
            ->sum('amount');

        $completedDistributions = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'distribution')
            ->where('status', 'completed')
            ->sum('amount');

        $totalEarnings = Transaction::query()
            ->where('user_id', $user->id)
            ->where('type', 'distribution')
            ->sum('amount');

        return view('member.dashboard-modern', [
            'userCohorts' => $portfolio['cohorts'],
            'totalInvested' => $portfolio['totalInvested'],
            'totalReturns' => $portfolio['totalReturns'],
            'activeCohorts' => $portfolio['activeCohorts'],
            'returnRate' => $portfolio['returnRate'],
            'recentTransactions' => $recentTransactions,
            'notifications' => $notifications,
            'pendingDistributions' => $pendingDistributions / 100,
            'completedDistributions' => $completedDistributions / 100,
            'totalEarnings' => $totalEarnings / 100,
        ]);
    }

    /**
     * Display member portfolio
     */
    public function portfolio(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $portfolio = $this->cohortService->getUserPortfolioSummary($user);

        $transactions = Transaction::query()
            ->where('user_id', $user->id)
            ->with('cohort:id,title')
            ->latest()
            ->paginate(20);

        // Calculate chart data for portfolio performance
        $chartData = $this->buildPortfolioChartData($user);

        // Get distribution summary
        $distributionStats = [
            'total' => Transaction::where('user_id', $user->id)
                ->where('type', 'distribution')
                ->sum('amount') / 100,
            'completed' => Transaction::where('user_id', $user->id)
                ->where('type', 'distribution')
                ->where('status', 'completed')
                ->sum('amount') / 100,
            'pending' => Transaction::where('user_id', $user->id)
                ->where('type', 'distribution')
                ->where('status', 'pending')
                ->sum('amount') / 100,
            'count' => Transaction::where('user_id', $user->id)
                ->where('type', 'distribution')
                ->count(),
        ];

        return view('member.portfolio-modern', [
            'userCohorts' => $portfolio['cohorts'],
            'totalInvested' => $portfolio['totalInvested'],
            'totalReturns' => $portfolio['totalReturns'],
            'activeCohorts' => $portfolio['activeCohorts'],
            'returnRate' => $portfolio['returnRate'],
            'transactions' => $transactions,
            'chartData' => $chartData,
            'distributionStats' => $distributionStats,
        ]);
    }

    /**
     * Display all notifications
     */
    public function notifications(): View
    {
        $notifications = Notification::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return view('member.notifications', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead(Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['read' => true]);

        return back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead(): RedirectResponse
    {
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true, 'read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Build chart data for portfolio performance
     */
    private function buildPortfolioChartData($user): array
    {
        $months = collect();
        $contributions = collect();
        $values = collect();

        // Get last 6 months of data
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months->push($date->format('M'));

            $monthlyContributions = Transaction::query()
                ->where('user_id', $user->id)
                ->where('type', 'contribution')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');

            $contributions->push($monthlyContributions / 100);

            // Calculate running portfolio value (simplified)
            $totalValue = Transaction::query()
                ->where('user_id', $user->id)
                ->whereIn('type', ['contribution', 'distribution'])
                ->where('created_at', '<=', $date->endOfMonth())
                ->selectRaw("SUM(CASE WHEN type = 'contribution' THEN amount ELSE 0 END) as contributions")
                ->selectRaw("SUM(CASE WHEN type = 'distribution' THEN amount ELSE 0 END) as distributions")
                ->first();

            $values->push((($totalValue->contributions ?? 0) + ($totalValue->distributions ?? 0)) / 100);
        }

        return [
            'labels' => $months->toArray(),
            'contributions' => $contributions->toArray(),
            'values' => $values->toArray(),
        ];
    }
}
