<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cohort;
use App\Models\Distribution;
use App\Models\DistributionPayment;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Helpers\ActivityLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DistributionController extends Controller
{
    public function index()
    {
        // Get all cohorts for viewing, but only operational for creation
        $cohorts = Cohort::where('admin_id', Auth::id())
            ->orWhereHas('members', function($q) {
                $q->where('user_id', Auth::id());
            })
            ->get();
        
        $operationalCohorts = Cohort::where('admin_id', Auth::id())
            ->where('status', 'operational')
            ->get();

        $distributions = Distribution::whereIn('cohort_id', $cohorts->pluck('id'))
            ->with('cohort')
            ->latest()
            ->paginate(15);

        $totalDistributed = Distribution::whereIn('cohort_id', $cohorts->pluck('id'))
            ->where('status', 'completed')
            ->sum('total_amount');
        
        $pendingDistributions = Distribution::whereIn('cohort_id', $cohorts->pluck('id'))
            ->where('status', 'pending')
            ->sum('total_amount');
        
        $thisMonthDistributions = Distribution::whereIn('cohort_id', $cohorts->pluck('id'))
            ->whereMonth('scheduled_date', now()->month)
            ->sum('total_amount');
        
        // Convert from cents to currency
        $totalDistributed = $totalDistributed / 100;
        $pendingDistributions = $pendingDistributions / 100;
        $thisMonthDistributions = $thisMonthDistributions / 100;
        
        $avgPerMember = 0;

        return view('admin.distributions-modern', compact(
            'distributions', 'cohorts', 'operationalCohorts', 'totalDistributed', 'pendingDistributions',
            'thisMonthDistributions', 'avgPerMember'
        ));
    }

    /**
     * Show distribution details (simple route without cohort)
     */
    public function showDistribution(Distribution $distribution)
    {
        $cohort = $distribution->cohort;
        
        // Check access
        $isMember = $cohort->members()->where('user_id', Auth::id())->exists();
        $isAdmin = $cohort->admin_id === Auth::id();

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$isMember && !$isAdmin && $user->role !== 'platform_admin') {
            abort(403, 'You must be a member of this cohort to view this distribution.');
        }

        // Load relationships
        $distribution->load(['payments.cohortMember.user', 'processedBy']);

        // Calculate statistics
        $stats = [
            'total_paid' => $distribution->payments()->where('status', 'completed')->sum('amount'),
            'total_pending' => $distribution->payments()->where('status', 'pending')->sum('amount'),
            'completed_count' => $distribution->payments()->where('status', 'completed')->count(),
            'pending_count' => $distribution->payments()->where('status', 'pending')->count(),
        ];

        return view('admin.distribution-show', compact('cohort', 'distribution', 'stats', 'isAdmin'));
    }

    /**
     * Show create distribution form
     */
    public function create()
    {
        // Only allow distributions for operational cohorts
        $cohorts = Cohort::where('admin_id', Auth::id())
            ->where('status', 'operational')
            ->get();
        return view('admin.distributions.create', compact('cohorts'));
    }

    /**
     * Store new distribution
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cohort_id' => 'required|exists:cohorts,id',
            'type' => 'required|in:profit,dividend,return,periodic,final,partial_refund,emergency',
            'total_amount' => 'required|numeric|min:0',
            'distribution_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $cohort = Cohort::findOrFail($validated['cohort_id']);
        
        // Check permission
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($cohort->admin_user_id !== Auth::id() && $user->role !== 'platform_admin') {
            abort(403);
        }
        
        // Ensure cohort is operational
        if ($cohort->status !== 'operational') {
            return back()->withErrors(['cohort_id' => 'Distributions can only be created for operational cohorts.']);
        }

        // Map distribution_date to scheduled_date (actual DB column)
        $scheduledDate = $validated['distribution_date'];
        $notes = $validated['notes'] ?? 'Distribution';
        
        // Calculate total amount in cents
        $totalAmount = (int)($validated['total_amount'] * 100);

        $distribution = Distribution::create([
            'cohort_id' => $validated['cohort_id'],
            'type' => $validated['type'] === 'profit' ? 'periodic' : $validated['type'],
            'total_amount' => $totalAmount,
            'scheduled_date' => $scheduledDate,
            'description' => $notes,
            'status' => 'pending',
            'processed_by' => Auth::id(),
            'split_percentage' => 50, // 50% to admin, 50% to members pro-rata
        ]);

        // Create distribution payments (50/50 split with pro-rata member distribution)
        $distribution->createPayments();
        
        // Notify members and admin
        app(NotificationService::class)->notifyDistributionCreated($distribution, $cohort);

        return redirect()->route('admin.distributions.index')
            ->with('success', 'Distribution created successfully! Payments have been allocated.');
    }

    public function complete(Distribution $distribution)
    {
        $distribution->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // Update all related transactions to completed
        Transaction::where('reference_number', $distribution->distribution_id)
            ->where('type', 'distribution')
            ->where('status', 'pending')
            ->update([
                'status' => 'completed',
            ]);

        // Count successful payments
        $successfulCount = Transaction::where('reference_number', $distribution->distribution_id)
            ->where('type', 'distribution')
            ->where('status', 'completed')
            ->count();

        $distribution->update([
            'successful_payments' => $successfulCount,
        ]);

        // Notify all members that distribution is complete
        $notificationService = app(NotificationService::class);
        $members = $distribution->cohort->members()->with('user')->get();
        foreach ($members as $member) {
            $transaction = Transaction::where('reference_number', $distribution->distribution_id)
                ->where('user_id', $member->user_id)
                ->first();

            if ($transaction && $member->user) {
                $notificationService->notifyDistributionCompleted(
                    $distribution,
                    $distribution->cohort,
                    $member->user,
                    $transaction->amount
                );
            }
        }
        }
        
        return back()->with('success', 'Distribution marked as completed! All members have been notified.');
    }

    /**
            'distribution_type' => 'required|in:regular,special,final',
            'total_amount' => 'required|integer|min:1',
            'distribution_date' => 'required|date',
            'description' => 'required|string|max:1000',
            'distribution_basis' => 'required|in:ownership,equal,custom',
        ]);

        // Verify sufficient funds
        $availableFunds = $cohort->netIncome();
        if ($validated['total_amount'] > $availableFunds) {
            return redirect()->back()
                ->with('error', "Insufficient funds. Available: R" . number_format($availableFunds / 100, 2))
                ->withInput();
        }

        // Create distribution
        $distribution = Distribution::create([
            'cohort_id' => $cohort->id,
            'created_by' => Auth::id(),
            'distribution_type' => $validated['distribution_type'],
            'total_amount' => $validated['total_amount'],
            'distribution_date' => $validated['distribution_date'],
            'description' => $validated['description'],
            'distribution_basis' => $validated['distribution_basis'],
            'status' => 'pending',
        ]);

        // Calculate and create individual payments based on ownership
        $members = $cohort->members()->with('user')->get();
        $totalShares = $members->sum('shares');

        foreach ($members as $member) {
            // Calculate amount based on distribution basis
            $amount = match($validated['distribution_basis']) {
                'ownership' => (int) round(($member->shares / $totalShares) * $validated['total_amount']),
                'equal' => (int) round($validated['total_amount'] / $members->count()),
                'custom' => 0, // Will be set manually
            };

            DistributionPayment::create([
                'distribution_id' => $distribution->id,
                'cohort_member_id' => $member->id,
                'amount' => $amount,
                'status' => 'pending',
            ]);
        }

        // Notify all members
        $members->each(function ($member) use ($distribution, $cohort) {
            $payment = $distribution->payments()->where('cohort_member_id', $member->id)->first();
            
            Notification::send(
                $member->user_id,
                'distribution_created',
                'New Distribution Payment',
                "A distribution of R" . number_format($payment->amount / 100, 2) . " has been scheduled for {$distribution->distribution_date->format('d M Y')}.",
                $cohort->id,
                route('member.distributions.show', [$cohort, $distribution]),
                'View Details',
                'high'
            );
        });

        ActivityLog::log('distribution_created', Auth::id(), $cohort->id, "Created distribution: R" . number_format($validated['total_amount'] / 100, 2));

        return redirect()->route('admin.cohorts.show', $cohort)
            ->with('success', 'Distribution created successfully! All members have been notified.');
    }

    /**
     * Show distribution details
     */
    public function show(Cohort $cohort, Distribution $distribution)
    {
        // Verify distribution belongs to cohort
        if ($distribution->cohort_id !== $cohort->id) {
            abort(404);
        }

        // Check access
        $isMember = $cohort->members()->where('user_id', Auth::id())->exists();
        $isAdmin = $cohort->admin_id === Auth::id();

        if (!$isMember && !$isAdmin) {
            abort(403, 'You must be a member of this cohort to view this distribution.');
        }

        // Load relationships
        $distribution->load(['payments.cohortMember.user', 'creator']);

        // Calculate statistics
        $stats = [
            'total_paid' => $distribution->payments()->where('status', 'completed')->sum('amount'),
            'total_pending' => $distribution->payments()->where('status', 'pending')->sum('amount'),
            'completed_count' => $distribution->payments()->where('status', 'completed')->count(),
            'pending_count' => $distribution->payments()->where('status', 'pending')->count(),
        ];

        return view('admin.distribution-show', compact('cohort', 'distribution', 'stats', 'isAdmin'));
    }

    /**
     * Process a payment
     */
    public function processPayment(Request $request, Cohort $cohort, Distribution $distribution, DistributionPayment $payment)
    {
        // Verify user is admin of this cohort
        if ($cohort->admin_id !== Auth::id()) {
            abort(403, 'Only cohort admin can process payments.');
        }

        // Verify payment belongs to distribution
        if ($payment->distribution_id !== $distribution->id) {
            abort(404);
        }

        $validated = $request->validate([
            'transaction_reference' => 'required|string|max:255',
            'payment_proof' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Upload payment proof if provided
        $paymentProofPath = null;
        if ($request->hasFile('payment_proof')) {
            $paymentProofPath = $request->file('payment_proof')->store('distributions/payment-proofs', 'public');
        }

        // Mark payment as completed
        $payment->markAsCompleted(
            $validated['transaction_reference'],
            $paymentProofPath
        );

        // Update member's total distributions received
        $member = $payment->cohortMember;
        $member->increment('total_distributions_received', $payment->amount);

        // Check if all payments are completed
        $allCompleted = $distribution->payments()
            ->where('status', '!=', 'completed')
            ->doesntExist();

        if ($allCompleted) {
            $distribution->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        // Notify member
        Notification::send(
            $member->user_id,
            'payment_completed',
            'Payment Received',
            "Your distribution payment of R" . number_format($payment->amount / 100, 2) . " has been processed.",
            $cohort->id,
            route('member.distributions.show', [$cohort, $distribution]),
            'View Details',
            'high'
        );

        ActivityLog::log('payment_processed', Auth::id(), $cohort->id, "Processed payment: R" . number_format($payment->amount / 100, 2) . " to {$member->user->first_name} {$member->user->last_name}");

        return redirect()->back()
            ->with('success', 'Payment processed successfully!');
    }

    /**
     * Approve distribution (Platform Admin only)
     */
    public function approve(Cohort $cohort, Distribution $distribution)
    {
        // Verify user is platform admin
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->role !== 'platform_admin') {
            abort(403, 'Only platform administrators can approve distributions.');
        }

        $distribution->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        // Notify cohort admin
        Notification::send(
            $cohort->admin_id,
            'distribution_approved',
            'Distribution Approved',
            "Your distribution of R" . number_format($distribution->total_amount / 100, 2) . " has been approved and can now be processed.",
            $cohort->id,
            route('admin.distributions.show', [$cohort, $distribution]),
            'Process Payments'
        );

        ActivityLog::log('distribution_approved', Auth::id(), $cohort->id, "Approved distribution: R" . number_format($distribution->total_amount / 100, 2));

        return redirect()->back()
            ->with('success', 'Distribution approved successfully!');
    }

    /**
     * Create transactions and notifications for all cohort members
     */
    private function createMemberTransactionsAndNotifications(Distribution $distribution, Cohort $cohort): void
    {
        $members = $cohort->members()->with('user')->get();
        $membersCount = $members->count();
        
        if ($membersCount === 0) {
            return;
        }

        // Calculate amount per member
        $amountPerMember = (int)($distribution->total_amount / $membersCount);

        foreach ($members as $member) {
            // Create transaction record for the member
            Transaction::create([
                'transaction_id' => 'TXN-' . date('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'cohort_id' => $cohort->id,
                'user_id' => $member->user_id,
                'type' => 'distribution',
                'amount' => $amountPerMember,
                'currency' => 'ZAR',
                'direction' => 'credit',
                'category' => 'distribution',
                'description' => $distribution->description . ' - ' . ucfirst($distribution->type) . ' Distribution',
                'status' => 'pending',
                'transaction_date' => $distribution->scheduled_date,
                'reference_number' => $distribution->distribution_id,
            ]);

            // Create notification for the member
            Notification::create([
                'user_id' => $member->user_id,
                'cohort_id' => $cohort->id,
                'title' => 'New Distribution Scheduled',
                'message' => "A new distribution of R" . number_format($amountPerMember / 100, 2) . " has been scheduled for " . $distribution->scheduled_date->format('M d, Y') . ".",
                'type' => 'distribution',
                'action_url' => route('admin.distributions.show', $distribution),
                'action_text' => 'View Details',
                'read' => false,
                'priority' => 'high',
            ]);
        }

        // Update distribution with member count
        $distribution->update([
            'total_recipients' => $membersCount,
        ]);
    }
}
