<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CohortController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\Admin\AdminCohortController;
use App\Http\Controllers\Admin\DistributionController;
use App\Http\Controllers\Admin\TimelineController;
use App\Http\Controllers\Platform\PlatformAdminController;
use App\Http\Controllers\KYCController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\Admin\WalletAdminController;
use App\Http\Controllers\Admin\ProfitController;
use App\Http\Controllers\CryptoPaymentController;
use App\Http\Controllers\NOWPaymentsController;
use App\Http\Controllers\ImpactSimulatorController;

// Public Routes
Route::get('/', function () {
    return view('welcome-modern');
})->name('home');

Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/how-it-works', function () {
    return view('pages.how-it-works');
})->name('how-it-works');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Cohort Management (Admin/Platform Admin)
Route::middleware(['auth', 'role:admin,platform_admin'])->group(function () {
    Route::get('/cohorts/create', [CohortController::class, 'create'])->name('cohorts.create');
    Route::post('/cohorts', [CohortController::class, 'store'])->name('cohorts.store');
    Route::get('/cohorts/{cohort}/edit', [CohortController::class, 'edit'])->name('cohorts.edit');
    Route::put('/cohorts/{cohort}', [CohortController::class, 'update'])->name('cohorts.update');
    Route::post('/cohorts/{cohort}/activate', [CohortController::class, 'activate'])->name('cohorts.activate');
});

