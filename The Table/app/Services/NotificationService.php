<?php

namespace App\Services;

use App\Models\User;
use App\Models\Cohort;
use App\Models\Notification;
use App\Models\CohortMember;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Priority levels based on fintech best practices
     */
    const PRIORITY_CRITICAL = 'critical';  // Immediate delivery, multiple channels
    const PRIORITY_HIGH = 'high';          // Within 15 minutes
    const PRIORITY_MEDIUM = 'medium';      // Within 1 hour
    const PRIORITY_LOW = 'low';            // Daily digest

    /**
     * Notification types
     */
    const TYPE_PAYMENT = 'payment_received';
    const TYPE_PAYMENT_FAILED = 'payment_failed';
    const TYPE_DISTRIBUTION = 'distribution_processed';
    const TYPE_DISTRIBUTION_PENDING = 'distribution_pending';
    const TYPE_MEMBER_JOINED = 'member_joined';
    const TYPE_MEMBER_LEFT = 'member_left';
    const TYPE_STATUS_CHANGE = 'cohort_status_change';
    const TYPE_KYC = 'kyc_status';
    const TYPE_WALLET = 'wallet_transaction';
    const TYPE_ADMIN_ALERT = 'admin_message';
    const TYPE_SYSTEM = 'system_alert';

    /**
     * Send notification to user
     */
    public function notify(User $user, string $type, array $data, string $priority = self::PRIORITY_MEDIUM): void
    {
        try {
            // Create notification record
            $notification = Notification::create([
                'user_id' => $user->id,
                'cohort_id' => $data['cohort_id'] ?? null,
                'title' => $data['title'],
                'message' => $data['message'],
                'type' => $type,
                'action_url' => $data['action_url'] ?? null,
                'action_text' => $data['action_text'] ?? null,
                'priority' => $priority,
                'read' => false,
            ]);

            // Log for compliance (7-year retention requirement)
            $this->logNotification($notification, $user, $type, $priority);

            // Future: Check user preferences and send via appropriate channels
            // Future: Queue for batching based on priority
            
        } catch (\Exception $e) {
            Log::error('Notification failed', [
                'user_id' => $user->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Notify all cohort members
     */
    public function notifyCohortMembers(Cohort $cohort, string $type, array $data, string $priority = self::PRIORITY_MEDIUM): void
    {
        $members = CohortMember::where('cohort_id', $cohort->id)
            ->where('status', 'active')
            ->with('user')
            ->get();

        foreach ($members as $member) {
            if ($member->user) {
                $this->notify($member->user, $type, $data, $priority);
            }
        }
    }

    /**
     * Notify admin
     */
    public function notifyAdmin(Cohort $cohort, string $type, array $data, string $priority = self::PRIORITY_HIGH): void
    {
        if ($cohort->admin) {
            $this->notify($cohort->admin, $type, $data, $priority);
        }
    }

    /**
     * ======================================================================
     * MEMBER JOIN NOTIFICATIONS
     * ======================================================================
     */
    public function notifyMemberJoined(User $member, Cohort $cohort, int $contributionAmount): void
    {
        // Notify the member
        $this->notify($member, self::TYPE_PAYMENT, [
            'cohort_id' => $cohort->id,
            'title' => 'Welcome to ' . $cohort->title,
            'message' => sprintf(
                'Your payment of R%s has been confirmed. You are now an active member of %s. Welcome aboard!',
                number_format($contributionAmount / 100, 2),
                $cohort->title
            ),
            'action_url' => route('cohorts.show', $cohort),
            'action_text' => 'View Cohort',
        ], self::PRIORITY_HIGH);

        // Notify admin
        $this->notifyAdmin($cohort, self::TYPE_ADMIN_ALERT, [
            'cohort_id' => $cohort->id,
            'title' => 'New Member Joined',
            'message' => sprintf(
                '%s has joined %s with a contribution of R%s. Total members: %d, Total capital: R%s',
                $member->name,
                $cohort->title,
                number_format($contributionAmount / 100, 2),
                $cohort->member_count,
                number_format($cohort->current_capital / 100, 2)
            ),
            'action_url' => route('admin.cohorts.show', $cohort),
            'action_text' => 'View Cohort',
        ], self::PRIORITY_HIGH);

        // Notify other members
        $this->notifyCohortMembers($cohort, self::TYPE_MEMBER_JOINED, [
            'cohort_id' => $cohort->id,
            'title' => 'New Member Joined',
            'message' => sprintf('%s has joined %s', $member->name, $cohort->title),
            'action_url' => route('cohorts.show', $cohort),
            'action_text' => 'View Cohort',
        ], self::PRIORITY_LOW);
    }

    /**
     * ======================================================================
     * DISTRIBUTION NOTIFICATIONS
     * ======================================================================
     */
    public function notifyDistributionCreated($distribution, Cohort $cohort): void
    {
        // Notify all members about pending distribution
        $this->notifyCohortMembers($cohort, self::TYPE_DISTRIBUTION_PENDING, [
            'cohort_id' => $cohort->id,
            'title' => 'Distribution Scheduled',
            'message' => sprintf(
                'A distribution of R%s has been scheduled for %s. Expected processing date: %s',
                number_format($distribution->total_amount / 100, 2),
                $cohort->title,
                $distribution->scheduled_date->format('M d, Y')
            ),
            'action_url' => route('admin.distributions.show', $distribution),
            'action_text' => 'View Details',
        ], self::PRIORITY_MEDIUM);

        // Notify admin
        $this->notifyAdmin($cohort, self::TYPE_ADMIN_ALERT, [
            'cohort_id' => $cohort->id,
            'title' => 'Distribution Created',
            'message' => sprintf(
                'Distribution of R%s created for %s with %d members',
                number_format($distribution->total_amount / 100, 2),
                $cohort->title,
                $cohort->member_count
            ),
            'action_url' => route('admin.distributions.show', $distribution),
            'action_text' => 'Manage Distribution',
        ], self::PRIORITY_MEDIUM);
    }

    public function notifyDistributionCompleted($distribution, Cohort $cohort, User $member, $amount): void
    {
        // Notify member - CRITICAL priority for financial transactions
        $this->notify($member, self::TYPE_DISTRIBUTION, [
            'cohort_id' => $cohort->id,
            'title' => 'Distribution Completed - R' . number_format($amount / 100, 2),
            'message' => sprintf(
                'Your distribution of R%s from %s has been processed and credited to your wallet. Transaction ID: %s',
                number_format($amount / 100, 2),
                $cohort->title,
                $distribution->distribution_id ?? 'N/A'
            ),
            'action_url' => route('member.portfolio'),
            'action_text' => 'View Portfolio',
        ], self::PRIORITY_CRITICAL);
    }

    /**
     * ======================================================================
     * PAYMENT NOTIFICATIONS
     * ======================================================================
     */
    public function notifyPaymentReceived(User $user, Cohort $cohort, int $amount, string $transactionId): void
    {
        $this->notify($user, self::TYPE_PAYMENT, [
            'cohort_id' => $cohort->id,
            'title' => 'Payment Received - R' . number_format($amount / 100, 2),
            'message' => sprintf(
                'We have received your payment of R%s for %s. Transaction ID: %s. Status: Completed',
                number_format($amount / 100, 2),
                $cohort->title,
                $transactionId
            ),
            'action_url' => route('cohorts.show', $cohort),
            'action_text' => 'View Cohort',
        ], self::PRIORITY_CRITICAL);
    }

    public function notifyPaymentFailed(User $user, Cohort $cohort, int $amount, string $reason): void
    {
        $this->notify($user, self::TYPE_PAYMENT_FAILED, [
            'cohort_id' => $cohort->id,
            'title' => 'Payment Failed',
            'message' => sprintf(
                'Your payment of R%s for %s could not be processed. Reason: %s. Please try again or contact support.',
                number_format($amount / 100, 2),
                $cohort->title,
                $reason
            ),
            'action_url' => route('cohorts.join', $cohort),
            'action_text' => 'Retry Payment',
        ], self::PRIORITY_CRITICAL);
    }

    /**
     * ======================================================================
     * STATUS CHANGE NOTIFICATIONS
     * ======================================================================
     */
    public function notifyStatusChange(Cohort $cohort, string $oldStatus, string $newStatus, string $reason = ''): void
    {
        $statusMessages = [
            'draft' => 'Your cohort is in draft mode and not yet visible to members',
            'funding' => 'Your cohort is now accepting new members',
            'operational' => 'Your cohort is now operational and actively running',
            'paused' => 'Your cohort has been temporarily paused',
            'completed' => 'Your cohort has completed its lifecycle',
        ];

        $message = sprintf(
            '%s has changed status from %s to %s. %s',
            $cohort->title,
            ucfirst($oldStatus),
            ucfirst($newStatus),
            $statusMessages[$newStatus] ?? ''
        );

        if ($reason) {
            $message .= ' Reason: ' . $reason;
        }

        // Notify all members
        $this->notifyCohortMembers($cohort, self::TYPE_STATUS_CHANGE, [
            'cohort_id' => $cohort->id,
            'title' => 'Cohort Status Updated',
            'message' => $message,
            'action_url' => route('cohorts.show', $cohort),
            'action_text' => 'View Cohort',
        ], self::PRIORITY_HIGH);

        // Notify admin
        $this->notifyAdmin($cohort, self::TYPE_ADMIN_ALERT, [
            'cohort_id' => $cohort->id,
            'title' => 'Status Changed: ' . ucfirst($newStatus),
            'message' => sprintf(
                '%s status changed from %s to %s. Current members: %d, Capital: R%s',
                $cohort->title,
                ucfirst($oldStatus),
                ucfirst($newStatus),
                $cohort->member_count,
                number_format($cohort->current_capital / 100, 2)
            ),
            'action_url' => route('admin.cohorts.show', $cohort),
            'action_text' => 'Manage Cohort',
        ], self::PRIORITY_HIGH);
    }

    /**
     * ======================================================================
     * KYC NOTIFICATIONS
     * ======================================================================
     */
    public function notifyKYCApproved(User $user): void
    {
        $this->notify($user, self::TYPE_KYC, [
            'title' => 'Identity Verification Approved',
            'message' => 'Congratulations! Your identity verification has been approved. You can now join cohorts and make investments.',
            'action_url' => route('cohorts.index'),
            'action_text' => 'Browse Cohorts',
        ], self::PRIORITY_HIGH);
    }

    public function notifyKYCRejected(User $user, string $reason): void
    {
        $this->notify($user, self::TYPE_KYC, [
            'title' => 'Identity Verification Requires Attention',
            'message' => sprintf(
                'Your identity verification requires additional information. Reason: %s. Please resubmit your documents.',
                $reason
            ),
            'action_url' => route('kyc.form'),
            'action_text' => 'Resubmit Verification',
        ], self::PRIORITY_HIGH);
    }

    /**
     * ======================================================================
     * WALLET NOTIFICATIONS
     * ======================================================================
     */
    public function notifyWalletDeposit(User $user, int $amount, int $newBalance, string $transactionId): void
    {
        $this->notify($user, self::TYPE_WALLET, [
            'title' => 'Wallet Deposit Confirmed',
            'message' => sprintf(
                'Your wallet has been credited with R%s. Transaction ID: %s. New balance: R%s',
                number_format($amount / 100, 2),
                $transactionId,
                number_format($newBalance / 100, 2)
            ),
            'action_url' => route('wallet.index'),
            'action_text' => 'View Wallet',
        ], self::PRIORITY_CRITICAL);
    }

    public function notifyWalletWithdrawal(User $user, int $amount, int $newBalance, string $transactionId): void
    {
        $this->notify($user, self::TYPE_WALLET, [
            'title' => 'Withdrawal Processed',
            'message' => sprintf(
                'Your withdrawal of R%s has been processed. Transaction ID: %s. New balance: R%s. Funds will arrive in 1-3 business days.',
                number_format($amount / 100, 2),
                $transactionId,
                number_format($newBalance / 100, 2)
            ),
            'action_url' => route('wallet.index'),
            'action_text' => 'View Wallet',
        ], self::PRIORITY_CRITICAL);
    }

    public function notifyLowBalance(User $user, int $balance, int $threshold = 100000): void
    {
        if ($balance < $threshold) {
            $this->notify($user, self::TYPE_WALLET, [
                'title' => 'Low Wallet Balance',
                'message' => sprintf(
                    'Your wallet balance is R%s. Consider adding funds to participate in new opportunities.',
                    number_format($balance / 100, 2)
                ),
                'action_url' => route('wallet.deposit.form'),
                'action_text' => 'Add Funds',
            ], self::PRIORITY_LOW);
        }
    }

    /**
     * ======================================================================
     * ADMIN ALERTS
     * ======================================================================
     */
    public function notifyAdminLargeTransaction(Cohort $cohort, User $member, int $amount, string $type): void
    {
        // Alert admin for transactions over R10,000
        if ($amount >= 1000000) {
            $this->notifyAdmin($cohort, self::TYPE_ADMIN_ALERT, [
                'cohort_id' => $cohort->id,
                'title' => 'Large Transaction Alert',
                'message' => sprintf(
                    'Large %s of R%s by %s (%s) in %s. Transaction requires review.',
                    $type,
                    number_format($amount / 100, 2),
                    $member->name,
                    $member->email,
                    $cohort->title
                ),
                'action_url' => route('admin.cohorts.show', $cohort),
                'action_text' => 'Review Transaction',
            ], self::PRIORITY_CRITICAL);
        }
    }

    /**
     * ======================================================================
     * TIMELINE & DOCUMENT NOTIFICATIONS
     * ======================================================================
     */
    public function notifyTimelineUpdate(Cohort $cohort, string $title, string $description): void
    {
        $this->notifyCohortMembers($cohort, self::TYPE_ADMIN_ALERT, [
            'cohort_id' => $cohort->id,
            'title' => 'New Update: ' . $title,
            'message' => $description,
            'action_url' => route('cohorts.show', $cohort) . '#timeline',
            'action_text' => 'View Timeline',
        ], self::PRIORITY_MEDIUM);
    }

    public function notifyDocumentUploaded(Cohort $cohort, string $documentName): void
    {
        $this->notifyCohortMembers($cohort, 'document_uploaded', [
            'cohort_id' => $cohort->id,
            'title' => 'New Document Available',
            'message' => sprintf('A new document "%s" has been uploaded to %s', $documentName, $cohort->title),
            'action_url' => route('cohorts.show', $cohort),
            'action_text' => 'View Documents',
        ], self::PRIORITY_MEDIUM);
    }

    /**
     * ======================================================================
     * COMPLIANCE LOGGING
     * ======================================================================
     */
    private function logNotification(Notification $notification, User $user, string $type, string $priority): void
    {
        // Log to database for 7-year retention (regulatory requirement)
        DB::table('activity_logs')->insert([
            'user_id' => $user->id,
            'action' => 'notification_sent',
            'description' => sprintf(
                'Notification sent: %s (Priority: %s, Type: %s)',
                $notification->title,
                $priority,
                $type
            ),
            'metadata' => json_encode([
                'notification_id' => $notification->id,
                'type' => $type,
                'priority' => $priority,
                'cohort_id' => $notification->cohort_id,
            ]),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
