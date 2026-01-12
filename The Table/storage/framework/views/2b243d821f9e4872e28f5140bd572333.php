<?php
    $currentRoute = request()->route()->getName() ?? '';
    
    $mobileNavItems = [
        ['id' => 'dashboard', 'label' => 'Home', 'icon' => 'layout-dashboard', 'route' => 'member.dashboard'],
        ['id' => 'wallet', 'label' => 'Wallet', 'icon' => 'wallet', 'route' => 'wallet.index'],
        ['id' => 'cohorts', 'label' => 'Cohorts', 'icon' => 'layers', 'route' => 'cohorts.index'],
        ['id' => 'portfolio', 'label' => 'Portfolio', 'icon' => 'briefcase', 'route' => 'member.portfolio'],
        ['id' => 'profile', 'label' => 'Profile', 'icon' => 'user-circle', 'route' => 'kyc.form'],
    ];
?>

<?php $__currentLoopData = $mobileNavItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        $isActive = Str::startsWith($currentRoute, explode('.', $item['route'])[0]);
    ?>
    
    <a href="<?php echo e(route($item['route'])); ?>" 
        class="flex flex-col items-center justify-center w-full h-full space-y-1.5 transition-colors duration-200 <?php echo e($isActive ? 'text-amber-500' : 'text-slate-500'); ?>">
        <div class="<?php echo e($isActive ? 'bg-amber-500/10 p-1.5 rounded-xl' : 'p-1.5'); ?>">
            <i data-lucide="<?php echo e($item['icon']); ?>" class="w-5 h-5" stroke-width="<?php echo e($isActive ? 2.5 : 2); ?>"></i>
        </div>
        <span class="text-[10px] font-medium tracking-wide"><?php echo e($item['label']); ?></span>
    </a>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH C:\wamp64\www\The round table\The Table\resources\views/partials/mobile-nav.blade.php ENDPATH**/ ?>