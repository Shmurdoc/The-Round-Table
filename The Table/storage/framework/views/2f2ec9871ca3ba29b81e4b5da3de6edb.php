

<?php $__env->startSection('title', 'KYC Review - RoundTable'); ?>
<?php $__env->startSection('page-title', 'KYC Review'); ?>

<?php $__env->startSection('content'); ?>
<div class="slide-up space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="user-check" class="w-5 h-5 text-amber-600"></i>
                </div>
                KYC Review
            </h1>
            <p class="text-slate-500 text-sm mt-2">Review <?php echo e($user->name); ?>'s identity documents</p>
        </div>
        <a href="<?php echo e(route('admin.kyc')); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to KYC List
        </a>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50">
            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="user" class="w-4 h-4 text-slate-500"></i>
                User Information
            </h3>
        </div>
        <div class="p-6">
            <div class="flex items-center gap-6 mb-6">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center">
                    <span class="text-2xl font-bold text-slate-600"><?php echo e(strtoupper(substr($user->name ?? 'U', 0, 1))); ?></span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-900"><?php echo e($user->name); ?></h2>
                    <p class="text-slate-500"><?php echo e($user->email); ?></p>
                    <p class="text-sm text-slate-400">Submitted <?php echo e($user->kyc_submitted_at?->diffForHumans() ?? 'recently'); ?></p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <span class="text-xs text-slate-400 uppercase font-bold">Phone</span>
                    <p class="text-slate-900 font-medium"><?php echo e($user->phone ?? 'Not provided'); ?></p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-bold">ID Number</span>
                    <p class="text-slate-900 font-mono"><?php echo e($user->kyc_id_number ?? 'Not provided'); ?></p>
                </div>
                <div>
                    <span class="text-xs text-slate-400 uppercase font-bold">Status</span>
                    <?php
                        $statusColors = [
                            'verified' => 'bg-emerald-100 text-emerald-700',
                            'pending' => 'bg-amber-100 text-amber-700',
                            'rejected' => 'bg-red-100 text-red-700',
                            'not_started' => 'bg-slate-100 text-slate-600',
                        ];
                    ?>
                    <span class="inline-flex px-3 py-1 text-xs font-bold rounded-lg <?php echo e($statusColors[$user->kyc_status] ?? $statusColors['not_started']); ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $user->kyc_status))); ?>

                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50">
            <h3 class="font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4 text-slate-500"></i>
                Uploaded Documents
            </h3>
        </div>
        <div class="p-6 space-y-6">
            <!-- ID Document Front -->
            <div>
                <label class="text-sm font-bold text-slate-700 mb-2 block">ID Document (Front)</label>
                <?php if($user->kyc_id_document_front): ?>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                        <a href="<?php echo e(Storage::url($user->kyc_id_document_front)); ?>" target="_blank" 
                           class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-medium">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            View Document
                        </a>
                        <img src="<?php echo e(Storage::url($user->kyc_id_document_front)); ?>" 
                             alt="ID Front" 
                             class="mt-3 max-h-64 rounded-lg border border-slate-200">
                    </div>
                <?php else: ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm">
                        <i data-lucide="alert-circle" class="w-4 h-4 inline mr-2"></i>
                        Not uploaded
                    </div>
                <?php endif; ?>
            </div>

            <!-- ID Document Back -->
            <div>
                <label class="text-sm font-bold text-slate-700 mb-2 block">ID Document (Back)</label>
                <?php if($user->kyc_id_document_back): ?>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                        <a href="<?php echo e(Storage::url($user->kyc_id_document_back)); ?>" target="_blank" 
                           class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-medium">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            View Document
                        </a>
                        <img src="<?php echo e(Storage::url($user->kyc_id_document_back)); ?>" 
                             alt="ID Back" 
                             class="mt-3 max-h-64 rounded-lg border border-slate-200">
                    </div>
                <?php else: ?>
                    <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm">
                        <i data-lucide="alert-circle" class="w-4 h-4 inline mr-2"></i>
                        Not uploaded
                    </div>
                <?php endif; ?>
            </div>

            <!-- Proof of Residence -->
            <div>
                <label class="text-sm font-bold text-slate-700 mb-2 block">Proof of Residence</label>
                <?php if($user->kyc_proof_of_residence): ?>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                        <a href="<?php echo e(Storage::url($user->kyc_proof_of_residence)); ?>" target="_blank" 
                           class="inline-flex items-center gap-2 text-amber-600 hover:text-amber-700 font-medium">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            View Document
                        </a>
                        <img src="<?php echo e(Storage::url($user->kyc_proof_of_residence)); ?>" 
                             alt="Proof of Residence" 
                             class="mt-3 max-h-64 rounded-lg border border-slate-200">
                    </div>
                <?php else: ?>
                    <div class="bg-amber-50 text-amber-600 p-4 rounded-xl text-sm">
                        <i data-lucide="info" class="w-4 h-4 inline mr-2"></i>
                        Not uploaded (optional)
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <?php if($user->kyc_status === 'pending'): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="check-square" class="w-4 h-4 text-slate-500"></i>
                    Verification Decision
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Approve -->
                    <form action="<?php echo e(route('admin.kyc.approve', $user)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition shadow-lg shadow-emerald-200">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            Approve KYC
                        </button>
                    </form>

                    <!-- Reject -->
                    <button type="button" onclick="document.getElementById('rejectModal').classList.remove('hidden')"
                            class="w-full inline-flex items-center justify-center gap-2 px-6 py-4 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition shadow-lg shadow-red-200">
                        <i data-lucide="x-circle" class="w-5 h-5"></i>
                        Reject KYC
                    </button>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div id="rejectModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Reject KYC</h3>
                </div>
                <form action="<?php echo e(route('admin.kyc.reject', $user)); ?>" method="POST" class="p-6">
                    <?php echo csrf_field(); ?>
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Rejection Reason</label>
                        <textarea name="rejection_reason" rows="4" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                            placeholder="Please explain why this KYC submission is being rejected..."></textarea>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')"
                                class="flex-1 px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition">
                            Confirm Reject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php elseif($user->kyc_status === 'rejected'): ?>
        <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="x-circle" class="w-5 h-5 text-red-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-red-900">KYC Rejected</h4>
                    <p class="text-red-700 mt-1"><?php echo e($user->kyc_rejection_reason ?? 'No reason provided.'); ?></p>
                </div>
            </div>
        </div>
    <?php elseif($user->kyc_status === 'verified'): ?>
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-emerald-900">KYC Verified</h4>
                    <p class="text-emerald-700 mt-1">This user's identity has been verified.</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.modern', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\The round table\The Table\resources\views/admin/kyc-review.blade.php ENDPATH**/ ?>