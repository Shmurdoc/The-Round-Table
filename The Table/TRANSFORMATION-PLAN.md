# RoundTable System Transformation Plan
## Project Partnership Platform

> **Date:** January 8, 2026 
> **Goal:** Transform investment platform into short-term project partnership system

---

## 🎯 Core Transformation

### FROM: Investment Platform
- Members invest money
- Wait for returns
- Passive income model
- Bank + Crypto payments

### TO: Project Partnership Platform
- Members contribute to specific projects
- Active partnership with operational involvement
- Profit sharing based on contribution
- Crypto-only (USDT via NOWPayments)
- 50% profit auto-allocated to admin (operational partner)
- Weekly results distributed (Fridays)
- Daily activity updates
- Timeline tracking

---

## 📋 Key Changes

### 1. Payment System
- ❌ Remove: Bank transfers, PayFast, EFT options
- ✅ Keep Only: USDT via NOWPayments
- ✅ Add: NOWPayments API integration
- ✅ Support: USDT TRC20, ERC20, BEP20

### 2. Terminology Changes
| Old Term | New Term |
|----------|----------|
| Investment | Contribution |
| Investor | Partner |
| Returns | Profit Share |
| Cohort | Project Group |
| Admin | Project Manager |

### 3. Profit Distribution
- 50% automatically to admin (operational partner)
- 50% split among contributing partners based on contribution %
- Weekly Friday distributions
- Daily profit updates visible

### 4. Timeline & Updates
- Admin can post daily timeline updates
- Members see real-time progress
- Business days only (Mon-Fri)
- Production mode activation by admin

### 5. Voting System
- Make visible on member dashboard
- Show active votes with alerts
- Display on cohort pages prominently
- Quick vote access

### 6. Security & Trust
- Add fund safety badges
- Show escrow information
- Display transparency metrics
- Real-time activity feeds

---

## 🛠️ Technical Implementation

### Files to Modify
1. **Payment Views** (`resources/views/cohorts/join-*.blade.php`)
2. **Payment Controller** (`CryptoPaymentController.php`)
3. **Routes** (`web.php`)
4. **Models** (Cohort, Transaction, Distribution)
5. **Member Dashboard** (`dashboard.blade.php`, `dashboard-modern.blade.php`)
6. **Admin Dashboard** (add timeline/production mode controls)

### New Features to Add
1. **NOWPayments Service** (`app/Services/NOWPaymentsService.php`)
2. **Timeline System** (migrations, models, views)
3. **Weekly Distribution Job** (automated Friday payouts)
4. **Daily Activity Feed** (business days tracking)
5. **Production Mode** (cohort status expansion)
6. **Enhanced Voting UI** (dashboard widgets)

---

## ✅ Testing Requirements

Test all account types from TEST-ACCOUNTS.md:
1. Platform Admin - Full control
2. Cohort Admin - Timeline updates, production activation
3. Verified Member - Full participation
4. Pending KYC - Limited access
5. New Member - KYC redirect
6. Rejected KYC - Resubmit flow

---

## 📊 Success Metrics

- ✅ Zero bank payment options visible
- ✅ USDT payment fully functional
- ✅ Timeline updates working
- ✅ Weekly distributions automated
- ✅ Voting visible and functional
- ✅ All 6 account types tested
- ✅ Daily activity feed active
- ✅ 50% admin profit share automatic

---

*Implementation in progress...*
