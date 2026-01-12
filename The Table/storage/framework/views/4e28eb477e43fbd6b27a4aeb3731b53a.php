<?php
    $currentRoute = request()->route()->getName() ?? '';
    $user = auth()->user();
    $role = $user->role ?? 'member';
    
    // Build navigation based on role - DIFFERENT MENUS FOR DIFFERENT ROLES
    $navItems = [];
    
    if ($role === 'platform_admin') {
        // Platform Admin Navigation
        $navItems = [
            ['id' => 'platform-dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'platform-admin.dashboard'],
            ['id' => 'all-users', 'label' => 'All Users', 'icon' => 'users-2', 'route' => 'platform-admin.users.index'],
            ['id' => 'kyc-review', 'label' => 'KYC Review', 'icon' => 'file-check', 'route' => 'platform-admin.kyc'],
            ['id' => 'reports', 'label' => 'Reports', 'icon' => 'bar-chart-2', 'route' => 'platform-admin.reports'],
            ['id' => 'settings', 'label' => 'Settings', 'icon' => 'sliders', 'route' => 'platform-admin.settings'],
            ['id' => 'divider-1', 'divider' => true],
            ['id' => 'cohorts', 'label' => 'All Cohorts', 'icon' => 'layers', 'route' => 'cohorts.index'],
        ];
    } elseif ($role === 'admin') {
        // Cohort Admin Navigation
        $navItems = [
            ['id' => 'admin-dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'admin.dashboard'],
            ['id' => 'my-cohorts', 'label' => 'My Cohorts', 'icon' => 'layers', 'route' => 'admin.cohorts.index'],
            ['id' => 'distributions', 'label' => 'Distributions', 'icon' => 'banknote', 'route' => 'admin.distributions.index'],
            ['id' => 'members', 'label' => 'Members', 'icon' => 'users', 'route' => 'admin.members'],
            ['id' => 'kyc-review', 'label' => 'KYC Review', 'icon' => 'file-check', 'route' => 'admin.kyc'],
            ['id' => 'divider-1', 'divider' => true],
            ['id' => 'browse-cohorts', 'label' => 'Browse Cohorts', 'icon' => 'search', 'route' => 'cohorts.index'],
            ['id' => 'profile', 'label' => 'My Profile', 'icon' => 'user-circle', 'route' => 'kyc.form'],
        ];
    } else {
        // Member Navigation
        $navItems = [
            ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'layout-dashboard', 'route' => 'member.dashboard'],
            ['id' => 'wallet', 'label' => 'My Wallet', 'icon' => 'wallet', 'route' => 'wallet.index'],
            ['id' => 'cohorts', 'label' => 'Browse Cohorts', 'icon' => 'layers', 'route' => 'cohorts.index'],
            ['id' => 'portfolio', 'label' => 'My Portfolio', 'icon' => 'briefcase', 'route' => 'member.portfolio'],
            ['id' => 'notifications', 'label' => 'Notifications', 'icon' => 'bell', 'route' => 'member.notifications'],
            ['id' => 'divider-1', 'divider' => true],
            ['id' => 'profile', 'label' => 'Profile & KYC', 'icon' => 'user-circle', 'route' => 'kyc.form'],
        ];
    }
?>

<?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php if(isset($item['divider']) && $item['divider']): ?>
        <div class="border-t border-slate-800 my-4 mx-2"></div>
        <?php continue; ?>
    <?php endif; ?>
    
    <?php
        // Check if route exists before rendering
        $routeExists = \Route::has($item['route']);
        if (!$routeExists) continue;
        
        // Use exact match or check if current route starts with the item's route (for sub-routes)
        $isActive = $currentRoute === $item['route'] || 
                    Str::startsWith($currentRoute, $item['route'] . '.');
    ?>
    
    <a href="<?php echo e(route($item['route'])); ?>" 
        class="w-full flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200 group relative overflow-hidden
        <?php echo e($isActive 
            ? 'bg-gradient-to-r from-amber-500 to-amber-600 text-white shadow-lg shadow-amber-900/20 font-semibold' 
            : 'text-slate-400 hover:bg-slate-800/50 hover:text-slate-200'); ?>">
        <i data-lucide="<?php echo e($item['icon']); ?>" class="w-5 h-5 relative z-10"></i>
        <span class="relative z-10"><?php echo e($item['label']); ?></span>
        <?php if(!$isActive): ?>
            <div class="absolute inset-0 bg-white/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <?php endif; ?>
    </a>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Role Badge -->
<div class="mt-auto pt-4 border-t border-slate-800 mx-2">
    <div class="px-4 py-3">
        <div class="text-xs text-slate-500 uppercase tracking-wider mb-2">Account Type</div>
        <?php if($role === 'platform_admin'): ?>
            <div class="flex items-center gap-2 text-purple-400">
                <i data-lucide="crown" class="w-4 h-4"></i>
                <span class="text-sm font-semibold">Platform Admin</span>
            </div>
        <?php elseif($role === 'admin'): ?>
            <div class="flex items-center gap-2 text-blue-400">
                <i data-lucide="shield" class="w-4 h-4"></i>
                <span class="text-sm font-semibold">Cohort Admin</span>
            </div>
        <?php else: ?>
            <div class="flex items-center gap-2 text-amber-400">
                <i data-lucide="user" class="w-4 h-4"></i>
                <span class="text-sm font-semibold">Member</span>
            </div>
            <?php if(!in_array($user->kyc_status, ['approved', 'verified'])): ?>
                <div class="mt-2 text-xs text-red-400 flex items-center gap-1">
                    <i data-lucide="alert-triangle" class="w-3 h-3"></i>
                    <span>KYC Required</span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\wamp64\www\The round table\The Table\resources\views/partials/sidebar-nav.blade.php ENDPATH**/ ?>