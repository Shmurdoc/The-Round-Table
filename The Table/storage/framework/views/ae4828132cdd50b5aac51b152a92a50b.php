

<?php $__env->startSection('title', $cohort->title ?? $cohort->name . ' - RoundTable'); ?>
<?php $__env->startSection('page-title', 'Cohort Details'); ?>

<?php $__env->startSection('content'); ?>
<div class="slide-up space-y-6">
    <!-- Featured Image Hero -->
    <?php if($cohort->featured_image): ?>
    <div class="relative h-64 rounded-3xl overflow-hidden shadow-2xl border-4 border-slate-900/10">
        <img src="<?php echo e(asset('storage/' . $cohort->featured_image)); ?>" 
             alt="<?php echo e($cohort->title ?? $cohort->name); ?>"
             class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 p-8 z-10">
            <div class="flex flex-wrap items-center gap-2 mb-3">
                <span class="text-[10px] font-bold uppercase px-3 py-1.5 rounded-lg backdrop-blur-md
                    <?php echo e($cohort->status === 'operational' ? 'bg-emerald-500/30 text-emerald-100 border border-emerald-400/50' : 
                       ($cohort->status === 'funding' ? 'bg-amber-500/30 text-amber-100 border border-amber-400/50' : 
                       ($cohort->status === 'pending_approval' ? 'bg-blue-500/30 text-blue-100 border border-blue-400/50' : 'bg-slate-500/30 text-slate-100 border border-slate-400/50'))); ?>">
                    <?php echo e(str_replace('_', ' ', $cohort->status)); ?>

                </span>
                <span class="text-xs text-white/80 font-semibold px-3 py-1.5 bg-white/10 rounded-lg backdrop-blur-md border border-white/20">
                    <i data-lucide="clock" class="w-3 h-3 inline-block mr-1"></i>
                    <?php echo e($cohort->duration_months ?? 6); ?> Months
                </span>
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-white drop-shadow-lg"><?php echo e($cohort->title ?? $cohort->name); ?></h1>
        </div>
    </div>
    <?php endif; ?>

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <?php if(!$cohort->featured_image): ?>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="layers" class="w-5 h-5 text-purple-600"></i>
                </div>
                <?php echo e($cohort->title ?? $cohort->name); ?>

            </h1>
            <p class="text-slate-500 text-sm mt-2">
                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded
                    <?php echo e($cohort->status === 'operational' ? 'bg-emerald-100 text-emerald-700' : 
                       ($cohort->status === 'funding' ? 'bg-amber-100 text-amber-700' : 
                       ($cohort->status === 'pending_approval' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700'))); ?>">
                    <?php echo e(str_replace('_', ' ', $cohort->status)); ?>

                </span>
            </p>
            <?php else: ?>
            <h2 class="text-2xl font-bold text-slate-900">Cohort Management</h2>
            <p class="text-slate-500 text-sm mt-1">View and manage cohort details, members, and operations</p>
            <?php endif; ?>
        </div>
        <div class="flex gap-3">
            <a href="<?php echo e(route('admin.cohorts.index')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back
            </a>
            <a href="<?php echo e(route('admin.cohorts.edit', $cohort)); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-700 text-sm font-bold rounded-xl hover:bg-amber-200 transition">
                <i data-lucide="edit" class="w-4 h-4"></i>
                Edit
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Members</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono"><?php echo e($stats['total_members'] ?? $cohort->members_count ?? 0); ?></div>
            <div class="text-xs text-slate-500">Active partners</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Capital</span>
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="wallet" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R<?php echo e(number_format(($cohort->current_capital ?? 0) / 100, 0)); ?></div>
            <div class="text-xs text-slate-500">Total pooled</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Contribution</span>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="credit-card" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R<?php echo e(number_format(($cohort->contribution_amount ?? 0) / 100, 0)); ?></div>
            <div class="text-xs text-slate-500">Per member</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Distributions</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="banknote" class="w-4 h-4 text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono"><?php echo e($stats['distributions_count'] ?? 0); ?></div>
            <div class="text-xs text-slate-500">Total payouts</div>
        </div>
    </div>

    <!-- Production Mode Activation -->
    <?php if(!$cohort->production_mode): ?>
    
    <!-- Status Management -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50">
            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="settings" class="w-5 h-5 text-slate-500"></i>
                Cohort Status Management
            </h3>
        </div>
        <div class="p-6">
            <div class="mb-6">
                <div class="text-sm text-slate-600 mb-4">
                    <strong>Current Status:</strong> 
                    <span class="px-3 py-1 text-xs font-bold rounded-full <?php echo e($cohort->status === 'draft' ? 'bg-slate-100 text-slate-700' : 
                        ($cohort->status === 'funding' ? 'bg-purple-100 text-purple-700' : 
                        ($cohort->status === 'operational' ? 'bg-emerald-100 text-emerald-700' : 
                        ($cohort->status === 'paused' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-600')))); ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $cohort->status))); ?>

                    </span>
                </div>
                
                <div class="space-y-3 text-sm text-slate-600 bg-slate-50 p-4 rounded-xl">
                    <p><strong>Status Guide:</strong></p>
                    <ul class="space-y-2 ml-4">
                        <li><strong class="text-slate-700">Draft:</strong> Cohort is being prepared. Not visible to members.</li>
                        <li><strong class="text-purple-700">Funding:</strong> Cohort is open for member contributions. Members can join.</li>
                        <li><strong class="text-emerald-700">Operational:</strong> Cohort is active and generating profits.</li>
                        <li><strong class="text-red-700">Paused:</strong> Cohort operations are temporarily paused.</li>
                        <li><strong class="text-slate-700">Completed:</strong> Cohort has finished its lifecycle.</li>
                    </ul>
                </div>
            </div>

            <form action="<?php echo e(route('admin.cohorts.change-status', $cohort)); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Change Status To:</label>
                    <select name="status" required class="w-full px-4 py-3 border border-slate-200 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="draft" <?php echo e($cohort->status === 'draft' ? 'selected' : ''); ?>>Draft (Private)</option>
                        <option value="funding" <?php echo e($cohort->status === 'funding' ? 'selected' : ''); ?>>Funding (Open for Members)</option>
                        <option value="operational" <?php echo e($cohort->status === 'operational' ? 'selected' : ''); ?>>Operational (Active)</option>
                        <option value="paused" <?php echo e($cohort->status === 'paused' ? 'selected' : ''); ?>>Paused</option>
                        <option value="completed" <?php echo e($cohort->status === 'completed' ? 'selected' : ''); ?>>Completed</option>
                    </select>
                </div>

                <button type="submit" class="w-full px-6 py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition flex items-center justify-center gap-2">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                    Update Status
                </button>
            </form>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-amber-200 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="rocket" class="w-6 h-6 text-amber-700"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-amber-900 mb-1">Activate Production Mode</h3>
                <p class="text-sm text-amber-700 mb-4">This partnership is ready to begin operations. Activate production mode to start the weekly timeline and profit distribution system.</p>
                <form action="<?php echo e(route('admin.cohorts.activate-production', $cohort)); ?>" method="POST" onsubmit="return confirm('Are you sure you want to activate production mode? This will notify all partners and begin weekly tracking.');">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-600 text-white text-sm font-bold rounded-xl hover:bg-amber-700 transition shadow-sm">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Activate Production
                    </button>
                </form>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-2xl p-6">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-emerald-200 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="check-circle-2" class="w-6 h-6 text-emerald-700"></i>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-emerald-900 mb-1">Production Mode Active</h3>
                <p class="text-sm text-emerald-700">Activated <?php echo e($cohort->production_activated_at->format('F d, Y \a\t g:i A')); ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Timeline Form & Display -->
    <?php if($cohort->production_mode): ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Post Timeline Update -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden sticky top-6">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="calendar-plus" class="w-4 h-4 text-purple-600"></i>
                        Post Update
                    </h3>
                </div>
                <form action="<?php echo e(route('admin.cohorts.timeline.store', $cohort)); ?>" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                    <?php echo csrf_field(); ?>
                    
                    <div>
                        <label class="text-xs font-bold text-slate-700 mb-1 block">Event Type</label>
                        <select name="event_type" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="progress">📊 Progress Update</option>
                            <option value="profit">💰 Profit Recording</option>
                            <option value="milestone">🎯 Milestone Achieved</option>
                            <option value="update">📝 General Update</option>
                            <option value="meeting">👥 Meeting Notes</option>
                            <option value="achievement">🏆 Achievement</option>
                            <option value="alert">⚠️ Important Alert</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 mb-1 block">Title</label>
                        <input type="text" name="title" required maxlength="100" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Brief update title">
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 mb-1 block">Description</label>
                        <textarea name="description" rows="4" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Detailed update..."></textarea>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 mb-1 block">Event Date</label>
                        <input type="date" name="event_date" value="<?php echo e(date('Y-m-d')); ?>" required class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>

                    <div id="profit-amount-field" style="display: none;">
                        <label class="text-xs font-bold text-slate-700 mb-1 block">Profit Amount (R)</label>
                        <input type="number" name="profit_amount" step="0.01" min="0" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="0.00">
                        <p class="text-xs text-slate-500 mt-1">For weekly profit distribution</p>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-700 mb-1 block">Proof Document (optional)</label>
                        <input type="file" name="proof_document" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <p class="text-xs text-slate-500 mt-1">PDF, JPG, PNG only</p>
                    </div>

                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-purple-600 text-white text-sm font-bold rounded-xl hover:bg-purple-700 transition shadow-sm">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Post Update
                    </button>
                </form>
            </div>
        </div>

        <!-- Timeline Feed -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="timeline" class="w-4 h-4 text-purple-600"></i>
                        Partnership Timeline
                    </h3>
                </div>
                <div class="divide-y divide-slate-100 max-h-[600px] overflow-y-auto">
                    <?php $__empty_1 = true; $__currentLoopData = $cohort->timelines()->latest('event_date')->latest('created_at')->limit(20)->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $timeline): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-5 hover:bg-slate-50 transition-colors">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 <?php echo e($timeline->event_type_color); ?> rounded-xl flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="<?php echo e($timeline->event_type_icon); ?>" class="w-5 h-5 text-white"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4 mb-2">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-slate-900 text-sm mb-1"><?php echo e($timeline->title); ?></h4>
                                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                                <span><?php echo e($timeline->event_date->format('M d, Y')); ?></span>
                                                <span>•</span>
                                                <span><?php echo e($timeline->created_at->diffForHumans()); ?></span>
                                                <?php if($timeline->profit_amount > 0): ?>
                                                    <span>•</span>
                                                    <span class="font-bold text-emerald-600">R<?php echo e(number_format($timeline->profit_amount / 100, 2)); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-bold uppercase px-2 py-1 rounded <?php echo e($timeline->event_type === 'profit' ? 'bg-emerald-100 text-emerald-700' : ($timeline->event_type === 'alert' ? 'bg-red-100 text-red-700' : 'bg-slate-100 text-slate-700')); ?>">
                                            <?php echo e(str_replace('_', ' ', $timeline->event_type)); ?>

                                        </span>
                                    </div>
                                    <p class="text-sm text-slate-600 mb-3"><?php echo e($timeline->description); ?></p>
                                    <?php if($timeline->proof_document): ?>
                                        <a href="<?php echo e(Storage::url($timeline->proof_document)); ?>" target="_blank" class="inline-flex items-center gap-2 text-xs text-purple-600 hover:text-purple-700 font-bold">
                                            <i data-lucide="paperclip" class="w-3 h-3"></i>
                                            View Proof Document
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <form action="<?php echo e(route('admin.cohorts.timeline.destroy', [$cohort, $timeline])); ?>" method="POST" onsubmit="return confirm('Delete this timeline entry?');" class="flex-shrink-0">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="p-2 text-slate-400 hover:text-red-600 rounded-lg hover:bg-red-50 transition">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-12 text-center">
                            <i data-lucide="calendar-x" class="w-12 h-12 text-slate-300 mx-auto mb-3"></i>
                            <p class="text-sm text-slate-500 font-bold mb-1">No timeline entries yet</p>
                            <p class="text-xs text-slate-400">Post your first update to get started</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Cohort Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Cohort Info</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Description</div>
                        <p class="text-sm text-slate-600"><?php echo e($cohort->description); ?></p>
                    </div>
                    <?php if($cohort->exit_strategy): ?>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Exit Strategy</div>
                            <p class="text-sm text-slate-600"><?php echo e($cohort->exit_strategy); ?></p>
                        </div>
                    <?php endif; ?>
                    <?php if($cohort->funding_end_date): ?>
                        <div>
                            <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Funding Deadline</div>
                            <p class="text-sm text-slate-900"><?php echo e($cohort->funding_end_date->format('M d, Y')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Quick Actions</h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="<?php echo e(route('admin.cohorts.control-panel', $cohort)); ?>" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center group-hover:bg-emerald-200 transition-colors">
                            <i data-lucide="settings" class="w-5 h-5 text-emerald-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-slate-900 text-sm">Control Panel</div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </a>
                    <a href="<?php echo e(route('admin.cohorts.profits.index', $cohort)); ?>" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center group-hover:bg-amber-200 transition-colors">
                            <i data-lucide="trending-up" class="w-5 h-5 text-amber-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-slate-900 text-sm">Profit Management</div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </a>
                    <a href="<?php echo e(route('admin.cohorts.votes.create', $cohort)); ?>" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                            <i data-lucide="vote" class="w-5 h-5 text-purple-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-slate-900 text-sm">Create Vote</div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </a>
                    <a href="<?php echo e(route('admin.cohorts.reports.create', $cohort)); ?>" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                            <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-slate-900 text-sm">Post Update</div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </a>
                    <a href="<?php echo e(route('admin.cohorts.edit', $cohort)); ?>" class="flex items-center space-x-3 p-3 rounded-xl hover:bg-slate-50 transition-colors group">
                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center group-hover:bg-slate-200 transition-colors">
                            <i data-lucide="edit" class="w-5 h-5 text-slate-600"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-bold text-slate-900 text-sm">Edit Cohort</div>
                        </div>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-slate-400"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Members List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900">Members</h3>
                    <span class="text-xs text-slate-500"><?php echo e($cohort->members_count ?? 0); ?> total</span>
                </div>
                <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                    <?php $__empty_1 = true; $__currentLoopData = $cohort->members ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="p-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-slate-600">
                                            <?php echo e(strtoupper(substr($member->user->name ?? 'U', 0, 1))); ?>

                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-bold text-slate-900 text-sm"><?php echo e($member->user->name ?? 'Unknown'); ?></div>
                                        <div class="text-xs text-slate-500">Joined <?php echo e($member->created_at->format('M d, Y')); ?></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-slate-900 font-mono">R<?php echo e(number_format($member->capital_paid / 100, 2)); ?></div>
                                        <div class="text-xs text-slate-500">
                                            <?php echo e($cohort->current_capital > 0 ? number_format(($member->capital_paid / $cohort->current_capital) * 100, 1) : 0); ?>% stake
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-bold uppercase px-2 py-1 rounded
                                        <?php echo e($member->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'); ?>">
                                        <?php echo e($member->status); ?>

                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="p-8 text-center">
                            <i data-lucide="users" class="w-8 h-8 text-slate-300 mx-auto mb-2"></i>
                            <p class="text-sm text-slate-500">No members yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
    
    // Show/hide profit amount field based on event type
    const eventTypeSelect = document.querySelector('select[name="event_type"]');
    const profitField = document.getElementById('profit-amount-field');
    
    if (eventTypeSelect && profitField) {
        eventTypeSelect.addEventListener('change', function() {
            if (this.value === 'profit') {
                profitField.style.display = 'block';
                profitField.querySelector('input').setAttribute('required', 'required');
            } else {
                profitField.style.display = 'none';
                profitField.querySelector('input').removeAttribute('required');
                profitField.querySelector('input').value = '';
            }
        });
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.modern', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\The round table\The Table\resources\views/admin/cohorts/show.blade.php ENDPATH**/ ?>