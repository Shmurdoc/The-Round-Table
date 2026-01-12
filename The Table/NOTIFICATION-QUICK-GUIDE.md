# NOTIFICATION SYSTEM - QUICK REFERENCE GUIDE

## 🎯 What's New?

### Distribution System
- **Admins can ONLY create distributions for operational cohorts**
- Non-operational cohorts are filtered out from the dropdown
- Clear validation prevents mistakes
- UI shows warning when no operational cohorts exist

### Notification Coverage
All critical events now trigger automatic notifications:

| Event | Member Notified | Admin Notified | Priority |
|-------|----------------|----------------|----------|
| Member Joins | ✅ Yes | ✅ Yes | HIGH |
| Payment Received | ✅ Yes | ✅ Yes | CRITICAL |
| Distribution Created | ✅ Yes | ✅ Yes | MEDIUM |
| Distribution Completed | ✅ Yes | ❌ No | CRITICAL |
| Status Change | ✅ Yes | ✅ Yes | HIGH |
| KYC Approved | ✅ Yes | ❌ No | HIGH |
| KYC Rejected | ✅ Yes | ❌ No | HIGH |
| Timeline Update | ✅ Yes | ❌ No | MEDIUM |

---

## 🚀 How to Use (For Developers)

### 1. Member Joins Cohort
**Automatic** - Already integrated in `CohortService::joinCohort()`

Sends 3 notifications:
- Member: Welcome + payment confirmation
- Admin: New member alert with stats
- Other Members: New member joined

### 2. Distribution Created
**Automatic** - Already integrated in `DistributionController::store()`

```php
app(NotificationService::class)->notifyDistributionCreated($distribution, $cohort);
```

### 3. Distribution Completed
**Automatic** - Already integrated in `DistributionController::complete()`

Notifies each member individually with their specific amount.

### 4. Status Change
**Automatic** - Already integrated in `AdminCohortController::changeStatus()`

```php
app(NotificationService::class)->notifyStatusChange($cohort, $oldStatus, $newStatus);
```

### 5. KYC Events
**Automatic** - Already integrated in `KYCController`

```php
// Approval
app(NotificationService::class)->notifyKYCApproved($user);

// Rejection
app(NotificationService::class)->notifyKYCRejected($user, $reason);
```

### 6. Timeline Update
**Automatic** - Already integrated in `TimelineController::store()`

```php
app(NotificationService::class)->notifyTimelineUpdate($cohort, $title, $description);
```

---

## 📱 Available Notification Methods

### For Members
```php
$notificationService = app(NotificationService::class);

// Payment notifications
$notificationService->notifyPaymentReceived($user, $cohort, $amount, $transactionId);
$notificationService->notifyPaymentFailed($user, $cohort, $amount, $reason);

// Wallet notifications
$notificationService->notifyWalletDeposit($user, $amount, $newBalance, $transactionId);
$notificationService->notifyWalletWithdrawal($user, $amount, $newBalance, $transactionId);
$notificationService->notifyLowBalance($user, $balance, $threshold);

// Document notifications
$notificationService->notifyDocumentUploaded($cohort, $documentName);
```

### For Admins
```php
// Large transaction alert (auto-triggered for amounts > R10,000)
$notificationService->notifyAdminLargeTransaction($cohort, $member, $amount, $type);

// Manual admin notification
$notificationService->notifyAdmin($cohort, NotificationService::TYPE_ADMIN_ALERT, [
    'cohort_id' => $cohort->id,
    'title' => 'Your Alert Title',
    'message' => 'Your message',
    'action_url' => route('admin.some.route'),
    'action_text' => 'Take Action',
], NotificationService::PRIORITY_HIGH);
```

### For All Cohort Members
```php
$notificationService->notifyCohortMembers($cohort, $type, $data, $priority);
```

---

## 🎨 Priority Levels

