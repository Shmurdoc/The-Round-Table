

<?php $__env->startSection('title', 'Portfolio - RoundTable'); ?>
<?php $__env->startSection('page-title', 'Portfolio'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8 slide-up">
    <!-- Hero Stats -->
    <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden group">
        <!-- Background Image -->
        <div class="absolute inset-0 opacity-10 group-hover:opacity-15 transition-opacity duration-1000">
            <img src="<?php echo e(asset('assets/img/showcase/inv7.jpg')); ?>" 
                 alt="Portfolio Background"
                 class="w-full h-full object-cover">
        </div>
        
        <!-- Background Effect Overlays -->
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-amber-500/20 rounded-full blur-3xl z-[1]"></div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl z-[1]"></div>
        
        <div class="relative z-10">
            <div class="flex items-center space-x-2 mb-2">
                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                <h2 class="text-slate-400 text-sm font-mono uppercase tracking-wider">Partnership Value</h2>
            </div>
            <div class="text-5xl md:text-6xl font-extrabold font-mono tracking-tighter mb-6">
                R <?php echo e(number_format($totalInvested / 100, 0)); ?><span class="text-slate-600 text-3xl">.<?php echo e(str_pad($totalInvested % 100, 2, '0', STR_PAD_LEFT)); ?></span>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white/10 backdrop-blur-md px-5 py-3 rounded-xl border border-white/10">
                    <div class="text-[10px] text-slate-300 uppercase font-bold mb-1">Active Cohorts</div>
                    <div class="font-bold text-xl flex items-center">
                        <i data-lucide="layers" class="w-4 h-4 mr-2 text-amber-400"></i>
                        <?php echo e($activeCohorts); ?>

                    </div>
                </div>
                <div class="bg-emerald-500/10 backdrop-blur-md px-5 py-3 rounded-xl border border-emerald-500/20">
                    <div class="text-[10px] text-emerald-300 uppercase font-bold mb-1">Total Returns</div>
                    <div class="font-bold text-xl text-emerald-400">R<?php echo e(number_format($totalReturns / 100, 0)); ?></div>
                </div>
                <div class="bg-white/10 backdrop-blur-md px-5 py-3 rounded-xl border border-white/10">
                    <div class="text-[10px] text-slate-300 uppercase font-bold mb-1">Return Rate</div>
                    <div class="font-bold text-xl <?php echo e($returnRate >= 0 ? 'text-emerald-400' : 'text-red-400'); ?>">
                        <?php echo e($returnRate >= 0 ? '+' : ''); ?><?php echo e(number_format($returnRate, 1)); ?>%
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-md px-5 py-3 rounded-xl border border-white/10">
                    <div class="text-[10px] text-slate-300 uppercase font-bold mb-1">KYC Status</div>
                    <div class="font-bold text-xl flex items-center">
                        <?php if(in_array(auth()->user()->kyc_status, ['approved', 'verified'])): ?>
                            <i data-lucide="shield-check" class="w-4 h-4 mr-2 text-emerald-400"></i>
                            <span class="text-emerald-400">Verified</span>
                        <?php else: ?>
                            <i data-lucide="shield-alert" class="w-4 h-4 mr-2 text-amber-400"></i>
                            <span class="text-amber-400">Pending</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Partnerships List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-slate-900 text-lg flex items-center">
                    <i data-lucide="briefcase" class="w-5 h-5 mr-2 text-amber-500"></i>
                    Your Contributions
                </h3>
                <a href="<?php echo e(route('cohorts.index')); ?>" class="text-sm font-bold text-amber-600 hover:text-amber-700 flex items-center space-x-1">
                    <span>Browse More</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
            
            <?php $__empty_1 = true; $__currentLoopData = $userCohorts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cohort): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $pivotData = $cohort->pivot ?? null;
                    $contributionAmount = $pivotData ? $pivotData->contribution_amount : 0;
                    $currentValue = $contributionAmount * (1 + ($cohort->actual_return_percent ?? 0) / 100);
                    $gain = $currentValue - $contributionAmount;
                    $gainPercent = $contributionAmount > 0 ? ($gain / $contributionAmount) * 100 : 0;
                ?>
                
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                            <div class="flex items-center space-x-4">
                                <div class="h-12 w-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 border border-slate-200">
                                    <?php if($cohort->cohort_class === 'lease'): ?>
                                        <i data-lucide="container" class="w-6 h-6"></i>
                                    <?php elseif($cohort->cohort_class === 'utilization'): ?>
                                        <i data-lucide="factory" class="w-6 h-6"></i>
                                    <?php else: ?>
                                        <i data-lucide="building-2" class="w-6 h-6"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-lg"><?php echo e($cohort->title); ?></h4>
                                    <div class="flex items-center space-x-2 mt-1">
                                        <span class="text-xs font-bold uppercase px-2 py-0.5 rounded
                                            <?php echo e($cohort->status === 'operational' ? 'bg-emerald-100 text-emerald-700' : 
                                               ($cohort->status === 'funding' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700')); ?>">
                                            <?php echo e(ucfirst($cohort->status)); ?>

                                        </span>
                                        <span class="text-xs text-slate-500">• <?php echo e(ucfirst($cohort->cohort_class ?? 'standard')); ?></span>
                                    </div>
                                </div>
                            </div>
                            <a href="<?php echo e(route('cohorts.show', $cohort)); ?>" 
                               class="text-sm font-bold text-amber-600 hover:text-amber-700 flex items-center space-x-1">
                                <span>View Details</span>
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-slate-100">
                            <div>
                                    <div class="text-[10px] text-slate-400 uppercase font-bold">Contributed</div>
                                    <div class="font-mono font-bold text-slate-900">R<?php echo e(number_format($contributionAmount / 100, 0)); ?></div>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase font-bold">Current Value</div>
                                <div class="font-mono font-bold text-slate-900">R<?php echo e(number_format($currentValue / 100, 0)); ?></div>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase font-bold">Gain/Loss</div>
                                <div class="font-mono font-bold <?php echo e($gain >= 0 ? 'text-emerald-600' : 'text-red-600'); ?>">
                                    <?php echo e($gain >= 0 ? '+' : ''); ?>R<?php echo e(number_format($gain / 100, 0)); ?>

                                </div>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-400 uppercase font-bold">Return</div>
                                <div class="font-mono font-bold <?php echo e($gainPercent >= 0 ? 'text-emerald-600' : 'text-red-600'); ?>">
                                    <?php echo e($gainPercent >= 0 ? '+' : ''); ?><?php echo e(number_format($gainPercent, 1)); ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="bg-white border border-slate-200 p-12 rounded-2xl text-center">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i data-lucide="wallet" class="w-10 h-10 text-slate-400"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 text-xl mb-2">No Contributions Yet</h4>
                    <p class="text-slate-500 mb-6 max-w-md mx-auto">Start contributing by joining cohorts. Browse available opportunities to get started.</p>
                    <a href="<?php echo e(route('cohorts.index')); ?>" class="inline-flex items-center space-x-2 px-6 py-3 bg-slate-900 text-white rounded-xl font-bold hover:bg-slate-800 transition-colors">
                        <span>Browse Cohorts</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Transaction History -->
        <div class="space-y-6">
            <h3 class="font-bold text-slate-900 text-lg flex items-center">
                <i data-lucide="history" class="w-5 h-5 mr-2 text-amber-500"></i>
                Recent Transactions
            </h3>
            
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $transactions->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-4 flex justify-between items-center hover:bg-slate-50 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                    <?php echo e(in_array($transaction->type, ['contribution', 'deposit']) ? 'bg-emerald-100' : 
                                       ($transaction->type === 'distribution' ? 'bg-blue-100' : 'bg-slate-100')); ?>">
                                    <?php if(in_array($transaction->type, ['contribution', 'deposit'])): ?>
                                        <i data-lucide="arrow-up-right" class="w-4 h-4 text-emerald-600"></i>
                                    <?php elseif($transaction->type === 'distribution'): ?>
                                        <i data-lucide="arrow-down-left" class="w-4 h-4 text-blue-600"></i>
                                    <?php else: ?>
                                        <i data-lucide="repeat" class="w-4 h-4 text-slate-600"></i>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-slate-900"><?php echo e(ucfirst($transaction->type)); ?></div>
                                    <div class="text-xs text-slate-500"><?php echo e($transaction->created_at->format('d M Y')); ?></div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-mono font-bold text-sm 
                                    <?php echo e(in_array($transaction->type, ['contribution', 'deposit', 'distribution']) ? 'text-emerald-600' : 'text-slate-900'); ?>">
                                    <?php echo e(in_array($transaction->type, ['contribution', 'deposit', 'distribution']) ? '+' : '-'); ?>R<?php echo e(number_format($transaction->amount / 100, 0)); ?>

                                </div>
                                <div class="text-[10px] text-slate-400 uppercase"><?php echo e($transaction->cohort->title ?? 'System'); ?></div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-8 text-center">
                            <i data-lucide="receipt" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                            <p class="text-sm text-slate-500">No transactions yet</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if($transactions->count() > 10): ?>
                    <div class="p-4 border-t border-slate-100 text-center">
                        <button class="text-sm font-bold text-slate-600 hover:text-slate-900">
                            View All Transactions
                        </button>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Quick Links -->
            <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-200/50 p-5 rounded-2xl">
                <h4 class="font-bold text-amber-800 text-sm mb-3">Account Actions</h4>
                <div class="space-y-2">
                    <a href="<?php echo e(route('kyc.form')); ?>" class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-amber-200/50 hover:border-amber-300 transition-colors group">
                        <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center">
                            <i data-lucide="user-check" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-amber-700">Update Profile</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto group-hover:text-amber-500"></i>
                    </a>
                    <a href="#" class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-amber-200/50 hover:border-amber-300 transition-colors group">
                        <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center">
                            <i data-lucide="download" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-amber-700">Download Statements</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto group-hover:text-amber-500"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.modern', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\The round table\The Table\resources\views/member/portfolio-modern.blade.php ENDPATH**/ ?>