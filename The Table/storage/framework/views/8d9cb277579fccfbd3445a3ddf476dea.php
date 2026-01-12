

<?php $__env->startSection('title', 'Edit Cohort - RoundTable'); ?>
<?php $__env->startSection('page-title', 'Edit Cohort'); ?>

<?php $__env->startSection('content'); ?>
<div class="slide-up space-y-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="edit" class="w-5 h-5 text-amber-600"></i>
                </div>
                Edit Cohort
            </h1>
            <p class="text-slate-500 text-sm mt-2">Update cohort details for: <?php echo e($cohort->title); ?></p>
        </div>
        <a href="<?php echo e(route('admin.cohorts.show', $cohort)); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-700 text-sm font-bold rounded-xl hover:bg-slate-200 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back
        </a>
    </div>

    <!-- Status Badge -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm text-blue-800">
                    <strong>Current Status:</strong> 
                    <span class="px-2 py-1 rounded-full text-xs font-bold 
                        <?php echo e($cohort->status === 'draft' ? 'bg-slate-200 text-slate-700' : 'bg-blue-200 text-blue-700'); ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $cohort->status))); ?>

                    </span>
                </p>
                <?php if($cohort->status === 'pending_approval'): ?>
                    <p class="text-xs text-blue-600 mt-1">Changes will require re-approval from the platform.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Error Display -->
    <?php if($errors->any()): ?>
        <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                </div>
                <div>
                    <h4 class="font-bold text-red-800">Please correct the following errors:</h4>
                    <ul class="mt-2 space-y-1 text-sm text-red-600">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.cohorts.update', $cohort)); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <!-- Basic Info -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4 text-slate-500"></i>
                    Basic Information
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Cohort Name <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo e(old('title', $cohort->title)); ?>" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="e.g., Tech Growth Fund 2026">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Description <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="4" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="Describe the investment thesis and goals..."><?php echo e(old('description', $cohort->description)); ?></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Cohort Class <span class="text-red-500">*</span></label>
                        <select name="cohort_class" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['cohort_class'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Select cohort class...</option>
                            <option value="utilization" <?php echo e(old('cohort_class', $cohort->cohort_class) == 'utilization' ? 'selected' : ''); ?>>Utilization (Rental Income)</option>
                            <option value="lease" <?php echo e(old('cohort_class', $cohort->cohort_class) == 'lease' ? 'selected' : ''); ?>>Lease (Asset Leasing)</option>
                            <option value="resale" <?php echo e(old('cohort_class', $cohort->cohort_class) == 'resale' ? 'selected' : ''); ?>>Resale (Buy & Sell)</option>
                            <option value="hybrid" <?php echo e(old('cohort_class', $cohort->cohort_class) == 'hybrid' ? 'selected' : ''); ?>>Hybrid (Mixed Strategy)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Asset Type <span class="text-red-500">*</span></label>
                        <select name="asset_type" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['asset_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Select asset type...</option>
                            <option value="real_estate" <?php echo e(old('asset_type', $cohort->asset_type) == 'real_estate' ? 'selected' : ''); ?>>Real Estate</option>
                            <option value="equipment" <?php echo e(old('asset_type', $cohort->asset_type) == 'equipment' ? 'selected' : ''); ?>>Equipment</option>
                            <option value="business" <?php echo e(old('asset_type', $cohort->asset_type) == 'business' ? 'selected' : ''); ?>>Business</option>
                            <option value="renewable_energy" <?php echo e(old('asset_type', $cohort->asset_type) == 'renewable_energy' ? 'selected' : ''); ?>>Renewable Energy</option>
                            <option value="intellectual_property" <?php echo e(old('asset_type', $cohort->asset_type) == 'intellectual_property' ? 'selected' : ''); ?>>Intellectual Property</option>
                            <option value="other" <?php echo e(old('asset_type', $cohort->asset_type) == 'other' ? 'selected' : ''); ?>>Other</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Capital Requirements -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="banknote" class="w-4 h-4 text-slate-500"></i>
                    Capital Requirements
                </h3>
                <p class="text-xs text-slate-500 mt-1">All amounts in cents. Minimum R3,000 = 300000 cents.</p>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Minimum Viable Capital <span class="text-red-500">*</span></label>
                        <input type="number" name="minimum_viable_capital" value="<?php echo e(old('minimum_viable_capital', $cohort->minimum_viable_capital)); ?>" min="300000" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['minimum_viable_capital'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="300000">
                        <p class="text-xs text-slate-400 mt-1">Min to proceed (300000 = R3,000)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Ideal Target <span class="text-red-500">*</span></label>
                        <input type="number" name="ideal_target" value="<?php echo e(old('ideal_target', $cohort->ideal_target)); ?>" min="300000" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['ideal_target'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="1000000">
                        <p class="text-xs text-slate-400 mt-1">Optimal funding goal</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Hard Cap <span class="text-red-500">*</span></label>
                        <input type="number" name="hard_cap" value="<?php echo e(old('hard_cap', $cohort->hard_cap)); ?>" min="300000" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['hard_cap'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="2000000">
                        <p class="text-xs text-slate-400 mt-1">Max funding limit</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Min Contribution <span class="text-red-500">*</span></label>
                        <input type="number" name="min_contribution" value="<?php echo e(old('min_contribution', $cohort->min_contribution)); ?>" min="300000" max="10000000" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['min_contribution'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="300000">
                        <p class="text-xs text-slate-400 mt-1">Min per member (R3k-R100k)</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Max Contribution <span class="text-red-500">*</span></label>
                        <input type="number" name="max_contribution" value="<?php echo e(old('max_contribution', $cohort->max_contribution)); ?>" min="300000" max="10000000" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['max_contribution'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="10000000">
                        <p class="text-xs text-slate-400 mt-1">Max per member (R3k-R100k)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                    Timeline & Duration
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Funding Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="funding_start_date" value="<?php echo e(old('funding_start_date', $cohort->funding_start_date?->format('Y-m-d'))); ?>" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['funding_start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Funding End Date <span class="text-red-500">*</span></label>
                        <input type="date" name="funding_end_date" value="<?php echo e(old('funding_end_date', $cohort->funding_end_date?->format('Y-m-d'))); ?>" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['funding_end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Expected Exit Date <span class="text-red-500">*</span></label>
                        <input type="date" name="expected_exit_date" value="<?php echo e(old('expected_exit_date', $cohort->expected_exit_date?->format('Y-m-d'))); ?>" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['expected_exit_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Duration (Months) <span class="text-red-500">*</span></label>
                        <input type="number" name="duration_months" value="<?php echo e(old('duration_months', $cohort->duration_months)); ?>" min="1" max="120" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['duration_months'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="12">
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk & Returns -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="shield" class="w-4 h-4 text-slate-500"></i>
                    Risk & Exit Strategy
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Risk Level <span class="text-red-500">*</span></label>
                        <select name="risk_level" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['risk_level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">Select risk level...</option>
                            <option value="low" <?php echo e(old('risk_level', $cohort->risk_level) == 'low' ? 'selected' : ''); ?>>Low Risk</option>
                            <option value="moderate" <?php echo e(old('risk_level', $cohort->risk_level) == 'moderate' ? 'selected' : ''); ?>>Moderate Risk</option>
                            <option value="high" <?php echo e(old('risk_level', $cohort->risk_level) == 'high' ? 'selected' : ''); ?>>High Risk</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Projected Annual Return (%)</label>
                        <input type="number" name="projected_annual_return" value="<?php echo e(old('projected_annual_return', $cohort->projected_annual_return)); ?>" min="0" max="100" step="0.1"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                            placeholder="15.5">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Exit Strategy <span class="text-red-500">*</span></label>
                    <textarea name="exit_strategy" rows="3" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 <?php $__errorArgs = ['exit_strategy'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                        placeholder="Describe how investors will exit and receive returns..."><?php echo e(old('exit_strategy', $cohort->exit_strategy)); ?></textarea>
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Risk Factors (Optional)</label>
                    <textarea name="risk_factors" rows="2"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"
                        placeholder="Describe potential risks..."><?php echo e(old('risk_factors', $cohort->risk_factors)); ?></textarea>
                </div>
            </div>
        </div>

        <!-- Files -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h3 class="font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="upload" class="w-4 h-4 text-slate-500"></i>
                    Documents & Media
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Featured Image</label>
                        <?php if($cohort->featured_image): ?>
                            <div class="mb-2 p-2 bg-slate-50 rounded-lg flex items-center gap-2">
                                <i data-lucide="image" class="w-4 h-4 text-slate-400"></i>
                                <span class="text-xs text-slate-600">Current: <?php echo e(basename($cohort->featured_image)); ?></span>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="featured_image" accept="image/*"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <p class="text-xs text-slate-400 mt-1">Upload new to replace current image</p>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Prospectus PDF</label>
                        <?php if($cohort->prospectus_file): ?>
                            <div class="mb-2 p-2 bg-slate-50 rounded-lg flex items-center gap-2">
                                <i data-lucide="file-text" class="w-4 h-4 text-slate-400"></i>
                                <span class="text-xs text-slate-600">Current: <?php echo e(basename($cohort->prospectus_file)); ?></span>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="prospectus_file" accept=".pdf"
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <p class="text-xs text-slate-400 mt-1">Upload new to replace current file</p>
                    </div>
                </div>

                <!-- Gallery Images -->
                <div class="mt-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Gallery Images (Optional)</label>
                    <?php if($cohort->images && is_array($cohort->images) && count($cohort->images) > 0): ?>
                        <div class="mb-3 grid grid-cols-4 gap-2">
                            <?php $__currentLoopData = $cohort->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $galleryImage): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="relative group">
                                    <img src="<?php echo e(asset('storage/' . $galleryImage)); ?>" 
                                         class="w-full h-20 object-cover rounded-lg border border-slate-200">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition rounded-lg flex items-center justify-center">
                                        <i data-lucide="check" class="w-4 h-4 text-white"></i>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <p class="text-xs text-slate-500 mb-2">Current gallery (<?php echo e(count($cohort->images)); ?> images). Upload new images to replace all.</p>
                    <?php endif; ?>
                    <input type="file" 
                           name="images[]" 
                           id="gallery_images"
                           accept="image/*" 
                           multiple
                           onchange="previewGalleryImages(this)"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <p class="text-xs text-slate-400 mt-1">Select multiple images (max 10, up to 2MB each)</p>
                    
                    <!-- Preview Container -->
                    <div id="gallery_preview" class="mt-3 grid grid-cols-4 gap-2 hidden"></div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="flex gap-4 justify-end">
            <a href="<?php echo e(route('admin.cohorts.show', $cohort)); ?>" class="px-6 py-3 bg-slate-100 text-slate-700 font-bold rounded-xl hover:bg-slate-200 transition">
                Cancel
            </a>
            <button type="submit" class="inline-flex items-center gap-2 px-8 py-3 bg-amber-500 text-slate-900 font-bold rounded-xl hover:bg-amber-400 transition shadow-lg shadow-amber-200">
                <i data-lucide="save" class="w-5 h-5"></i>
                Save Changes
            </button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    lucide.createIcons();
    
    // Preview gallery images
    function previewGalleryImages(input) {
        const preview = document.getElementById('gallery_preview');
        preview.innerHTML = '';
        
        if (input.files && input.files.length > 0) {
            preview.classList.remove('hidden');
            
            Array.from(input.files).slice(0, 10).forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative group';
                    div.innerHTML = `
                        <img src="${e.target.result}" 
                             class="w-full h-20 object-cover rounded-lg border-2 border-amber-400">
                        <div class="absolute top-1 right-1 bg-amber-500 text-white text-xs px-2 py-1 rounded">
                            ${index + 1}
                        </div>
                    `;
                    preview.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        } else {
            preview.classList.add('hidden');
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.modern', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\wamp64\www\The round table\The Table\resources\views/admin/cohorts/edit.blade.php ENDPATH**/ ?>