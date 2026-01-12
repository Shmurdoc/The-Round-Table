

<?php $__env->startSection('title', 'Partner Account - RoundTable'); ?>
<?php $__env->startSection('page-title', 'Partner Account'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 slide-up">
    <!-- Main Balance Card -->
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
        <!-- Header with Tabs -->
        <div class="border-b border-slate-100">
            <div class="flex items-center space-x-8 px-6 pt-4">
                <button class="pb-4 text-sm font-bold text-slate-900 border-b-2 border-emerald-500">Balances</button>
                <button class="pb-4 text-sm font-medium text-slate-400 hover:text-slate-600 transition">
                    Operations
                    <?php if($transactions->where('status', 'pending')->count() > 0): ?>
                        <span class="ml-1.5 bg-slate-200 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full">
                            <?php echo e($transactions->where('status', 'pending')->count()); ?>

                        </span>
                    <?php endif; ?>
                </button>
                <a href="<?php echo e(route('wallet.transactions')); ?>" class="pb-4 text-sm font-medium text-slate-400 hover:text-slate-600 transition">Statement</a>
            </div>
        </div>

        <!-- Total Available Balance -->
        <div class="p-8">
            <p class="text-slate-500 text-sm mb-2">Total available balance</p>
            <div class="flex items-baseline mb-6">
                <span class="text-4xl md:text-5xl font-bold text-slate-900">
                    R<?php echo e(number_format($availableBalance / 100, 0)); ?>

                </span>
                <span class="text-2xl text-slate-400 ml-1">.<?php echo e(str_pad($availableBalance % 100, 2, '0', STR_PAD_LEFT)); ?></span>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-4">
                <a href="<?php echo e(route('wallet.deposit.form')); ?>" 
                   class="flex-1 py-4 px-6 bg-gradient-to-r from-emerald-400 to-emerald-500 text-white font-bold rounded-2xl text-center shadow-lg shadow-emerald-200 hover:shadow-xl hover:shadow-emerald-300 transition-all transform hover:scale-[1.02]">
                    <i data-lucide="download" class="w-5 h-5 inline-block mr-2"></i>
                    Deposit
                </a>
                <a href="<?php echo e(route('wallet.withdraw.form')); ?>" 
                   class="flex-1 py-4 px-6 bg-white text-slate-700 font-bold rounded-2xl text-center border-2 border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition-all">
                    <i data-lucide="upload" class="w-5 h-5 inline-block mr-2"></i>
                    Withdraw
                </a>
            </div>
        </div>
    </div>

    <!-- Main Account Card -->
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="wallet" class="w-6 h-6 text-slate-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900">Main Account</h3>
                    <p class="text-sm text-slate-400">Nº <?php echo e($wallet->wallet_id); ?></p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-400">Opened</p>
                <p class="text-sm text-slate-600"><?php echo e($wallet->created_at->format('d.m.Y')); ?></p>
            </div>
        </div>

        <!-- Portfolio Valuation -->
        <div class="mb-6">
            <div class="flex items-baseline mb-2">
                <span class="text-3xl font-bold text-slate-900">R<?php echo e(number_format($totalPortfolioValue / 100, 0)); ?></span>
                <span class="text-xl text-slate-400 ml-1">.<?php echo e(str_pad($totalPortfolioValue % 100, 2, '0', STR_PAD_LEFT)); ?></span>
            </div>
            <p class="text-sm text-slate-500">Portfolio Valuation</p>
        </div>

        <!-- Progress Bar -->
        <?php
            $cohortPercentage = $totalPortfolioValue > 0 ? ($totalInCohorts / $totalPortfolioValue) * 100 : 0;
            $walletPercentage = 100 - $cohortPercentage;
        ?>
        <div class="h-3 bg-slate-100 rounded-full overflow-hidden mb-4">
            <div class="h-full flex">
                <div class="bg-purple-500 transition-all" style="width: <?php echo e($cohortPercentage); ?>%"></div>
                <div class="bg-emerald-400" style="width: <?php echo e($walletPercentage); ?>%"></div>
            </div>
        </div>

        <!-- Legend -->
        <div class="flex justify-between text-sm">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full bg-purple-500"></div>
                <span class="text-slate-500">Current partnerships</span>
                <span class="font-bold text-slate-900">R<?php echo e(number_format($totalInCohorts / 100, 2)); ?></span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 rounded-full bg-emerald-400"></div>
                <span class="text-slate-500">Available balance</span>
                <span class="font-bold text-emerald-500">R<?php echo e(number_format($availableBalance / 100, 2)); ?></span>
            </div>
        </div>
    </div>

    <!-- Active Cohort Partnerships -->
    <?php if($cohortFunds->count() > 0): ?>
    <div class="space-y-4">
        <h3 class="font-bold text-slate-900 text-lg px-1">Active Partnerships</h3>
        
        <?php $__currentLoopData = $cohortFunds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg
                        <?php echo e($fund->cohort ? 'bg-gradient-to-br from-rose-400 to-rose-500' : 'bg-slate-400'); ?>">
                        <?php echo e($fund->cohort ? substr($fund->cohort->name, 0, 2) : 'XX'); ?>

                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900"><?php echo e($fund->cohort->name ?? 'Unknown Cohort'); ?></h4>
                        <p class="text-sm text-slate-400">Nº <?php echo e($fund->fund_id); ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400">Joined</p>
                    <p class="text-sm text-slate-600"><?php echo e($fund->created_at->format('d.m.Y')); ?></p>
                </div>
            </div>

            <!-- Fund Details -->
            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <p class="text-xs text-slate-400 mb-1">Principal</p>
                    <p class="font-bold text-slate-900"><?php echo e($fund->formatted_principal); ?></p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Current Value</p>
                    <p class="font-bold text-emerald-600"><?php echo e($fund->formatted_current_value); ?></p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 mb-1">Earnings</p>
                    <p class="font-bold text-emerald-600">+<?php echo e($fund->formatted_earnings); ?></p>
                </div>
            </div>

            <!-- Status & Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <div class="flex items-center space-x-2">
                    <?php if($fund->is_locked): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                            <i data-lucide="lock" class="w-3 h-3 mr-1"></i>
                            Locked
                        </span>
                        <?php if($fund->maturity_date): ?>
                            <span class="text-xs text-slate-400">Until <?php echo e($fund->maturity_date->format('d M Y')); ?></span>
                        <?php endif; ?>
                    <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                            <i data-lucide="unlock" class="w-3 h-3 mr-1"></i>
                            Unlocked
                        </span>
                        <?php if($fund->auto_lock_at && $fund->auto_lock_at->isFuture()): ?>
                            <span class="text-xs text-slate-400">Auto-locks <?php echo e($fund->auto_lock_at->diffForHumans()); ?></span>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <?php if($fund->canWithdraw()): ?>
                    <form action="<?php echo e(route('wallet.cohort.withdraw', $fund)); ?>" method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="text-sm font-bold text-rose-600 hover:text-rose-700">
                            Withdraw to Wallet
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h3 class="font-bold text-slate-900">Recent Transactions</h3>
            <a href="<?php echo e(route('wallet.transactions')); ?>" class="text-sm font-bold text-amber-600 hover:text-amber-700">View All</a>
        </div>
        
        <div class="divide-y divide-slate-50">
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="p-4 flex items-center justify-between hover:bg-slate-50 transition-colors">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center
                        <?php echo e(in_array($txn->type, ['deposit', 'transfer_from_cohort', 'profit', 'refund']) ? 'bg-emerald-100' : 'bg-amber-100'); ?>">
                        <i data-lucide="<?php echo e($txn->type_icon); ?>" class="w-5 h-5 
                            <?php echo e(in_array($txn->type, ['deposit', 'transfer_from_cohort', 'profit', 'refund']) ? 'text-emerald-600' : 'text-amber-600'); ?>"></i>
                    </div>
                    <div>
                        <p class="font-medium text-slate-900"><?php echo e(ucfirst(str_replace('_', ' ', $txn->type))); ?></p>
                        <p class="text-xs text-slate-400"><?php echo e($txn->created_at->format('d M Y, H:i')); ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold <?php echo e(in_array($txn->type, ['deposit', 'transfer_from_cohort', 'profit', 'refund']) ? 'text-emerald-600' : 'text-slate-900'); ?>">
                        <?php echo e($txn->formatted_amount); ?>

                    </p>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full <?php echo e($txn->status_badge); ?>">
                        <?php echo e(ucfirst($txn->status)); ?>

                    </span>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="receipt" class="w-8 h-8 text-slate-400"></i>
                </div>
                <p class="text-slate-500">No transactions yet</p>
                <a href="<?php echo e(route('wallet.deposit.form')); ?>" class="inline-block mt-4 text-sm font-bold text-amber-600 hover:text-amber-700">
                    Make your first deposit
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-4">
        <a href="<?php echo e(route('cohorts.index')); ?>" class="bg-gradient-to-br from-slate-800 to-slate-900 text-white rounded-2xl p-6 hover:from-slate-700 hover:to-slate-800 transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-lg mb-1">Browse Cohorts</h4>
                    <p class="text-slate-400 text-sm">Find partnership opportunities</p>
                </div>
                <i data-lucide="arrow-right" class="w-6 h-6 text-slate-400 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>
        <a href="<?php echo e(route('member.portfolio')); ?>" class="bg-gradient-to-br from-amber-400 to-amber-500 text-white rounded-2xl p-6 hover:from-amber-500 hover:to-amber-600 transition-all group">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-lg mb-1">My Portfolio</h4>
                    <p class="text-amber-100 text-sm">View all partnerships</p>
                </div>
                <i data-lucide="arrow-right" class="w-6 h-6 text-amber-100 group-hover:translate-x-1 transition-transform"></i>
            </div>
        </a>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.modern', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\The round table\The Table\resources\views/member/wallet/index.blade.php ENDPATH**/ ?>