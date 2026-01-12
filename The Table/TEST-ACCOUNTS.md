# RoundTable Test Accounts

> **Created:** January 6, 2026 
> **Purpose:** Test accounts for all user roles and KYC states in the RoundTable platform

---

## 🔐 Login URL
```
http://localhost/The%20round%20table/The%20Table/public/login
```

---

## 👤 Account Types & Credentials

### 1. 🔴 Platform Admin (Super Admin)
**Highest level of access - manages entire platform**

| Field | Value |
|-------|-------|
| **Email** | `platform.admin@roundtable.co.za` |
| **Password** | `Platform@2026` |
| **Role** | `platform_admin` |
| **KYC Status** | Verified |
| **Phone** | 0800001111 |

**Capabilities:**
- Approve/Reject cohort applications
- Review and approve all KYC submissions
- View all cohorts across the platform
- Manage platform settings
- Access platform-wide analytics
- Manage all users

---

### 2. 🟠 Cohort Admin (Investment Manager)
**Can create and manage investment cohorts**

| Field | Value |
|-------|-------|
| **Email** | `cohort.admin@roundtable.co.za` |
| **Password** | `Cohort@2026` |
| **Role** | `admin` |
| **KYC Status** | Verified |
| **Phone** | 0800002222 |
| **Bank** | Capitec |
| **Account** | 1234567890 |
| **Branch Code** | 470010 |

**Capabilities:**
- Create new investment cohorts
- Manage cohort members
- Record daily profits
- Distribute dividends
- Create votes for cohort members
- Post cohort updates/reports
- Manage cohort investments

---

### 3. 🟢 Verified Member (Full Access Partner)
**Regular member with verified KYC - full platform access**

| Field | Value |
|-------|-------|
| **Email** | `verified.member@roundtable.co.za` |
| **Password** | `Member@2026` |
| **Role** | `member` |
| **KYC Status** | Verified ✅ |
| **Phone** | 0800003333 |
| **Bank** | FNB |
| **Account** | 9876543210 |
| **Branch Code** | 250655 |

**Capabilities:**
- Join investment cohorts
- Make deposits and withdrawals
- View wallet and transactions
- Participate in votes
- View investment portfolio
- Receive profit distributions

---

### 4. 🟡 Pending KYC Member
**Member who submitted KYC but awaiting approval**

| Field | Value |
|-------|-------|
| **Email** | `pending.member@roundtable.co.za` |
| **Password** | `Member@2026` |
| **Role** | `member` |
| **KYC Status** | Pending ⏳ |
| **Phone** | 0800004444 |

**Limitations:**
- ❌ Cannot join cohorts
- ❌ Cannot make deposits
- ❌ Cannot invest
- ✅ Can view cohorts
- ✅ Can view wallet (empty)

---

### 5. ⚪ New Member (No KYC)
**Newly registered member who hasn't submitted KYC**

| Field | Value |
|-------|-------|
| **Email** | `new.member@roundtable.co.za` |
| **Password** | `Member@2026` |
| **Role** | `member` |
| **KYC Status** | Not Started 📝 |
| **Phone** | 0800005555 |

**Limitations:**
- ❌ Cannot join cohorts
- ❌ Cannot make deposits
- ❌ Cannot invest
- ✅ Will be redirected to KYC page
- ✅ Can browse and view cohorts

---

### 6. 🔵 Rejected KYC Member
**Member whose KYC was rejected - needs to resubmit**

| Field | Value |
|-------|-------|
| **Email** | `rejected.member@roundtable.co.za` |
| **Password** | `Member@2026` |
| **Role** | `member` |
| **KYC Status** | Rejected ❌ |
| **Phone** | 0800006666 |
| **Rejection Reason** | ID document was unclear. Please resubmit with a clearer photo. |

**Limitations:**
- ❌ Cannot join cohorts
- ❌ Cannot make deposits
- ✅ Can resubmit KYC
- ✅ Will see rejection reason

---

### 7. 🟢 Jane Smith (Verified Member)
**Active verified member with wallet balance**

| Field | Value |
|-------|-------|
| **Email** | `jane.smith@roundtable.co.za` |
| **Password** | `Jane@2026` |
| **Role** | `member` |
| **KYC Status** | Verified ✅ |
| **Phone** | 0800007777 |
| **Bank** | Standard Bank |
| **Account** | 5551234567 |
| **Branch Code** | 051001 |

**Capabilities:**
- Join investment cohorts
- Make deposits and withdrawals
- View wallet and transactions
- Participate in votes
- View investment portfolio
- Receive profit distributions

---

## 📊 Quick Reference Table

| Account Type | Email | Password | Role | KYC |
|-------------|-------|----------|------|-----|
| Platform Admin | platform.admin@roundtable.co.za | Platform@2026 | platform_admin | ✅ Verified |
| Cohort Admin | cohort.admin@roundtable.co.za | Cohort@2026 | admin | ✅ Verified |
| Verified Member | verified.member@roundtable.co.za | Member@2026 | member | ✅ Verified |
| Pending Member | pending.member@roundtable.co.za | Member@2026 | member | ⏳ Pending |
| New Member | new.member@roundtable.co.za | Member@2026 | member | 📝 Not Started |
| Rejected Member | rejected.member@roundtable.co.za | Member@2026 | member | ❌ Rejected |
| Jane Smith | jane.smith@roundtable.co.za | Jane@2026 | member | ✅ Verified |

---

## 🔗 Important URLs

| Page | URL |
|------|-----|
| Login | `/login` |
| Register | `/register` |
| KYC Verification | `/kyc` |
| Member Dashboard | `/dashboard` |
| Admin Dashboard | `/admin/dashboard` |
| Platform Admin | `/platform-admin/dashboard` |
| Cohorts | `/cohorts` |
| Wallet | `/wallet` |

---

## 🛡️ Role Hierarchy

```
Platform Admin (platform_admin)
    └── Full platform control
    └── KYC approvals
    └── Cohort approvals
    
Cohort Admin (admin)
    └── Cohort management
    └── Profit recording
    └── Member management
    
Member (member)
    └── KYC Verified → Full access
    └── KYC Pending → View only
    └── KYC Not Started → Redirected to KYC
    └── KYC Rejected → Can resubmit
```

---

## 📝 Notes

1. **Passwords expire:** Consider changing passwords if using in production
2. **Test environment only:** These accounts are for development/testing
3. **KYC documents:** Test accounts don't have actual document uploads
4. **Wallets:** Created automatically on first access for verified members

---

*Last updated: January 6, 2026*
