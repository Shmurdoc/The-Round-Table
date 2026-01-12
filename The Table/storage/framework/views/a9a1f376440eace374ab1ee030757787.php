

<?php $__env->startSection('title', 'Distributions - RoundTable'); ?>
<?php $__env->startSection('page-title', 'Distributions'); ?>

<?php $__env->startSection('content'); ?>
<div class="slide-up space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="banknote" class="w-5 h-5 text-purple-600"></i>
                </div>
                Manage Distributions
            </h1>
            <p class="text-slate-500 text-sm mt-2">Create and track profit distributions to cohort members</p>
        </div>
        <button onclick="openCreateModal()" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition shadow-lg shadow-emerald-200">
            <i data-lucide="plus" class="w-5 h-5"></i>
            New Distribution
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Total Distributed</span>
                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R<?php echo e(number_format($totalDistributed ?? 0)); ?></div>
            <div class="text-xs text-slate-500">All time</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Pending</span>
                <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R<?php echo e(number_format($pendingDistributions ?? 0)); ?></div>
            <div class="text-xs text-slate-500">Awaiting processing</div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">This Month</span>
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="calendar" class="w-4 h-4 text-blue-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R<?php echo e(number_format($thisMonthDistributions ?? 0)); ?></div>
            <div class="text-xs text-slate-500"><?php echo e(now()->format('F Y')); ?></div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <span class="text-[10px] text-slate-400 uppercase font-bold">Avg Per Member</span>
                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4 text-purple-600"></i>
                </div>
            </div>
            <div class="text-2xl font-bold text-slate-900 font-mono">R<?php echo e(number_format($avgPerMember ?? 0)); ?></div>
            <div class="text-xs text-slate-500">Per distribution</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <select name="cohort_id" class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">All Cohorts</option>
                <?php $__currentLoopData = $cohorts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cohort): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cohort->id); ?>" <?php echo e(request('cohort_id') == $cohort->id ? 'selected' : ''); ?>>
                        <?php echo e($cohort->name ?? $cohort->title); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <select name="status" class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">All Status</option>
                <option value="pending" <?php echo e(request('status') === 'pending' ? 'selected' : ''); ?>>Pending</option>
                <option value="completed" <?php echo e(request('status') === 'completed' ? 'selected' : ''); ?>>Completed</option>
            </select>
            <select name="type" class="px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">All Types</option>
                <option value="profit" <?php echo e(request('type') === 'profit' ? 'selected' : ''); ?>>Profit</option>
                <option value="dividend" <?php echo e(request('type') === 'dividend' ? 'selected' : ''); ?>>Dividend</option>
                <option value="return" <?php echo e(request('type') === 'return' ? 'selected' : ''); ?>>Return</option>
            </select>
            <button type="submit" class="px-6 py-3 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Distributions Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Date</th>
                        <th class="text-left px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Cohort</th>
                        <th class="text-left px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Type</th>
                        <th class="text-right px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Total</th>
                        <th class="text-right px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Per Member</th>
                        <th class="text-center px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Members</th>
                        <th class="text-center px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Status</th>
                        <th class="text-right px-6 py-4 text-[10px] uppercase font-bold text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php $__empty_1 = true; $__currentLoopData = $distributions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $distribution): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-900"><?php echo e($distribution->scheduled_date ? $distribution->scheduled_date->format('M d, Y') : 'N/A'); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900 text-sm"><?php echo e($distribution->cohort->name ?? $distribution->cohort->title); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-bold uppercase px-2 py-1 rounded
                                    <?php echo e($distribution->type === 'periodic' ? 'bg-emerald-100 text-emerald-700' : 
                                       ($distribution->type === 'final' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-700')); ?>">
                                    <?php echo e(ucfirst($distribution->type)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold font-mono text-slate-900">R<?php echo e(number_format($distribution->total_amount / 100, 2)); ?></span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-mono text-slate-600">R<?php echo e($distribution->cohort->members_count > 0 ? number_format($distribution->total_amount / 100 / $distribution->cohort->members_count, 2) : '0.00'); ?></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-mono text-slate-600"><?php echo e($distribution->cohort->members_count ?? 0); ?></span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs font-bold uppercase px-2 py-1 rounded
                                    <?php echo e($distribution->status === 'completed' ? 'bg-emerald-100 text-emerald-700' : 
                                       ($distribution->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700')); ?>">
                                    <?php echo e(ucfirst($distribution->status)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="<?php echo e(route('admin.distributions.show', $distribution)); ?>" class="p-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition" title="View">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <?php if($distribution->status === 'pending'): ?>
                                        <form action="<?php echo e(route('admin.distributions.complete', $distribution)); ?>" method="POST" class="inline" onsubmit="return confirm('Process this distribution? This will trigger payments.')">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="p-2 bg-emerald-100 text-emerald-600 rounded-lg hover:bg-emerald-200 transition" title="Complete">
                                                <i data-lucide="check" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <i data-lucide="banknote" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                                <p class="text-slate-500">No distributions yet</p>
                                <button onclick="openCreateModal()" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-emerald-100 text-emerald-700 font-bold rounded-lg hover:bg-emerald-200 transition">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    Create First Distribution
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if(isset($distributions) && $distributions->hasPages()): ?>
            <div class="px-6 py-4 border-t border-slate-100">
                <?php echo e($distributions->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Distribution Modal -->
<div id="createModal" class="fixed inset-0 bg-slate-900/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
        <form action="<?php echo e(route('admin.distributions.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center justify-between">
                    <h3 class="font-bold text-lg text-slate-900">Create New Distribution</h3>
                    <button type="button" onclick="closeCreateModal()" class="p-2 hover:bg-slate-100 rounded-lg transition">
                        <i data-lucide="x" class="w-5 h-5 text-slate-500"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Select Cohort <span class="text-red-500">*</span></label>
                    <select name="cohort_id" id="cohortSelect" onchange="updateCohortInfo()" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                        <option value="">Choose cohort...</option>
                        <?php $__currentLoopData = $cohorts ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cohort): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cohort->id); ?>" data-members="<?php echo e($cohort->members_count ?? 0); ?>" data-pool="<?php echo e(($cohort->contribution_amount ?? 0) * ($cohort->members_count ?? 0)); ?>">
                                <?php echo e($cohort->name ?? $cohort->title); ?> (<?php echo e($cohort->members_count ?? 0); ?> members)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div id="cohortInfo" class="bg-blue-50 rounded-xl p-4 hidden">
                    <div class="text-[10px] uppercase font-bold text-blue-600 mb-2">Cohort Details</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-blue-500">Members</div>
                            <div class="font-bold text-blue-900" id="memberCount">0</div>
                        </div>
                        <div>
                            <div class="text-xs text-blue-500">Total Pool</div>
                            <div class="font-bold text-blue-900" id="totalPool">R0</div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Distribution Type <span class="text-red-500">*</span></label>
                    <select name="type" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                        <option value="profit">Profit Distribution</option>
                        <option value="dividend">Dividend</option>
                        <option value="return">Capital Return</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Total Distribution Amount (R) <span class="text-red-500">*</span></label>
                    <input type="number" name="total_amount" id="totalAmount" onchange="calculatePerMember()" step="0.01" min="0" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                    <p class="text-xs text-slate-500 mt-1">Total amount to be distributed to all members</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Amount Per Member</label>
                    <input type="text" id="perMemberAmount" class="w-full px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm font-mono" readonly>
                    <p class="text-xs text-slate-500 mt-1">Automatically calculated</p>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Distribution Date <span class="text-red-500">*</span></label>
                    <input type="date" name="distribution_date" value="<?php echo e(date('Y-m-d')); ?>" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500" required>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" placeholder="Optional notes about this distribution" 
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
                </div>
            </div>
            
            <div class="p-6 border-t border-slate-100 flex gap-3">
                <button type="button" onclick="closeCreateModal()" class="flex-1 px-4 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition flex items-center justify-center gap-2">
                    <i data-lucide="check" class="w-5 h-5"></i>
                    Create Distribution
                </button>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
    
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        document.getElementById('createModal').classList.add('flex');
    }
    
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.getElementById('createModal').classList.remove('flex');
    }
    
    function updateCohortInfo() {
        const select = document.getElementById('cohortSelect');
        const option = select.options[select.selectedIndex];
        const infoDiv = document.getElementById('cohortInfo');
        
        if (option.value) {
            const members = option.dataset.members;
            const pool = option.dataset.pool;
            document.getElementById('memberCount').textContent = members;
            document.getElementById('totalPool').textContent = 'R' + new Intl.NumberFormat().format(pool);
            infoDiv.classList.remove('hidden');
            calculatePerMember();
        } else {
            infoDiv.classList.add('hidden');
        }
    }
    
    function calculatePerMember() {
        const cohortSelect = document.getElementById('cohortSelect');
        const totalAmount = document.getElementById('totalAmount').value;
        
        if (cohortSelect.value && totalAmount) {
            const option = cohortSelect.options[cohortSelect.selectedIndex];
            const members = parseInt(option.dataset.members);
            if (members > 0) {
                const perMember = parseFloat(totalAmount) / members;
                document.getElementById('perMemberAmount').value = 'R' + perMember.toFixed(2);
            }
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.modern', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\The round table\The Table\resources\views/admin/distributions-modern.blade.php ENDPATH**/ ?>