// Member Routes (Authenticated + KYC Verified)
Route::middleware(['auth'])->group(function () {
    // Cohort Discovery (Authenticated users)
    Route::get('/cohorts', [CohortController::class, 'index'])->name('cohorts.index');
    Route::get('/cohorts/{cohort}', [CohortController::class, 'show'])->name('cohorts.show');
    
    // KYC Routes
    Route::get('/kyc', [KYCController::class, 'form'])->name('kyc.form');
    Route::post('/kyc', [KYCController::class, 'submit'])->name('kyc.submit');

    // Dashboard - Using MemberController with modern views
    Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('member.dashboard');

    // Portfolio & Dashboard - Using MemberController with modern views
    Route::get('/member/portfolio', [MemberController::class, 'portfolio'])->name('member.portfolio');

    // Notifications
    Route::get('/member/notifications', [MemberController::class, 'notifications'])->name('member.notifications');
    Route::post('/member/notifications/{notification}/read', [MemberController::class, 'markNotificationRead'])->name('member.notifications.read');
    Route::post('/member/notifications/mark-all-read', [MemberController::class, 'markAllNotificationsRead'])->name('member.notifications.mark-all-read');

    // Cohort Participation (Requires KYC)
    Route::middleware(['kyc'])->group(function () {
        Route::get('/cohorts/{cohort}/join', [CohortController::class, 'showJoinForm'])->name('cohorts.join');
        Route::post('/cohorts/{cohort}/process-payment', [CohortController::class, 'processPayment'])->name('cohorts.process-payment');
        
        // NOWPayments USDT Payment Routes
        Route::post('/payments/nowpayments/initiate', [NOWPaymentsController::class, 'initiatePayment'])->name('payments.nowpayments.initiate');
        Route::post('/payments/nowpayments/status', [NOWPaymentsController::class, 'checkPaymentStatus'])->name('payments.nowpayments.status');
        
        Route::delete('/cohorts/{cohort}/leave', [CohortController::class, 'leave'])->name('cohorts.leave');
        Route::get('/my-cohorts/{cohort}', [CohortController::class, 'memberCohortDetails'])->name('member.cohort.details');

        // Voting
        Route::get('/cohorts/{cohort}/votes/{vote}', [VoteController::class, 'show'])->name('member.votes.show');
        Route::post('/cohorts/{cohort}/votes/{vote}/cast', [VoteController::class, 'cast'])->name('member.votes.cast');
        Route::post('/votes/{vote}/cast', [VoteController::class, 'cast'])->name('votes.cast');

        // Profile & Settings
        Route::get('/profile', function () {
            return view('member.profile');
        })->name('member.profile');
        Route::post('/profile', [KYCController::class, 'updateProfile'])->name('member.profile.update');

        // Banking Details
        Route::post('/banking', [KYCController::class, 'updateBanking'])->name('member.banking.update');

        // Deposit & Withdrawal (Legacy TransactionController) - Requires KYC
        Route::get('/member/deposit', [TransactionController::class, 'depositForm'])->name('member.deposit.form');
        Route::post('/member/deposit', [TransactionController::class, 'depositSubmit'])->name('member.deposit.submit');
        Route::get('/member/withdraw', [TransactionController::class, 'withdrawForm'])->name('member.withdraw.form');
        Route::post('/member/withdraw', [TransactionController::class, 'withdrawSubmit'])->name('member.withdraw.submit');

        // Wallet System - Requires KYC
        Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
        Route::get('/wallet/deposit', [WalletController::class, 'depositForm'])->name('wallet.deposit.form');
        Route::post('/wallet/deposit', [WalletController::class, 'depositSubmit'])->name('wallet.deposit.submit');
        Route::get('/wallet/deposit/{transaction}', [WalletController::class, 'depositConfirm'])->name('wallet.deposit.confirm');
        Route::get('/wallet/withdraw', [WalletController::class, 'withdrawForm'])->name('wallet.withdraw.form');
        Route::post('/wallet/withdraw', [WalletController::class, 'withdrawSubmit'])->name('wallet.withdraw.submit');
        Route::get('/wallet/transfer', [WalletController::class, 'transferForm'])->name('wallet.transfer.form');
        Route::post('/wallet/transfer', [WalletController::class, 'transferSubmit'])->name('wallet.transfer.submit');
        Route::get('/wallet/transactions', [WalletController::class, 'transactions'])->name('wallet.transactions');
        Route::post('/wallet/cohort/{cohortFund}/withdraw', [WalletController::class, 'withdrawFromCohort'])->name('wallet.cohort.withdraw');

        // Crypto Payment Routes (Requires KYC)
        Route::get('/crypto/deposit', [CryptoPaymentController::class, 'depositPage'])->name('crypto.deposit');
        Route::post('/crypto/generate-address', [CryptoPaymentController::class, 'generateAddress'])->name('crypto.generate-address');
        Route::get('/crypto/withdrawal', [CryptoPaymentController::class, 'withdrawalPage'])->name('crypto.withdrawal');
        Route::post('/crypto/withdrawal', [CryptoPaymentController::class, 'processWithdrawal'])->name('crypto.process-withdrawal');
        Route::get('/crypto/rates', [CryptoPaymentController::class, 'getRates'])->name('crypto.rates');
        Route::get('/crypto/history', [CryptoPaymentController::class, 'transactionHistory'])->name('crypto.history');

        // Impact Simulator Routes (Requires KYC)
        Route::get('/simulator', [ImpactSimulatorController::class, 'index'])->name('simulator.index');
        Route::post('/simulator/utilization', [ImpactSimulatorController::class, 'simulateUtilization'])->name('simulator.utilization');
        Route::post('/simulator/lease', [ImpactSimulatorController::class, 'simulateLease'])->name('simulator.lease');
        Route::post('/simulator/resale', [ImpactSimulatorController::class, 'simulateResale'])->name('simulator.resale');
        Route::post('/simulator/monte-carlo', [ImpactSimulatorController::class, 'monteCarlo'])->name('simulator.monte-carlo');
        Route::post('/simulator/portfolio', [ImpactSimulatorController::class, 'analyzePortfolio'])->name('simulator.portfolio');
        Route::post('/simulator/save', [ImpactSimulatorController::class, 'saveSimulation'])->name('simulator.save');
        Route::post('/simulator/export-pdf', [ImpactSimulatorController::class, 'exportPDF'])->name('simulator.export-pdf');
        Route::post('/simulator/compare', [ImpactSimulatorController::class, 'compareScenarios'])->name('simulator.compare');
    });
});

// Crypto Webhook (Public - but signature verified)
Route::post('/webhook/crypto/deposit', [CryptoPaymentController::class, 'depositWebhook'])->name('webhook.crypto.deposit');

// NOWPayments IPN Webhook (Public - signature verified in controller)
Route::post('/webhook/nowpayments/ipn', [NOWPaymentsController::class, 'handleWebhook'])->name('webhook.nowpayments.ipn');