```php
NotificationService::PRIORITY_CRITICAL  // Immediate, financial transactions
NotificationService::PRIORITY_HIGH      // Within 15 minutes, important updates
NotificationService::PRIORITY_MEDIUM    // Within 1 hour, general updates
NotificationService::PRIORITY_LOW       // Daily digest, informational
```

**When to use each:**
- **CRITICAL**: Payments, withdrawals, distribution completed
- **HIGH**: Member joins, status changes, KYC updates
- **MEDIUM**: Distribution scheduled, timeline updates
- **LOW**: General announcements, new members

---

## 📊 Testing Your Notifications

### Test Member Join:
1. Create verified member with R10,000 wallet
2. Join operational cohort
3. Check notifications for:
 - Member: Welcome message
 - Admin: New member alert
 - Other members: New member joined

### Test Distribution:
1. Ensure cohort status is **operational**
2. Create distribution via admin panel
3. Check all members receive "Distribution Scheduled" notification
4. Complete distribution
5. Each member receives individual payment notification with correct amount

### Test Status Change:
1. Change cohort status (e.g., funding → operational)
2. All members receive notification explaining new status
3. Admin receives confirmation with stats

### Test KYC:
1. Submit KYC documents as member
2. Admin approves/rejects
3. Member receives appropriate notification

---

## 🔍 Viewing Notifications

### In Database:
```sql
-- View all notifications
SELECT * FROM notifications ORDER BY created_at DESC;

-- View user's unread notifications
SELECT * FROM notifications WHERE user_id = ? AND read = 0;

-- View cohort notifications
SELECT * FROM notifications WHERE cohort_id = ?;
```

### In Code:
```php
// Get user notifications
$notifications = Auth::user()->notifications()
    ->orderBy('created_at', 'desc')
    ->paginate(20);

// Mark as read
$notification->markAsRead();

// Unread count
$unreadCount = Auth::user()->notifications()->where('read', false)->count();
```

---

## ⚠️ Important Notes

### Distribution Rules:
- ✅ **ONLY operational cohorts** can have distributions created
- ❌ Draft, funding, paused, completed cohorts → blocked
- UI automatically filters dropdown to show only operational cohorts
- Backend validation prevents attempts to bypass UI

### Notification Best Practices:
1. **Always include transaction IDs** for financial notifications
2. **Use appropriate priority** - don't abuse CRITICAL
3. **Provide action links** - every notification should have a CTA
4. **Keep messages clear** - avoid jargon
5. **Include context** - explain what happened and why it matters

### Compliance:
- All notifications are logged to `activity_logs` table
- 7-year retention for regulatory compliance
- Logs include IP address, user agent, metadata
- Cannot be deleted (audit trail requirement)

---

## 🐛 Troubleshooting

### Notifications not appearing?
1. Check if NotificationService is being called
2. Verify user_id is correct
3. Check `notifications` table in database
4. Ensure cohort_id is valid (if applicable)

### Distribution creation blocked?
1. Verify cohort status is "operational"
2. Check admin permissions
3. Ensure cohort has members
4. Verify minimum viable capital reached

### No notifications for event?
1. Check if NotificationService method exists for that event
2. Verify integration in controller
3. Check error logs for exceptions

---

## 📞 Support

For issues or questions:
1. Check [NOTIFICATION-SYSTEM-IMPLEMENTATION.md](./NOTIFICATION-SYSTEM-IMPLEMENTATION.md) for full details
2. Review NotificationService.php for available methods
3. Check activity_logs table for compliance logging
4. Contact development team

---

## 🎉 Quick Wins

### Immediately Available:
- ✅ Member join notifications (3 per join)
- ✅ Distribution lifecycle notifications
- ✅ Status change notifications with explanations
- ✅ KYC approval/rejection notifications
- ✅ Timeline update broadcasts
- ✅ Compliance logging (7-year retention)
- ✅ Distribution filtering (operational only)

### Coming in Phase 2:
- Email notifications
- SMS notifications 
- Push notifications
- User notification preferences
- Batching/digest system
- Notification history export

---

*Quick Reference Guide - Updated January 12, 2026*
