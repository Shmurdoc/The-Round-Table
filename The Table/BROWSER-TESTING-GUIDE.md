# 🧪 Browser Testing Guide - All Features & All Accounts

## ✅ Automated Tests: 100% PASSED (27/27)

All backend systems tested and verified. Now test the UI in your browser.

---

## 🌐 TESTING URL
**http://127.0.0.1:8000**

The Laravel development server is running. Access all features through this URL.

---

## 📋 TEST CHECKLIST

### 🔴 TEST 1: Platform Admin (`platform.admin@roundtable.co.za` / `Platform@2026`)

**Dashboard Access:**
- [ ] Login successful → redirects to admin dashboard
- [ ] Dashboard shows platform statistics (users, cohorts, revenue)
- [ ] Can see all cohorts across the platform
- [ ] Navigation menu shows admin options

**Cohort Approvals:**
- [ ] Navigate to pending cohorts
- [ ] Can view cohort details
- [ ] Can approve/reject cohorts
- [ ] Approval triggers notification to cohort admin

**KYC Management:**
- [ ] Navigate to KYC review section
- [ ] Can see pending KYC submissions
- [ ] Can review member documents
- [ ] Can approve/reject KYC
- [ ] Rejection reason field works

**User Management:**
- [ ] Can view all users
- [ ] Can see user roles and statuses
- [ ] User search/filter works
- [ ] Can access user details

---

### 🟠 TEST 2: Cohort Admin (`cohort.admin@roundtable.co.za` / `Cohort@2026`)

**Dashboard Access:**
- [ ] Login successful → redirects to admin dashboard
- [ ] Dashboard shows cohort statistics
- [ ] Can see managed cohorts
- [ ] Quick actions work (create cohort, view members, etc.)

**Cohort Creation:**
- [ ] Navigate to "Create Cohort"
- [ ] Form loads with all fields
- [ ] Can select cohort type (Utilization/Lease/Resale)
- [ ] Validation works (required fields)
- [ ] Submit creates cohort successfully
- [ ] Redirects to cohort details page

**Production Mode Activation:**
- [ ] Navigate to cohort details
- [ ] "Activate Production Mode" button visible
- [ ] Click button activates production
- [ ] Status changes to "Production"
- [ ] Activation timestamp displays
- [ ] All partners notified

**Timeline System:**
- [ ] Timeline form visible on cohort page
- [ ] Can select event type (Progress/Profit/Milestone/Update/Meeting/Achievement/Alert)
- [ ] Can enter title and description
- [ ] Can set event date
- [ ] Can upload proof documents (for profits)
- [ ] Profit amount field appears for "Profit Recording"
- [ ] Submit posts update successfully
- [ ] Timeline feed shows new entry
- [ ] Partners receive notifications

**Profit Recording:**
- [ ] Select "Profit Recording" event type
- [ ] Enter profit amount (e.g., R 5,000)
- [ ] Upload proof document
- [ ] Submit records profit
- [ ] Partners see profit in timeline

**Member Management:**
- [ ] Can view cohort members list
- [ ] Can see member contributions
- [ ] Can see member KYC status
- [ ] Member details accessible

**Voting:**
- [ ] Can create new vote
- [ ] Can set voting deadline
- [ ] Can add voting options
- [ ] Vote visible to members
- [ ] Can see voting results

---

### 🟢 TEST 3: Verified Member (`verified.member@roundtable.co.za` / `Member@2026`)

**Dashboard Access:**
- [ ] Login successful → redirects to member dashboard
- [ ] Dashboard shows portfolio summary
- [ ] Active cohorts displayed
- [ ] Wallet balance visible
- [ ] Recent notifications shown

**Voting Widget:**
- [ ] Voting widget visible on dashboard
- [ ] Shows active votes count
- [ ] "Cast Vote Now" buttons work
- [ ] Vote participation tracked
- [ ] Visual indicators for voted vs pending

**Browse Cohorts:**
- [ ] Navigate to "Browse Cohorts"
- [ ] Can see available cohorts
- [ ] Can filter by type (Utilization/Lease/Resale)
- [ ] Can search cohorts
- [ ] Cohort cards show key info (capital, members, ROI)

**Join Partnership (USDT Only):**
- [ ] Click "Join Partnership" on cohort
- [ ] Partnership form loads (NOT bank form)
- [ ] Shows USDT payment options only
- [ ] Shows 50/50 profit split explanation
- [ ] Partnership terms displayed clearly
- [ ] Can select contribution amount
- [ ] TRC20/BEP20/ERC20 options visible
- [ ] Submit generates payment address

**View Timeline:**
- [ ] Navigate to joined cohort details
- [ ] Timeline feed visible (if production mode active)
- [ ] Can see all timeline entries
- [ ] Event type badges show correct colors
- [ ] Profit amounts displayed
- [ ] Proof documents accessible
- [ ] "Posted by" information shows

**Wallet Operations:**
- [ ] Navigate to wallet
- [ ] Balance displays correctly
- [ ] Transaction history visible
- [ ] Can request withdrawal
- [ ] Withdrawal limits enforced (R3,000-R100,000)

**Portfolio:**
- [ ] Navigate to "My Portfolio"
- [ ] Shows all joined cohorts
- [ ] Shows contribution amounts
- [ ] Shows profit distributions
- [ ] ROI calculations visible
- [ ] Can view cohort details

**Voting:**
- [ ] Receives vote notifications
- [ ] Can access votes from dashboard
- [ ] Can cast vote on active proposals
- [ ] Vote confirmation shown
- [ ] Can see voting results

