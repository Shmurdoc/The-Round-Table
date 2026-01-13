<?php $__env->startSection('title', 'Dashboard - RoundTable'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-8 slide-up">
    <!-- Hero Stats -->
    <div class="bg-slate-900 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden group">
        <!-- Background Image -->
          <div class="absolute inset-0 opacity-10 group-hover:opacity-15 transition-opacity duration-1000">
          <img src="<?php echo e(Storage::url('cohorts/images/inv5.jpg')); ?>" 
              alt="Dashboard Background"
              class="w-full h-full object-cover">
       </div>
        
        <!-- Background Effect Overlays -->
        <div class="absolute -right-20 -top-20 w-96 h-96 bg-amber-500/20 rounded-full blur-3xl group-hover:bg-amber-500/30 transition-all duration-1000 z-[1]"></div>
        <div class="absolute -left-20 -bottom-20 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl z-[1]"></div>
        
        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-3 gap-8 items-end">
            <div class="lg:col-span-2">
                <div class="flex items-center space-x-2 mb-2">
                    <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                    <h2 class="text-slate-400 text-sm font-mono uppercase tracking-wider">Total Active Deployment</h2>
                </div>
                <div class="text-5xl md:text-6xl font-extrabold font-mono tracking-tighter mb-6">
                    R <?php echo e(number_format($totalInvested / 100, 0)); ?><span class="text-slate-600 text-3xl">.<?php echo e(str_pad($totalInvested % 100, 2, '0', STR_PAD_LEFT)); ?></span>
                </div>
                
                <div class="flex flex-wrap gap-4">
                    <div class="bg-white/10 backdrop-blur-md px-5 py-3 rounded-xl border border-white/10 hover:bg-white/20 transition-colors">
                        <div class="text-[10px] text-slate-300 uppercase font-bold mb-1">Active Cohorts</div>
                        <div class="font-bold text-xl flex items-center">
                            <i data-lucide="layers" class="w-4 h-4 mr-2 text-amber-400"></i>
                            <?php echo e($activeCohorts); ?>

                        </div>
                    </div>
                    <div class="bg-emerald-500/10 backdrop-blur-md px-5 py-3 rounded-xl border border-emerald-500/20">
                        <div class="text-[10px] text-emerald-300 uppercase font-bold mb-1">Weighted Yield</div>
                        <div class="font-bold text-xl text-emerald-400">+<?php echo e(number_format($returnRate, 1)); ?>%</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md px-5 py-3 rounded-xl border border-white/10 hover:bg-white/20 transition-colors">
                        <div class="text-[10px] text-slate-300 uppercase font-bold mb-1">Pending Payouts</div>
                        <div class="font-bold text-xl flex items-center">
                            <i data-lucide="clock" class="w-4 h-4 mr-2 text-amber-400"></i>
                            R <?php echo e(number_format($pendingDistributions / 100, 0)); ?>

                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mini Ledger -->
            <div class="bg-slate-800/50 backdrop-blur-md rounded-2xl p-4 border border-slate-700/50">
                <h4 class="text-xs font-bold text-slate-400 uppercase mb-3 border-b border-slate-700 pb-2">Live Ledger</h4>
                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $recentTransactions->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex justify-between items-center text-sm">
                            <div class="flex flex-col">
                                <span class="text-slate-200 font-medium"><?php echo e(ucfirst($transaction->type)); ?></span>
                                <span class="text-[10px] text-slate-500"><?php echo e($transaction->created_at->format('h:i A')); ?> • <?php echo e($transaction->cohort->title ?? 'System'); ?></span>
                            </div>
                            <span class="font-mono <?php echo e($transaction->type === 'deposit' || $transaction->type === 'distribution' ? 'text-emerald-400' : 'text-slate-300'); ?>">
                                <?php echo e($transaction->type === 'deposit' || $transaction->type === 'distribution' ? '+' : '-'); ?>R<?php echo e(number_format($transaction->amount / 100, 0)); ?>

                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-sm text-slate-500 text-center py-4">No recent activity</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Votes Section -->
    <?php
        $activeVotes = \App\Models\Vote::whereHas('cohort.members', function($q) {
            $q->where('user_id', auth()->id());
        })
        ->where('status', 'active')
        ->where('voting_ends_at', '>', now())
        ->with('cohort')
        ->get();
        
        $userVotedOn = $activeVotes->filter(function($vote) {
            return $vote->hasUserVoted(auth()->id());
        })->pluck('id');
    ?>

    <?php if($activeVotes->count() > 0): ?>
    <div class="bg-gradient-to-br from-purple-50 to-indigo-50 border-2 border-purple-200 rounded-2xl p-6 shadow-lg animate-pulse-subtle">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i data-lucide="vote" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="font-bold text-purple-900 text-lg">Active Votes Require Your Input</h3>
                    <p class="text-sm text-purple-600">Your partnership decisions are important</p>
                </div>
            </div>
            <span class="bg-purple-600 text-white px-4 py-2 rounded-full text-lg font-bold shadow-lg">
                <?php echo e($activeVotes->count()); ?>

            </span>
        </div>
        
        <div class="space-y-3">
            <?php $__currentLoopData = $activeVotes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vote): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $hasVoted = $userVotedOn->contains($vote->id);
                    $participation = $vote->cohort->members()->count() > 0 
                        ? ($vote->responses()->count() / $vote->cohort->members()->count()) * 100 
                        : 0;
                ?>
                <div class="bg-white rounded-xl p-5 border <?php echo e($hasVoted ? 'border-emerald-200 bg-emerald-50/50' : 'border-purple-200'); ?> hover:border-purple-400 transition-all shadow-sm hover:shadow-md">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-1">
                                <div class="font-bold text-slate-900 text-lg"><?php echo e($vote->title); ?></div>
                                <?php if($hasVoted): ?>
                                    <span class="bg-emerald-500 text-white px-2 py-0.5 rounded-full text-xs font-bold">
                                        ✓ Voted
                                    </span>
                                <?php else: ?>
                                    <span class="bg-red-500 text-white px-2 py-0.5 rounded-full text-xs font-bold animate-pulse">
                                        Pending
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="text-sm text-slate-600 mb-3">
                                <i data-lucide="briefcase" class="w-3 h-3 inline mr-1"></i>
                                <?php echo e($vote->cohort->title); ?>

                            </div>
                            <div class="flex items-center space-x-4 text-xs text-slate-500">
                                <span class="flex items-center">
                                    <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                                    Ends <?php echo e($vote->deadline->diffForHumans()); ?>

                                </span>
                                <span class="flex items-center">
                                    <i data-lucide="users" class="w-3 h-3 mr-1"></i>
                                    <?php echo e($vote->responses()->count()); ?>/<?php echo e($vote->cohort->members()->count()); ?> voted (<?php echo e(number_format($participation, 0)); ?>%)
                                </span>
                            </div>
                            <!-- Progress Bar -->
                            <div class="mt-3">
                                <div class="w-full bg-slate-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full transition-all" style="width: <?php echo e($participation); ?>%"></div>
                                </div>
                            </div>
                        </div>
                        <a href="<?php echo e(route('member.votes.show', [$vote->cohort, $vote])); ?>" 
                           class="ml-4 px-5 py-3 <?php echo e($hasVoted ? 'bg-slate-500' : 'bg-purple-600'); ?> text-white rounded-xl text-sm font-bold hover:<?php echo e($hasVoted ? 'bg-slate-600' : 'bg-purple-700'); ?> transition-colors flex items-center space-x-2 shadow-lg">
                            <span><?php echo e($hasVoted ? 'View Results' : 'Cast Vote Now'); ?></span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Active Seats Column -->
        <div class="lg:col-span-2 space-y-4">
            <h3 class="font-bold text-slate-900 text-lg flex items-center">
                <i data-lucide="armchair" class="w-5 h-5 mr-2 text-amber-500"></i>
                Your Active Partnerships
            </h3>
            
            <?php $__empty_1 = true; $__currentLoopData = $userCohorts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cohort): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $pivotData = $cohort->pivot ?? null;
                    $contributionAmount = $pivotData ? $pivotData->contribution_amount : ($cohort->min_contribution ?? 0);
                    $currentValue = $contributionAmount * (1 + ($cohort->actual_return_percent ?? 0) / 100);
                ?>
                <a href="<?php echo e(route('cohorts.show', $cohort)); ?>" 
                   class="bg-white border border-slate-200 p-5 rounded-2xl flex flex-col md:flex-row justify-between items-center shadow-sm hover:shadow-md transition-shadow group block">
                    <div class="flex items-center space-x-5 w-full md:w-auto mb-4 md:mb-0">
                        <div class="h-12 w-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-600 border border-slate-100 group-hover:border-amber-200 group-hover:bg-amber-50 transition-colors">
                            <?php if($cohort->cohort_class === 'lease'): ?>
                                <i data-lucide="container" class="w-6 h-6"></i>
                            <?php elseif($cohort->cohort_class === 'utilization'): ?>
                                <i data-lucide="factory" class="w-6 h-6"></i>
                            <?php else: ?>
                                <i data-lucide="building-2" class="w-6 h-6"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <div class="font-bold text-slate-900 text-lg"><?php echo e($cohort->title); ?></div>
                                <span class="bg-<?php echo e($cohort->status === 'operational' ? 'emerald' : 'amber'); ?>-100 text-<?php echo e($cohort->status === 'operational' ? 'emerald' : 'amber'); ?>-700 text-[10px] font-bold px-2 py-0.5 rounded uppercase">
                                    <?php echo e($cohort->status === 'operational' ? 'Running' : ucfirst($cohort->status)); ?>

                                </span>
                            </div>
                            <div class="text-xs text-slate-500 font-mono mt-1">
                                Joined: <?php echo e($pivotData ? \Carbon\Carbon::parse($pivotData->joined_at)->format('d M Y') : 'N/A'); ?>

                                <?php if($cohort->expected_exit_date): ?>
                                    • Exit: <?php echo e($cohort->expected_exit_date->format('d M Y')); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-8 w-full md:w-auto justify-between md:justify-end border-t md:border-t-0 border-slate-100 pt-4 md:pt-0">
                        <div class="text-right">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Invested</div>
                            <div class="font-mono font-bold text-slate-900">R<?php echo e(number_format($contributionAmount / 100, 0)); ?></div>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] text-slate-400 uppercase font-bold">Current Value</div>
                            <div class="font-mono font-bold text-emerald-600">R<?php echo e(number_format($currentValue / 100, 0)); ?></div>
                        </div>
                        <i data-lucide="chevron-right" class="w-5 h-5 text-slate-300 group-hover:text-amber-500 transition-colors"></i>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="bg-white border border-slate-200 p-8 rounded-2xl text-center">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="armchair" class="w-8 h-8 text-slate-400"></i>
                    </div>
                    <h4 class="font-bold text-slate-900 mb-2">No Active Seats</h4>
                    <p class="text-sm text-slate-500 mb-4">You haven't joined any cohorts yet. Browse available opportunities to get started.</p>
                    <a href="<?php echo e(route('cohorts.index')); ?>" class="inline-flex items-center space-x-2 px-4 py-2 bg-slate-900 text-white rounded-lg text-sm font-bold hover:bg-slate-800 transition-colors">
                        <span>Browse Cohorts</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Messages / Notifications -->
        <div class="space-y-4">
            <h3 class="font-bold text-slate-900 text-lg">System Notices</h3>
            <div class="bg-white border border-slate-200 p-5 rounded-2xl shadow-sm space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $notifications->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-start space-x-3 <?php echo e(!$loop->last ? 'pb-4 border-b border-slate-100' : ''); ?>">
                        <i data-lucide="<?php echo e($notification->type === 'info' ? 'info' : ($notification->type === 'warning' ? 'alert-triangle' : 'check-circle')); ?>" 
                           class="w-5 h-5 text-<?php echo e($notification->type === 'info' ? 'blue' : ($notification->type === 'warning' ? 'amber' : 'emerald')); ?>-500 mt-0.5"></i>
                        <div>
                            <p class="text-sm text-slate-800 font-medium"><?php echo e($notification->title); ?></p>
                            <p class="text-xs text-slate-500 mt-1"><?php echo e(Str::limit($notification->message, 80)); ?></p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-4">
                        <i data-lucide="bell-off" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                        <p class="text-sm text-slate-500">No new notifications</p>
                    </div>
                <?php endif; ?>
                
                <?php if($notifications->count() > 3): ?>
                    <a href="<?php echo e(route('member.notifications')); ?>" class="block w-full text-center text-xs font-bold text-slate-500 hover:text-slate-900 pt-2 border-t border-slate-100">
                        View All Updates
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 border border-amber-200/50 p-5 rounded-2xl">
                <h4 class="font-bold text-amber-800 text-sm mb-3">Quick Actions</h4>
                <div class="space-y-2">
                    <a href="<?php echo e(route('wallet.index')); ?>" class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-amber-200/50 hover:border-amber-300 transition-colors group">
                        <div class="w-8 h-8 bg-emerald-500/10 rounded-lg flex items-center justify-center">
                            <i data-lucide="wallet" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-emerald-700">My Wallet</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto group-hover:text-emerald-500"></i>
                    </a>
                    <a href="<?php echo e(route('wallet.deposit.form')); ?>" class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-amber-200/50 hover:border-amber-300 transition-colors group">
                        <div class="w-8 h-8 bg-blue-500/10 rounded-lg flex items-center justify-center">
                            <i data-lucide="plus-circle" class="w-4 h-4 text-blue-600"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-blue-700">Deposit Funds</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto group-hover:text-blue-500"></i>
                    </a>
                    <a href="<?php echo e(route('wallet.withdraw.form')); ?>" class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-amber-200/50 hover:border-amber-300 transition-colors group">
                        <div class="w-8 h-8 bg-purple-500/10 rounded-lg flex items-center justify-center">
                            <i data-lucide="arrow-up-circle" class="w-4 h-4 text-purple-600"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-purple-700">Withdraw Funds</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto group-hover:text-purple-500"></i>
                    </a>
                    <a href="<?php echo e(route('cohorts.index')); ?>" class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-amber-200/50 hover:border-amber-300 transition-colors group">
                        <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center">
                            <i data-lucide="search" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-amber-700">Browse Cohorts</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto group-hover:text-amber-500"></i>
                    </a>
                    <a href="<?php echo e(route('kyc.form')); ?>" class="flex items-center space-x-3 p-3 bg-white rounded-xl border border-amber-200/50 hover:border-amber-300 transition-colors group">
                        <div class="w-8 h-8 bg-amber-500/10 rounded-lg flex items-center justify-center">
                            <i data-lucide="shield-check" class="w-4 h-4 text-amber-600"></i>
                        </div>
                        <span class="text-sm font-medium text-slate-700 group-hover:text-amber-700">Complete KYC</span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300 ml-auto group-hover:text-amber-500"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.modern', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\The round table\The Table\resources\views/member/dashboard-modern.blade.php ENDPATH**/ ?>