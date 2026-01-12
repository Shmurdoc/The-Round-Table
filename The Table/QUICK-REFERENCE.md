# RoundTable - Quick Reference

## 🚀 Quick Start Commands

```bash
# Setup (first time)
cd "C:\wamp64\www\The round table\The Table"
composer install
php artisan key:generate
php artisan migrate
php artisan storage:link

# Start server
php artisan serve
# Access: http://localhost:8000

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📁 Key Files Location

| Item | Location |
|------|----------|
| Migrations | `database/migrations/` (15 files) |
| Models | `app/Models/` (8 files) |
| Controllers | `app/Http/Controllers/` (3 complete, 4 pending) |
| Routes | `routes/web.php` |
| Middleware | `app/Http/Middleware/` |
| Config | `.env` |
| Documentation | `IMPLEMENTATION-GUIDE.md`, `DEPLOYMENT-CHECKLIST.md`, `SYSTEM-SUMMARY.md` |

## 💰 Capital Configuration

```
Min Contribution: R3,000 (300000 cents)
Max Contribution: R100,000 (10000000 cents)
Currency: ZAR
Storage: Integer (cents for precision)
```

## 👥 User Roles

| Role | Access Level | Key Features |
|------|--------------|--------------|
| Member | Basic | Join cohorts, vote, view portfolio |
| Admin | Elevated | Create/manage cohorts, record transactions, publish reports |
| Platform Admin | Superuser | Approve cohorts, approve KYC, manage users, system settings |

## 🔑 Key Model Methods

### Cohort
```php
$cohort->fundingProgress() // 0-100%
$cohort->hasMetMVC() // bool
$cohort->canAcceptContribution($amount) // bool
$cohort->calculateROI() // percentage
$cohort->netIncome() // in cents
```

### User
```php
$user->isKYCVerified() // bool
$user->canParticipateInCohorts() // bool
$user->totalInvestedCapital() // in cents
$user->activeCohorts() // collection
```

### CohortMember
```php
$member->calculateOwnership() // updates ownership %
$member->getROI() // percentage
```

## 🛣️ Main Routes

### Public
- `GET /` - Home
- `GET /cohorts` - Browse cohorts
- `GET /cohorts/{cohort}` - Cohort details
- `GET /login`, `POST /login` - Authentication
- `GET /register`, `POST /register` - Registration

### Member (Auth + KYC)
- `GET /dashboard` - Dashboard
- `POST /cohorts/{cohort}/join` - Join cohort
- `GET /my-cohorts/{cohort}` - My cohort details
- `POST /votes/{vote}/cast` - Cast vote

### Admin (Role: admin)
- `GET /admin/dashboard` - Admin dashboard
- `POST /admin/cohorts` - Create cohort
- `POST /admin/cohorts/{cohort}/transactions` - Record transaction
- `POST /admin/cohorts/{cohort}/reports` - Publish report

### Platform Admin (Role: platform_admin)
- `GET /platform/dashboard` - Platform dashboard
- `POST /platform/cohorts/{cohort}/approve` - Approve cohort
- `POST /platform/kyc/{user}/approve` - Approve KYC

## 🗄️ Database Tables

1. users - User accounts
2. cohorts - Cohort management
3. cohort_members - Membership tracking
4. transactions - Financial ledger
5. reports - Reporting system
6. votes - Governance
7. vote_responses - Vote tracking
8. distributions - Payment processing
9. distribution_payments - Individual payments
10. documents - File management
11. communications - Messaging
12. notifications - Alerts
13. activity_logs - Audit trail
14. reviews - Ratings
15. settings - Configuration

## 🔒 Security Checklist

- [x] Password hashing (bcrypt)
- [x] CSRF protection
- [x] SQL injection prevention (Eloquent)
- [x] XSS protection (Blade)
- [x] Role-based access control
- [x] KYC verification gates
- [x] Session security
- [x] Activity logging
- [x] File upload validation
- [ ] 2FA (ready, needs UI)
- [ ] Rate limiting (ready, needs config)

## 📊 System Status

| Component | Status | Completion |
|-----------|--------|------------|
| Database | ✅ Complete | 100% |
| Models (Core) | ✅ Complete | 100% |
| Controllers | 🟡 Partial | 45% |
| Security | ✅ Complete | 100% |
| Routes | ✅ Complete | 100% |
| Views | 🔴 Pending | 0% |
| **TOTAL** | **🟡 70%** | **70%** |

## ⚡ Next Priority Tasks

1. Complete KYCController (2 hours)
2. Create login.blade.php (1 hour)
3. Create register.blade.php (1 hour)
4. Create dashboard views (2-3 hours)
5. Create cohort browse/details (2-3 hours)

## 📞 Support References

- **Full System Documentation**: `SYSTEM-SUMMARY.md`
- **Implementation Guide**: `IMPLEMENTATION-GUIDE.md`
- **Deployment Checklist**: `DEPLOYMENT-CHECKLIST.md`
- **Laravel Docs**: https://laravel.com/docs/12.x
- **Argon Dashboard**: `../argon-dashboard-master/`

## 🔧 Troubleshooting

### "Class not found"
```bash
composer dump-autoload
```

### "No application encryption key"
```bash
php artisan key:generate
```

### "Database not found"
```sql
CREATE DATABASE roundtable CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### "Storage link not found"
```bash
php artisan storage:link
```

### "Migrations failed"
```bash
php artisan migrate:fresh
```

## 💻 Development Workflow

1. **Start**: `php artisan serve`
2. **Make changes**: Edit files
3. **Clear cache**: `php artisan cache:clear`
4. **Test**: Visit http://localhost:8000
5. **Commit**: Git commit changes
6. **Deploy**: Follow DEPLOYMENT-CHECKLIST.md

---

**Quick Access**: This file provides instant reference for daily development tasks.