// Admin Routes (Cohort Administrators)
Route::middleware(['auth', 'role:admin,platform_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [CohortController::class, 'adminDashboard'])->name('dashboard');
    
    // Member Management
    Route::get('/members', [CohortController::class, 'manageMembers'])->name('members');
    Route::get('/kyc', [KYCController::class, 'reviewKYC'])->name('kyc');
    Route::get('/kyc/{user}/review', [KYCController::class, 'showReview'])->name('kyc.review');
    Route::post('/kyc/{user}/approve', [KYCController::class, 'approveKYC'])->name('kyc.approve');
    Route::post('/kyc/{user}/reject', [KYCController::class, 'rejectKYC'])->name('kyc.reject');
    
    // Investments
    Route::get('/investments', [CohortController::class, 'manageInvestments'])->name('investments');
    Route::get('/investments/create', [CohortController::class, 'createInvestment'])->name('investments.create');
    
    // Distributions
    Route::get('/distributions', [DistributionController::class, 'index'])->name('distributions.index');
    Route::post('/distributions', [DistributionController::class, 'store'])->name('distributions.store');
    Route::get('/distributions/create', [DistributionController::class, 'create'])->name('distributions.create');
    Route::get('/distributions/{distribution}', [DistributionController::class, 'showDistribution'])->name('distributions.show');
    Route::post('/distributions/{distribution}/complete', [DistributionController::class, 'complete'])->name('distributions.complete');

    // Cohort Management
    Route::resource('cohorts', AdminCohortController::class);
    Route::post('/cohorts/{cohort}/submit-approval', [AdminCohortController::class, 'submitForApproval'])->name('cohorts.submit-approval');
    Route::post('/cohorts/{cohort}/activate-production', [AdminCohortController::class, 'activateProduction'])->name('cohorts.activate-production');
    Route::patch('/cohorts/{cohort}/change-status', [AdminCohortController::class, 'changeStatus'])->name('cohorts.change-status');
    
    // Timeline Management
    Route::post('/cohorts/{cohort}/timeline', [TimelineController::class, 'store'])->name('cohorts.timeline.store');
    Route::get('/cohorts/{cohort}/timeline', [TimelineController::class, 'index'])->name('cohorts.timeline.index');
    Route::delete('/cohorts/{cohort}/timeline/{timeline}', [TimelineController::class, 'destroy'])->name('cohorts.timeline.destroy');
    Route::post('/cohorts/{cohort}/transactions', [AdminCohortController::class, 'recordTransaction'])->name('cohorts.transactions.store');
    
    // Cohort Control Panel & Operations
    Route::get('/cohorts/{cohort}/control-panel', [AdminCohortController::class, 'controlPanel'])->name('cohorts.control-panel');
    Route::post('/cohorts/{cohort}/activate', [AdminCohortController::class, 'activate'])->name('cohorts.activate');
    Route::post('/cohorts/{cohort}/pause', [AdminCohortController::class, 'pause'])->name('cohorts.pause');
    Route::post('/cohorts/{cohort}/resume', [AdminCohortController::class, 'resume'])->name('cohorts.resume');
    Route::post('/cohorts/{cohort}/record-profit', [AdminCohortController::class, 'recordProfit'])->name('cohorts.record-profit');
    Route::post('/cohorts/{cohort}/distribute-profit', [AdminCohortController::class, 'distributeProfit'])->name('cohorts.distribute-profit');

    // Reports
    Route::get('/cohorts/{cohort}/reports/create', [AdminCohortController::class, 'createReport'])->name('cohorts.reports.create');
    Route::post('/cohorts/{cohort}/reports', [AdminCohortController::class, 'storeReport'])->name('cohorts.reports.store');

    // Distributions (Legacy)
    Route::get('/cohorts/{cohort}/distributions/create', [DistributionController::class, 'create'])->name('cohorts.distributions.create');
    Route::post('/cohorts/{cohort}/distributions', [DistributionController::class, 'store'])->name('cohorts.distributions.store');

    // Votes
    Route::get('/cohorts/{cohort}/votes/create', [VoteController::class, 'create'])->name('cohorts.votes.create');
    Route::post('/cohorts/{cohort}/votes', [VoteController::class, 'store'])->name('votes.store');
    Route::get('/cohorts/{cohort}/votes/{vote}', [VoteController::class, 'show'])->name('votes.show');
    Route::post('/cohorts/{cohort}/votes/{vote}/close', [VoteController::class, 'close'])->name('votes.close');

    // Profit Management
    Route::get('/cohorts/{cohort}/profits', [ProfitController::class, 'index'])->name('cohorts.profits.index');
    Route::get('/cohorts/{cohort}/profits/create', [ProfitController::class, 'create'])->name('cohorts.profits.create');
    Route::post('/cohorts/{cohort}/profits', [ProfitController::class, 'store'])->name('cohorts.profits.store');
    Route::post('/cohorts/{cohort}/profits/share-rate', [ProfitController::class, 'updateShareRate'])->name('cohorts.profits.share-rate');
    Route::post('/cohorts/{cohort}/profits/targets', [ProfitController::class, 'updateTargets'])->name('cohorts.profits.targets');
    Route::post('/cohorts/{cohort}/profits/credit-wallets', [ProfitController::class, 'creditToWallets'])->name('cohorts.profits.credit-wallets');
    Route::get('/cohorts/{cohort}/profits/members', [ProfitController::class, 'memberProfits'])->name('cohorts.profits.members');
    Route::post('/daily-profits/{dailyProfit}/distribute', [ProfitController::class, 'distribute'])->name('daily-profits.distribute');

    // Wallet Management
    Route::get('/wallet', [WalletAdminController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/deposits/{transaction}/approve', [WalletAdminController::class, 'approveDeposit'])->name('wallet.approve-deposit');
    Route::post('/wallet/deposits/{transaction}/reject', [WalletAdminController::class, 'rejectDeposit'])->name('wallet.reject-deposit');
    Route::post('/wallet/withdrawals/{transaction}/approve', [WalletAdminController::class, 'approveWithdrawal'])->name('wallet.approve-withdrawal');
    Route::post('/wallet/withdrawals/{transaction}/reject', [WalletAdminController::class, 'rejectWithdrawal'])->name('wallet.reject-withdrawal');
    Route::get('/wallet/cohort-funds', [WalletAdminController::class, 'cohortFunds'])->name('wallet.cohort-funds');
    Route::post('/wallet/cohort-funds/{cohortFund}/lock', [WalletAdminController::class, 'lockFund'])->name('wallet.lock-fund');
    Route::post('/wallet/cohort-funds/{cohortFund}/unlock', [WalletAdminController::class, 'unlockFund'])->name('wallet.unlock-fund');
    Route::post('/wallet/cohort-funds/{cohortFund}/maturity', [WalletAdminController::class, 'setMaturityDate'])->name('wallet.set-maturity');
    Route::get('/wallet/profit-settings', [WalletAdminController::class, 'profitSettings'])->name('wallet.profit-settings');
    Route::post('/wallet/profit-settings', [WalletAdminController::class, 'updateProfitSettings'])->name('wallet.update-profit-settings');
    Route::post('/wallet/apply-daily-profits', [WalletAdminController::class, 'applyDailyProfits'])->name('wallet.apply-daily-profits');
    Route::post('/wallet/auto-lock', [WalletAdminController::class, 'autoLockFunds'])->name('wallet.auto-lock');
});

// Platform Admin Routes (Super Admin)
Route::middleware(['auth', 'role:platform_admin'])->prefix('platform-admin')->name('platform-admin.')->group(function () {
    Route::get('/dashboard', [PlatformAdminController::class, 'dashboard'])->name('dashboard');
    
    // User Management
    Route::get('/users', [PlatformAdminController::class, 'manageUsers'])->name('users');
    Route::get('/kyc', [PlatformAdminController::class, 'reviewAllKYC'])->name('kyc');
    Route::get('/reports', [PlatformAdminController::class, 'reports'])->name('reports');
    Route::get('/settings', [PlatformAdminController::class, 'settings'])->name('settings');

    // Cohort Approvals (Legacy)
    Route::get('/cohorts/pending', [PlatformAdminController::class, 'pendingCohorts'])->name('cohorts.pending');
    Route::post('/cohorts/{cohort}/approve', [PlatformAdminController::class, 'approveCohort'])->name('cohorts.approve');
    Route::post('/cohorts/{cohort}/reject', [PlatformAdminController::class, 'rejectCohort'])->name('cohorts.reject');

    // KYC Approvals (Legacy - using different method names to avoid conflicts with admin routes)
    Route::get('/kyc/pending', [PlatformAdminController::class, 'pendingKYC'])->name('kyc.pending');
    Route::post('/kyc/{user}/approve', [PlatformAdminController::class, 'approveKYC'])->name('kyc.approve');
    Route::post('/kyc/{user}/reject', [PlatformAdminController::class, 'rejectKYC'])->name('kyc.reject');

    // User Management (Legacy)
    Route::post('/users/{user}/suspend', [PlatformAdminController::class, 'suspendUser'])->name('users.suspend');
    Route::post('/users/{user}/activate', [PlatformAdminController::class, 'activateUser'])->name('users.activate');

    // System Settings (Legacy)
    Route::post('/settings', [PlatformAdminController::class, 'updateSettings'])->name('settings.update');

    // Reports & Analytics
    Route::get('/analytics', [PlatformAdminController::class, 'analytics'])->name('analytics');
    Route::get('/activity-logs', [PlatformAdminController::class, 'activityLogs'])->name('activity-logs');
});

