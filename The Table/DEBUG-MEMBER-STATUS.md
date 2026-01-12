# Debug Member Join Issue

## Quick Checks for Madoc Mhlongo

### 1. Check Cohort Status
```sql
SELECT id, title, status FROM cohorts WHERE id = 1;
```
**Expected:** `status = 'funding'`

### 2. Check Madoc's KYC Status
```sql
SELECT id, name, email, kyc_status, role FROM users WHERE email = 'madoc.mhlongo@example.com';
```
**Expected:** `kyc_status = 'verified' OR 'approved'`

### 3. Check if Madoc is Already a Member (Pivot Table)
```sql
SELECT * FROM cohort_user WHERE user_id = (SELECT id FROM users WHERE name LIKE '%Madoc%') AND cohort_id = 1;
```
**If returns rows:** Madoc is already a member

### 4. Check if Madoc is Already a Member (CohortMembers Table)
```sql
SELECT * FROM cohort_members WHERE user_id = (SELECT id FROM users WHERE name LIKE '%Madoc%') AND cohort_id = 1;
```
**If returns rows:** Madoc is already a member

### 5. Check Madoc's User ID
```sql
SELECT id, name, email, kyc_status FROM users WHERE name LIKE '%Madoc%' OR email LIKE '%madoc%';
```

## What Madoc Should See

### Scenario 1: Not Logged In
Shows: **"Login to Join"** button

### Scenario 2: Logged In + KYC Not Verified
Shows: **"KYC Required"** (yellow box with link to KYC form)

### Scenario 3: Logged In + KYC Verified + Not a Member
Shows: **"Reserve Seat in Cohort"** button ← This is what you want

### Scenario 4: Logged In + Already a Member
Shows: **"You're a Member"** (green box)

### Scenario 5: Cohort Status is NOT 'funding'
Shows: **Nothing** (no join card at all)

## Fix Options

### If Madoc's KYC is not verified:
1. Login as Platform Admin
2. Go to: `/admin/kyc`
3. Find Madoc's KYC submission
4. Click "Approve"

### If Madoc already joined but button still shows:
There might be a duplicate join - check both tables:
- `cohort_user` pivot table
- `cohort_members` table

### If Cohort status is not 'funding':
1. Go to: `/admin/cohorts/1`
2. Find "Cohort Status Management" section
3. Change status to "Funding"
4. Click "Update Status"

## SQL Commands to Run in phpMyAdmin

```sql
-- Get full picture of Madoc's account
SELECT 
    u.id,
    u.name,
    u.email,
    u.kyc_status,
    u.role,
    cm.cohort_id,
    cm.capital_paid,
    cm.status as member_status,
    c.title as cohort_title,
    c.status as cohort_status
FROM users u
LEFT JOIN cohort_members cm ON u.id = cm.user_id
LEFT JOIN cohorts c ON cm.cohort_id = c.id
WHERE u.name LIKE '%Madoc%' OR u.email LIKE '%madoc%';
```

This will show:
- Madoc's KYC status
- Which cohorts he's in
- How much he paid
- Cohort status

## Expected Output for Working Scenario

```
id: 5
name: Madoc Mhlongo
email: madoc.mhlongo@example.com
kyc_status: verified
role: member
cohort_id: NULL (if not joined) OR 1 (if joined)
capital_paid: NULL (if not joined) OR 300000+ (if joined)
member_status: NULL (if not joined) OR 'active' (if joined)
cohort_title: NULL (if not joined) OR 'test1' (if joined)
cohort_status: funding
```

## How to Test

1. **Login as Madoc:**
 - Email: (whatever Madoc's email is)
 - Password: (whatever you set)

2. **Go to cohort page:**
 - Navigate to: `/cohorts/1`

3. **Look at the sidebar card:**
 - If you see "Reserve Seat" → Everything is working!
 - If you see "You're a Member" → Madoc already joined
 - If you see "KYC Required" → Need to approve his KYC
 - If you see nothing → Cohort status is not 'funding'