---

### 🟡 TEST 4: Pending KYC Member (`pending.member@roundtable.co.za` / `Member@2026`)

**Access Restrictions:**
- [ ] Login successful → redirects to dashboard
- [ ] Dashboard shows "KYC Pending" banner
- [ ] Cannot join cohorts (button disabled)
- [ ] Cannot make deposits
- [ ] Cannot request withdrawals
- [ ] Can view cohorts (browse only)
- [ ] Can view wallet (should be empty)

**KYC Status:**
- [ ] "KYC Status: Pending" displayed prominently
- [ ] Shows "Under Review" message
- [ ] Timeline of submission visible
- [ ] No action required from member

---

### ⚪ TEST 5: New Member (No KYC) (`new.member@roundtable.co.za` / `Member@2026`)

**KYC Redirect:**
- [ ] Login successful → redirects to KYC form
- [ ] Cannot access other pages without KYC
- [ ] KYC form loads correctly
- [ ] All fields visible (ID, Proof of Address, etc.)
- [ ] Can upload documents
- [ ] Validation works
- [ ] Submit sends for review

**Feature Restrictions:**
- [ ] Cannot join cohorts
- [ ] Cannot access wallet
- [ ] Cannot make transactions
- [ ] Can browse cohorts (read-only)

---

### 🔵 TEST 6: Rejected KYC Member (`rejected.member@roundtable.co.za` / `Member@2026`)

**Rejection Message:**
- [ ] Login successful → sees rejection banner
- [ ] Rejection reason displayed clearly
- [ ] Example: "ID document was unclear. Please resubmit with a clearer photo."
- [ ] "Resubmit KYC" button visible

**Resubmission:**
- [ ] Click "Resubmit KYC"
- [ ] KYC form loads
- [ ] Previous rejection reason shown as reminder
- [ ] Can upload new documents
- [ ] Submit sends for re-review
- [ ] Status changes to "Pending"

---

## 🎯 TRANSFORMATION FEATURES - CRITICAL TESTS

### ✅ Partnership Terminology Check
- [ ] No "Investment" language visible (should be "Partnership/Contribution")
- [ ] No "Investor" language (should be "Partner")
- [ ] Forms say "Join Partnership" not "Invest"
- [ ] Wallet shows "Contribution" not "Investment"

### ✅ USDT-Only Payments Check
- [ ] NO bank account fields on join form
- [ ] Only USDT payment options (TRC20/BEP20/ERC20)
- [ ] Partnership-focused UI with 50/50 split explanation
- [ ] Old bank forms not accessible

### ✅ 50% Profit Split Check
- [ ] Admin receives 50% automatically (check in timeline when profit recorded)
- [ ] Partners share remaining 50% pro-rata
- [ ] Split visible in distribution records

### ✅ Timeline Transparency Check
- [ ] Timeline visible to all partners on cohort page
- [ ] Shows daily updates, profits, milestones
- [ ] Proof documents accessible
- [ ] Business day tracking works (Mon-Fri)

### ✅ Weekly Distribution Check (Manual Test)
Run in terminal:
```bash
php artisan profits:distribute-weekly --force
```
- [ ] Command runs successfully
- [ ] Calculates week's profits from timeline
- [ ] Applies 50/50 split automatically
- [ ] Credits partner wallets
- [ ] Sends notifications

### ✅ Voting Visibility Check
- [ ] Voting widget visible on member dashboard
- [ ] Shows active vote count
- [ ] "Cast Vote Now" buttons work
- [ ] Vote participation tracked

---

## 💰 PAYMENT TESTING (Requires API Keys)

**Prerequisites:**
Add to `.env`:
```env
NOWPAYMENTS_API_KEY=your_api_key_here
NOWPAYMENTS_IPN_SECRET=your_ipn_secret_here
NOWPAYMENTS_SANDBOX=true
```

**Test Flow:**
1. Login as verified member
2. Join a cohort
3. Select USDT payment (TRC20 recommended)
4. System generates payment address
5. Send test payment
6. Webhook receives confirmation
7. Wallet credited automatically
8. Cohort membership activated

---

## 📊 SUCCESS CRITERIA

**All tests passed if:**
- ✅ All 6 account types login successfully
- ✅ Each role sees appropriate dashboard and features
- ✅ No bank payment forms accessible
- ✅ USDT payment system works
- ✅ Timeline system visible and functional
- ✅ Voting widget visible on dashboard
- ✅ Production mode activation works
- ✅ 50/50 profit split automatic
- ✅ Partnership terminology throughout (no "investment" language)

---

## 🚨 COMMON ISSUES & FIXES

**Issue: Routes not found (404)**
- **Fix:** Restart Laravel server: `php artisan serve`

**Issue: Session expired**
- **Fix:** Clear cookies and re-login

**Issue: KYC redirect loop**
- **Fix:** Check user's kyc_status in database

**Issue: Payment not working**
- **Fix:** Verify NOWPayments API keys in .env

**Issue: Timeline not showing**
- **Fix:** Verify cohort has production_mode = 1

---

## 🎉 COMPLETION

Once all tests pass, the system is **100% production-ready** for deployment!

**Next Steps:**
1. Add NOWPayments API keys (production)
2. Configure webhook URL in NOWPayments dashboard
3. Deploy to production server
4. Test with real partners

---

**Test Duration:** Approximately 2-3 hours for complete testing

**Last Updated:** January 9, 2026 
**System Version:** 1.0.0 - Partnership Platform 
**Automated Tests:** 27/27 Passed ✅
