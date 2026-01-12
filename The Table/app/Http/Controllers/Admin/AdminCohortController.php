<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cohort;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\Distribution;
use App\Models\ActivityLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class AdminCohortController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $cohorts = Cohort::where('admin_id', Auth::id())
            ->orWhere(function($q) use ($user) {
                if ($user->isPlatformAdmin()) {
                    $q->whereNotNull('id'); // Get all for platform admins
                }
            })
            ->with('admin')
            ->latest()
            ->paginate(15);

        return view('admin.cohorts.index', compact('cohorts'));
    }

    public function create()
    {
        return view('admin.cohorts.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'cohort_class' => 'required|in:utilization,lease,resale,hybrid',
            'asset_type' => 'required|in:real_estate,equipment,business,renewable_energy,intellectual_property,other',
            'minimum_viable_capital' => 'required|integer|min:300000', // R3,000 in cents
            'ideal_target' => 'required|integer|min:300000',
            'hard_cap' => 'required|integer',
            'min_contribution' => 'required|integer|min:300000|max:10000000',
            'max_contribution' => 'required|integer|min:300000|max:10000000',
            'funding_start_date' => 'required|date|after:today',
            'funding_end_date' => 'required|date|after:funding_start_date',
            'expected_deployment_date' => 'nullable|date|after:funding_end_date',
            'expected_exit_date' => 'required|date',
            'duration_months' => 'required|integer|min:1',
            'risk_level' => 'required|in:low,moderate,high',
            'projected_annual_return' => 'nullable|numeric|min:0|max:100',
            'risk_factors' => 'nullable|string',
            'exit_strategy' => 'required|string',
            'setup_fee_percent' => 'nullable|integer|min:0|max:1000', // basis points
            'management_fee_percent' => 'nullable|integer|min:0|max:2000',
            'performance_fee_percent' => 'nullable|integer|min:0|max:5000',
            'featured_image' => 'nullable|image|max:2048',
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|max:2048',
            'prospectus_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validate business rules
        if ($request->ideal_target < $request->minimum_viable_capital) {
            return back()->with('error', 'Ideal target must be greater than or equal to Minimum Viable Capital.')
                ->withInput();
        }

        if ($request->hard_cap < $request->ideal_target) {
            return back()->with('error', 'Hard cap must be greater than ideal target.')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $cohortData = $request->except(['_token', 'expected_total_payout', 'featured_image', 'prospectus_file', 'images']);
            $cohortData['admin_id'] = Auth::id();
            $cohortData['status'] = 'draft';
            $cohortData['performance_bond_amount'] = (int)($request->minimum_viable_capital * 0.05); // 5% of MVC

            // Handle file uploads
            if ($request->hasFile('featured_image')) {
                $path = $request->file('featured_image')->store('cohorts/images', 'public');
                $cohortData['featured_image'] = $path;
            }

            if ($request->hasFile('prospectus_file')) {
                $path = $request->file('prospectus_file')->store('cohorts/prospectus', 'public');
                $cohortData['prospectus_file'] = $path;
            }

            // Handle multiple images
            if ($request->hasFile('images')) {
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('cohorts/gallery', 'public');
                }
                $cohortData['images'] = $imagePaths;
            }

            $cohort = Cohort::create($cohortData);

            // Log activity
            ActivityLog::log('cohort.created', 'Admin created new cohort: ' . $cohort->title, $cohort);

            DB::commit();

            return redirect()->route('admin.cohorts.show', $cohort)
                ->with('success', 'Cohort created successfully! Submit for approval when ready.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        // Authorization check
        if (!$user->isPlatformAdmin() && $cohort->admin_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $cohort->load([
            'members.user',
            'transactions' => function($q) {
                $q->latest()->take(50);
            },
            'reports' => function($q) {
                $q->latest();
            },
            'distributions',
            'votes',
            'documents',
            'reviews',
        ]);

        // Calculate statistics
        $stats = [
            'total_revenue' => $cohort->total_revenue,
            'total_expenses' => $cohort->total_expenses,
            'net_income' => $cohort->netIncome(),
            'roi' => $cohort->calculateROI(),
            'funding_progress' => $cohort->fundingProgress(),
            'member_count' => $cohort->members()->count(),
        ];

        return view('admin.cohorts.show', compact('cohort', 'stats'));
    }

    public function edit(Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Authorization check
        if (!$user->isPlatformAdmin() && $cohort->admin_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Only draft cohorts can be edited
        if (!in_array($cohort->status, ['draft', 'pending_approval'])) {
            return back()->with('error', 'Only draft or pending cohorts can be edited.');
        }

        return view('admin.cohorts.edit', compact('cohort'));
    }

    public function update(Request $request, Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Authorization check
        if (!$user->isPlatformAdmin() && $cohort->admin_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Only draft cohorts can be updated
        if (!in_array($cohort->status, ['draft', 'pending_approval'])) {
            return back()->with('error', 'Only draft or pending cohorts can be edited.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'cohort_class' => 'required|in:utilization,lease,resale,hybrid',
            'asset_type' => 'required|in:real_estate,equipment,business,renewable_energy,intellectual_property,other',
            'minimum_viable_capital' => 'required|integer|min:300000',
            'ideal_target' => 'required|integer|min:300000',
            'hard_cap' => 'required|integer',
            'min_contribution' => 'required|integer|min:300000|max:10000000',
            'max_contribution' => 'required|integer|min:300000|max:10000000',
            'funding_start_date' => 'required|date',
            'funding_end_date' => 'required|date|after:funding_start_date',
            'expected_exit_date' => 'required|date',
            'duration_months' => 'required|integer|min:1',
            'risk_level' => 'required|in:low,moderate,high',
            'projected_annual_return' => 'nullable|numeric|min:0|max:100',
            'risk_factors' => 'nullable|string',
            'exit_strategy' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'images' => 'nullable|array|max:10',
            'images.*' => 'nullable|image|max:2048',
            'prospectus_file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Validate business rules
        if ($request->ideal_target < $request->minimum_viable_capital) {
            return back()->with('error', 'Ideal target must be greater than or equal to Minimum Viable Capital.')
                ->withInput();
        }

        if ($request->hard_cap < $request->ideal_target) {
            return back()->with('error', 'Hard cap must be greater than ideal target.')
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $cohortData = $request->except(['featured_image', 'prospectus_file', 'images', '_token', '_method']);

            // Handle file uploads
            if ($request->hasFile('featured_image')) {
                // Delete old image if exists
                if ($cohort->featured_image) {
                    Storage::disk('public')->delete($cohort->featured_image);
                }
                $path = $request->file('featured_image')->store('cohorts/images', 'public');
                $cohortData['featured_image'] = $path;
            }

            if ($request->hasFile('prospectus_file')) {
                // Delete old file if exists
                if ($cohort->prospectus_file) {
                    Storage::disk('public')->delete($cohort->prospectus_file);
                }
                $path = $request->file('prospectus_file')->store('cohorts/prospectus', 'public');
                $cohortData['prospectus_file'] = $path;
            }

            // Handle multiple images
            if ($request->hasFile('images')) {
                // Delete old gallery images if they exist
                if ($cohort->images) {
                    $oldImages = is_array($cohort->images) ? $cohort->images : json_decode($cohort->images, true);
                    if ($oldImages) {
                        foreach ($oldImages as $oldImage) {
                            Storage::disk('public')->delete($oldImage);
                        }
                    }
                }
                
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('cohorts/gallery', 'public');
                }
                $cohortData['images'] = $imagePaths;
            }

            $cohort->update($cohortData);

            // Log activity
            ActivityLog::log('cohort.updated', 'Admin updated cohort: ' . $cohort->title, $cohort);

            DB::commit();

            return redirect()->route('admin.cohorts.show', $cohort)
                ->with('success', 'Cohort updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function submitForApproval(Cohort $cohort)
    {
        if ($cohort->admin_id !== Auth::id()) {
            abort(403);
        }

        if ($cohort->status !== 'draft') {
            return back()->with('error', 'Only draft cohorts can be submitted for approval.');
        }

        // Validate completeness
        $required = ['title', 'description', 'prospectus_file', 'featured_image', 'exit_strategy'];
        foreach ($required as $field) {
            if (empty($cohort->$field)) {
                return back()->with('error', "Please complete all required fields before submitting. Missing: $field");
            }
        }

        $cohort->update(['status' => 'pending_approval']);

        ActivityLog::log('cohort.submitted', 'Cohort submitted for approval: ' . $cohort->title, $cohort);

        return back()->with('success', 'Cohort submitted for approval successfully!');
    }

    public function changeStatus(Request $request, Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Authorization check
        if (!$user->isPlatformAdmin() && $cohort->admin_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,funding,operational,paused,completed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $oldStatus = $cohort->status;
        $newStatus = $request->status;

        // Status transition validation
        if ($newStatus === 'operational' && $cohort->current_capital < $cohort->minimum_viable_capital) {
            return back()->with('error', 'Cannot set to Operational: Minimum Viable Capital not reached.');
        }

        $cohort->update(['status' => $newStatus]);

        // Log activity
        ActivityLog::log('cohort.status_changed', "Cohort status changed from {$oldStatus} to {$newStatus}: " . $cohort->title, $cohort);

        // Send notifications to members about status change
        app(NotificationService::class)->notifyStatusChange($cohort, $oldStatus, $newStatus);

        return back()->with('success', "Cohort status changed to " . ucfirst($newStatus) . " successfully!");
    }

    public function recordTransaction(Request $request, Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($cohort->admin_id !== Auth::id() && !$user->isPlatformAdmin()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'type' => 'required|in:revenue_income,operating_expense,admin_fee,asset_purchase,asset_sale,other',
            'amount' => 'required|integer|min:1',
            'direction' => 'required|in:inflow,outflow',
            'category' => 'nullable|string|max:100',
            'description' => 'required|string',
            'transaction_date' => 'required|date',
            'receipt_file' => 'nullable|file|max:5120',
            'invoice_file' => 'nullable|file|max:5120',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $transactionData = $request->except(['receipt_file', 'invoice_file']);
            $transactionData['cohort_id'] = $cohort->id;
            $transactionData['user_id'] = Auth::id();
            $transactionData['status'] = 'completed';
            $transactionData['ip_address'] = $request->ip();

            // Handle file uploads
            if ($request->hasFile('receipt_file')) {
                $transactionData['receipt_file'] = $request->file('receipt_file')
                    ->store('transactions/receipts', 'public');
            }

            if ($request->hasFile('invoice_file')) {
                $transactionData['invoice_file'] = $request->file('invoice_file')
                    ->store('transactions/invoices', 'public');
            }

            $transaction = Transaction::create($transactionData);

            // Update cohort totals
            if ($request->type === 'revenue_income') {
                $cohort->increment('total_revenue', $request->amount);
            } elseif (in_array($request->type, ['operating_expense', 'asset_purchase'])) {
                $cohort->increment('total_expenses', $request->amount);
            } elseif ($request->type === 'admin_fee') {
                $cohort->increment('admin_fees_paid', $request->amount);
            }

            ActivityLog::log('transaction.recorded', 'Transaction recorded: ' . $request->description, $transaction);

            DB::commit();

            return back()->with('success', 'Transaction recorded successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    public function createReport(Request $request, Cohort $cohort)
    {
        if ($cohort->admin_id !== Auth::id()) {
            abort(403);
        }

        return view('admin.reports.create', compact('cohort'));
    }

    public function storeReport(Request $request, Cohort $cohort)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:monthly_financial,quarterly_operational,annual_comprehensive,incident,exit',
            'title' => 'required|string|max:255',
            'period_year' => 'required|integer',
            'period_month' => 'nullable|integer|between:1,12',
            'period_quarter' => 'nullable|integer|between:1,4',
            'content' => 'required|string',
            'period_revenue' => 'nullable|integer',
            'period_expenses' => 'nullable|integer',
            'cash_position' => 'nullable|integer',
            'utilization_rate' => 'nullable|numeric|between:0,100',
            'attachments.*' => 'nullable|file|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $reportData = $request->except('attachments');
            $reportData['cohort_id'] = $cohort->id;
            $reportData['submitted_by'] = Auth::id();
            $reportData['status'] = 'published';
            $reportData['submitted_at'] = now();
            $reportData['published_at'] = now();
            $reportData['due_date'] = now(); // Calculate based on type

            // Calculate cumulative figures
            $reportData['cumulative_revenue'] = $cohort->total_revenue;
            $reportData['cumulative_expenses'] = $cohort->total_expenses;

            if ($request->filled('period_revenue')) {
                $reportData['period_net_income'] = $request->period_revenue - ($request->period_expenses ?? 0);
            }

            // Handle attachments
            if ($request->hasFile('attachments')) {
                $attachmentPaths = [];
                foreach ($request->file('attachments') as $file) {
                    $attachmentPaths[] = $file->store('reports/attachments', 'public');
                }
                $reportData['attachments'] = $attachmentPaths;
            }

            $report = Report::create($reportData);

            ActivityLog::log('report.published', 'Report published: ' . $report->title, $report);

            DB::commit();

            return redirect()->route('admin.cohorts.show', $cohort)
                ->with('success', 'Report published successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Activate/Launch a cohort (start operations)
     */
    public function activate(Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($cohort->admin_id !== Auth::id() && !$user->isPlatformAdmin()) {
            abort(403);
        }

        if (!in_array($cohort->status, ['approved', 'funding'])) {
            return back()->with('error', 'Only approved or funding cohorts can be activated.');
        }

        // Check if minimum viable capital is met
        if ($cohort->current_capital < $cohort->minimum_viable_capital) {
            return back()->with('error', 'Minimum viable capital not yet reached. Current: R' . 
                number_format($cohort->current_capital / 100, 2) . ' / Required: R' . 
                number_format($cohort->minimum_viable_capital / 100, 2));
        }

        $cohort->update([
            'status' => 'operational',
            'launched_at' => now(),
            'deployed_at' => now(),
        ]);

        // Notify all members
        $cohort->members()->each(function ($member) use ($cohort) {
            \App\Models\Notification::create([
                'user_id' => $member->user_id,
                'cohort_id' => $cohort->id,
                'title' => 'Cohort Launched!',
                'message' => "{$cohort->title} has been activated and operations have begun. Check your portfolio for updates.",
                'type' => 'cohort_launched',
            ]);
        });

        ActivityLog::log('cohort.activated', 'Cohort activated: ' . $cohort->title, $cohort);

        return back()->with('success', 'Cohort activated successfully! All members have been notified.');
    }

    /**
     * Pause cohort operations
     */
    public function pause(Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($cohort->admin_id !== Auth::id() && !$user->isPlatformAdmin()) {
            abort(403);
        }

        if ($cohort->status !== 'operational') {
            return back()->with('error', 'Only operational cohorts can be paused.');
        }

        $cohort->update(['status' => 'paused']);

        ActivityLog::log('cohort.paused', 'Cohort paused: ' . $cohort->title, $cohort);

        return back()->with('success', 'Cohort operations have been paused.');
    }

    /**
     * Resume cohort operations
     */
    public function resume(Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($cohort->admin_id !== Auth::id() && !$user->isPlatformAdmin()) {
            abort(403);
        }

        if ($cohort->status !== 'paused') {
            return back()->with('error', 'Only paused cohorts can be resumed.');
        }

        $cohort->update(['status' => 'operational']);

        ActivityLog::log('cohort.resumed', 'Cohort resumed: ' . $cohort->title, $cohort);

        return back()->with('success', 'Cohort operations have been resumed.');
    }

    /**
     * Show cohort control panel for admin
     */
    public function controlPanel(Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($cohort->admin_id !== Auth::id() && !$user->isPlatformAdmin()) {
            abort(403);
        }

        $cohort->load(['members.user', 'transactions', 'dailyProfits' => function($q) {
            $q->orderBy('profit_date', 'desc')->take(30);
        }]);

        $stats = [
            'total_profit' => $cohort->total_profit_generated,
            'admin_profit' => $cohort->admin_profit_taken,
            'members_distributed' => $cohort->members_profit_distributed,
            'pending_distribution' => \App\Models\DailyProfit::where('cohort_id', $cohort->id)
                ->where('distributed', false)->sum('members_share'),
            'member_count' => $cohort->members()->where('status', 'active')->count(),
            'total_capital' => $cohort->current_capital,
        ];

        return view('admin.cohorts.control-panel', compact('cohort', 'stats'));
    }

    /**
     * Update cohort profit (add daily profit)
     */
    public function recordProfit(Request $request, Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($cohort->admin_id !== Auth::id() && !$user->isPlatformAdmin()) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'profit_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000',
        ]);

        $amountCents = (int) round($request->amount * 100);

        // Calculate splits
        $adminSharePercent = $cohort->admin_profit_share ?? 50;
        $adminShare = (int) ($amountCents * ($adminSharePercent / 100));
        $membersShare = $amountCents - $adminShare;

        $dailyProfit = \App\Models\DailyProfit::create([
            'cohort_id' => $cohort->id,
            'profit_date' => $request->profit_date,
            'total_profit' => $amountCents,
            'admin_share' => $adminShare,
            'members_share' => $membersShare,
            'admin_share_percentage' => $adminSharePercent,
            'notes' => $request->notes,
            'distributed' => false,
        ]);

        // Update cohort totals
        $cohort->increment('total_profit_generated', $amountCents);
        $cohort->increment('admin_profit_taken', $adminShare);

        ActivityLog::log('profit.recorded', 
            "Recorded profit of R" . number_format($request->amount, 2) . " for " . $request->profit_date, 
            $dailyProfit);

        return back()->with('success', 'Profit recorded successfully! Admin share: R' . 
            number_format($adminShare / 100, 2) . ', Members share: R' . number_format($membersShare / 100, 2));
    }

    /**
     * Distribute profit to members
     */
    public function distributeProfit(Request $request, Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        if ($cohort->admin_id !== Auth::id() && !$user->isPlatformAdmin()) {
            abort(403);
        }

        $pendingProfits = \App\Models\DailyProfit::where('cohort_id', $cohort->id)
            ->where('distributed', false)
            ->get();

        if ($pendingProfits->isEmpty()) {
            return back()->with('info', 'No pending profits to distribute.');
        }

        $totalDistributed = 0;
        $membersCredited = 0;

        DB::transaction(function () use ($cohort, $pendingProfits, &$totalDistributed, &$membersCredited) {
            $activeMembers = $cohort->members()->where('status', 'active')->get();
            $totalCapital = $activeMembers->sum('capital_committed');

            foreach ($pendingProfits as $profit) {
                $membersShare = $profit->members_share;

                foreach ($activeMembers as $member) {
                    // Calculate member's share based on ownership
                    $ownership = $totalCapital > 0 ? ($member->capital_committed / $totalCapital) : 0;
                    $memberAmount = (int) ($membersShare * $ownership);

                    if ($memberAmount > 0) {
                        // Credit to member's wallet
                        $wallet = $member->user->getOrCreateWallet();
                        $wallet->increment('balance', $memberAmount);

                        // Create wallet transaction
                        $wallet->transactions()->create([
                            'user_id' => $member->user_id,
                            'transaction_id' => 'DIV-' . date('Ymd') . '-' . strtoupper(\Str::random(6)),
                            'type' => 'dividend',
                            'amount' => $memberAmount,
                            'balance_before' => $wallet->balance - $memberAmount,
                            'balance_after' => $wallet->balance,
                            'status' => 'completed',
                            'description' => "Profit share from {$cohort->title}",
                        ]);

                        // Update member distribution tracking
                        $member->increment('total_distributions_received', $memberAmount);

                        $totalDistributed += $memberAmount;
                        $membersCredited++;
                    }
                }

                // Mark profit as distributed
                $profit->update([
                    'distributed' => true,
                    'distributed_at' => now(),
                    'distributed_by' => Auth::id(),
                ]);
            }

            // Update cohort tracking
            $cohort->increment('members_profit_distributed', $totalDistributed);
        });

        ActivityLog::log('profit.distributed', 
            "Distributed R" . number_format($totalDistributed / 100, 2) . " to members", 
            $cohort);

        return back()->with('success', "Distributed R" . number_format($totalDistributed / 100, 2) . 
            " to {$membersCredited} member accounts.");
    }

    /**
     * Activate production mode for partnership
     */
    public function activateProduction(Cohort $cohort)
    {
        /** @var User $user */
        $user = Auth::user();
        
        // Verify authorization
        if (!$user->isPlatformAdmin() && $cohort->admin_id !== Auth::id()) {
            abort(403, 'Unauthorized to activate production mode');
        }

        // Check if already in production
        if ($cohort->production_mode) {
            return back()->with('warning', 'This partnership is already in production mode.');
        }

        // Verify funding is complete
        if (!$cohort->hasMetMVC()) {
            return back()->with('error', 'Cannot activate production mode. Minimum viable capital not reached.');
        }

        // Activate production
        if ($cohort->activateProduction($user)) {
            ActivityLog::log('production.activated', 
                "Production mode activated by " . $user->first_name . " " . $user->last_name, 
                $cohort);

            return back()->with('success', '🚀 Production mode activated! Partnership is now operational. Daily updates and weekly distributions will begin.');
        }

        return back()->with('error', 'Failed to activate production mode.');
    }
}
