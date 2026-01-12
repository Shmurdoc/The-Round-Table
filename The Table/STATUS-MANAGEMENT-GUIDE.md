# Cohort Status Management Guide

## Overview
Admins now have full control over cohort status to manage member access and cohort lifecycle.

## Status Flow & Visibility

### Status Types

| Status | Description | Visible to Members? | Can Join? |
|--------|-------------|---------------------|-----------|
| **Draft** | Cohort is being prepared | ❌ No | ❌ No |
| **Funding** | Open for member contributions | ✅ Yes | ✅ Yes |
| **Operational** | Active and generating profits | ✅ Yes | ❌ No (closed) |
| **Paused** | Temporarily paused | ✅ Yes | ❌ No |
| **Completed** | Finished lifecycle | ✅ Yes | ❌ No |

## How to Change Status

### Step-by-Step Instructions

1. **Navigate to Cohort**
 - Go to: `/admin/cohorts/{cohort_id}`
 - You'll see the "Cohort Status Management" panel

2. **Review Current Status**
 - Current status is displayed with a colored badge
 - Read the status guide for each status type

3. **Select New Status**
 - Choose from the dropdown:
 - Draft (Private)
 - Funding (Open for Members) ← **Select this to allow members to join**
 - Operational (Active)
 - Paused
 - Completed

4. **Update Status**
 - Click "Update Status" button
 - Confirmation message will appear

## Common Workflows

### Making a New Cohort Visible to Members

**Problem:** Just created a cohort called "test1" but members can't see or join it.

**Solution:**
1. Go to Admin Dashboard → Cohorts
2. Click on "test1" cohort
3. Find "Cohort Status Management" section
4. Change status from **Draft** → **Funding**
5. Click "Update Status"
6. ✅ Members can now see and join the cohort!

### Closing a Cohort to New Members

**When:** You've reached your target capital or member count.

**Steps:**
1. Open the cohort in admin panel
2. Change status from **Funding** → **Operational**
3. Members can no longer join, but existing members remain

### Temporarily Pausing Operations

**When:** Need to halt operations for maintenance or restructuring.

**Steps:**
1. Change status to **Paused**
2. No new joins allowed
3. Existing members can still view
4. Change to **Operational** to resume

## Technical Details

### Database Fields
- `cohorts.status` column stores the current status
- Valid values: `draft`, `funding`, `operational`, `paused`, `completed`

### Route
```
PATCH /admin/cohorts/{cohort}/change-status
```

### Controller Method
```php
AdminCohortController@changeStatus
```

### Validation Rules
- Status must be one of: `draft`, `funding`, `operational`, `paused`, `completed`
- Cannot set to `operational` if current capital < minimum viable capital

### Activity Logging
- All status changes are logged with old and new status
- Viewable in activity logs

## Member Experience

### When Status = Draft
- Cohort **NOT visible** in cohort listing
- Cannot access cohort detail page
- No "Reserve Seat" button

### When Status = Funding
- Cohort **IS visible** in cohort listing
- Can view cohort details
- "Reserve Seat in Cohort" button appears (if KYC verified)
- Can join and contribute capital

### When Status = Operational
- Cohort visible in listings
- Can view details
- "Reserve Seat" button hidden (funding closed)
- Displays "Operational" badge

### When Status = Paused
- Cohort visible
- Shows "Paused" warning
- No new members can join

### When Status = Completed
- Cohort visible for historical reference
- Shows "Completed" badge
- No actions available

## Best Practices

1. **Start in Draft**
 - Finalize all details before opening to members
 - Upload featured image and prospectus
 - Set accurate dates and targets

2. **Move to Funding When Ready**
 - Ensure all information is accurate
 - Featured image uploaded
 - Risk factors documented
 - Exit strategy defined

3. **Monitor During Funding**
 - Watch capital progress
 - Check member count
 - Review contributions

4. **Transition to Operational**
 - When MVC reached or target date arrives
 - Close new memberships
 - Begin profit recording
 - Activate production mode

5. **Use Paused Status Sparingly**
 - Only for temporary halts
 - Communicate with members
 - Resume as soon as possible

6. **Complete When Finished**
 - At end of cohort lifecycle
 - After final distributions
 - Keep visible for records

## Troubleshooting

### "Members can't see my cohort"
**Fix:** Change status from Draft to Funding

### "Reserve Seat button not showing"
**Possible causes:**
- Status is not "Funding" → Change to Funding
- User not logged in → User must login
- User KYC not verified → User must complete KYC
- User already a member → They can't join twice

### "Can't change to Operational"
**Error:** "Minimum Viable Capital not reached"
**Fix:** Wait until enough members join and contribute the MVC amount

### "Status changed but members still can't join"
**Check:**
- Cohort status is exactly "funding" (lowercase)
- User has verified KYC status
- Hard cap not exceeded
- Funding end date not passed

## Quick Reference

### Admin Panel Location
```
Admin Dashboard → Cohorts → [Select Cohort] → Cohort Status Management
```

### Member Join Conditions
```php
$cohort->status === 'funding'
&& auth()->user()->kyc_status === 'verified'
&& !$alreadyMember
&& $cohort->current_capital < $cohort->hard_cap
```

### Status Change Notification
When changing to "funding", consider notifying:
- All verified members
- Email notification
- Platform notification

---

**Last Updated:** January 11, 2026 
**Version:** 1.0
