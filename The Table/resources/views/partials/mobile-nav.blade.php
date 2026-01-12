@php
    $currentRoute = request()->route()->getName() ?? '';
    
    $mobileNavItems = [
        ['id' => 'dashboard', 'label' => 'Home', 'icon' => 'layout-dashboard', 'route' => 'member.dashboard'],
        ['id' => 'wallet', 'label' => 'Wallet', 'icon' => 'wallet', 'route' => 'wallet.index'],
        ['id' => 'cohorts', 'label' => 'Cohorts', 'icon' => 'layers', 'route' => 'cohorts.index'],
        ['id' => 'portfolio', 'label' => 'Portfolio', 'icon' => 'briefcase', 'route' => 'member.portfolio'],
        ['id' => 'profile', 'label' => 'Profile', 'icon' => 'user-circle', 'route' => 'kyc.form'],
    ];
@endphp

@foreach($mobileNavItems as $item)
    @php
        $isActive = Str::startsWith($currentRoute, explode('.', $item['route'])[0]);
    @endphp
    
    <a href="{{ route($item['route']) }}" 
        class="flex flex-col items-center justify-center w-full h-full space-y-1.5 transition-colors duration-200 {{ $isActive ? 'text-amber-500' : 'text-slate-500' }}">
        <div class="{{ $isActive ? 'bg-amber-500/10 p-1.5 rounded-xl' : 'p-1.5' }}">
            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5" stroke-width="{{ $isActive ? 2.5 : 2 }}"></i>
        </div>
        <span class="text-[10px] font-medium tracking-wide">{{ $item['label'] }}</span>
    </a>
@endforeach
