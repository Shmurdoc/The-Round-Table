<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'RoundTable | Cohort Capital')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['Space Mono', 'monospace'],
                    },
                    colors: {
                        slate: { 850: '#151f32', 900: '#0f172a', 950: '#020617' },
                        amber: { 450: '#F59E0B' }
                    },
                    backgroundImage: {
                        'noise': "url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22 opacity=%220.05%22/%3E%3C/svg%3E')",
                    }
                }
            }
        }
    </script>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        .pattern-diagonal-lines {
            background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(239, 68, 68, 0.05) 10px, rgba(239, 68, 68, 0.05) 20px);
        }
        
        .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
        
        /* Animations */
        .fade-in { animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        .slide-up { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        /* Range Slider Styling */
        input[type=range] {
            -webkit-appearance: none; 
            appearance: none;
            background: transparent; 
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 24px;
            width: 24px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #0f172a;
            cursor: pointer;
            margin-top: -10px; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        input[type=range]::-webkit-slider-runnable-track {
            width: 100%;
            height: 6px;
            cursor: pointer;
            background: #e2e8f0;
            border-radius: 9999px;
        }

        /* Custom scrollbar for sidebar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.2);
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-900 overflow-x-hidden antialiased selection:bg-amber-200 selection:text-amber-900 bg-noise font-sans">

    <!-- DESKTOP SIDEBAR -->
    <nav class="hidden md:flex flex-col w-72 bg-slate-950 h-screen fixed left-0 top-0 border-r border-slate-800 z-50 text-white shadow-2xl">
        <div class="p-8 flex items-center space-x-3 border-b border-slate-800/50">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-slate-900 font-extrabold shadow-lg shadow-amber-500/20">R</div>
            <div>
                <span class="text-xl font-bold tracking-tight block leading-none">RoundTable</span>
                <span class="text-[10px] text-slate-400 uppercase tracking-widest font-mono">Cohort Capital</span>
            </div>
        </div>
        
        <div class="flex-1 py-8 space-y-2 px-4 overflow-y-auto custom-scrollbar">
            @include('partials.sidebar-nav')
        </div>

        <div class="p-6 mt-auto">
            <div class="rounded-xl bg-slate-900/50 border border-slate-800 p-4 backdrop-blur-sm">
                <div class="flex items-center space-x-2 text-xs text-emerald-400 mb-2 font-mono">
                    <i data-lucide="shield-check" class="w-3 h-3"></i>
                    <span>AES-256 Encrypted</span>
                </div>
                <div class="text-[10px] text-slate-500 leading-relaxed border-t border-slate-800 pt-2 mt-2">
                    RoundTable is an algorithmic funding protocol. Capital is deployed at your own risk.
                </div>
            </div>
        </div>
    </nav>

    <!-- MOBILE BOTTOM BAR -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-slate-950/95 backdrop-blur-xl border-t border-slate-800 pb-safe z-50 flex justify-around items-center h-20 px-4 shadow-2xl">
        @include('partials.mobile-nav')
    </nav>

    <!-- MAIN CONTENT AREA -->
    <main class="transition-all duration-300 md:ml-72 p-4 md:p-10 pb-28 md:pb-10 min-h-screen">
        
        <!-- HEADER -->
        <header class="flex justify-between items-center mb-10 sticky top-0 bg-slate-50/90 backdrop-blur-md z-30 py-4 -mx-4 px-4 md:-mx-10 md:px-10 border-b border-transparent transition-all" id="main-header">
            <div class="flex items-center space-x-3 text-sm font-medium text-slate-400">
                <span class="text-slate-500 font-bold tracking-tight">RoundTable</span>
                <i data-lucide="chevron-right" class="w-4 h-4 text-slate-300"></i>
                <span class="text-slate-900 font-bold capitalize bg-white/50 px-3 py-1 rounded-md border border-slate-200/50 shadow-sm">@yield('page-title', 'Dashboard')</span>
            </div>

            <div class="flex items-center space-x-3 pl-4">
                <!-- Notifications -->
                <button class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-200/50 rounded-full transition-colors relative" id="notification-btn">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    @php
                        $unreadCount = auth()->check() ? auth()->user()->notifications()->where('read', false)->count() : 0;
                    @endphp
                    @if($unreadCount > 0)
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-slate-50"></span>
                    @endif
                </button>
                
                <!-- User Profile -->
                <div class="flex items-center space-x-3 bg-white p-1.5 pr-4 rounded-full border border-slate-200 shadow-sm cursor-pointer hover:border-amber-400/30 hover:shadow-md transition-all group" id="user-menu-btn">
                    <div class="h-9 w-9 rounded-full bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-xs ring-2 ring-white group-hover:ring-amber-100 transition-all">
                        {{ strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? 'N', 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-xs text-right leading-tight">
                        <div class="font-bold text-slate-900">{{ auth()->user()->first_name ?? 'User' }} {{ auth()->user()->last_name ?? '' }}</div>
                        <div class="text-slate-500 font-mono text-[10px]">ID: #{{ str_pad(auth()->user()->id, 4, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
            </div>
        </header>

        <!-- FLASH MESSAGES -->
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-start space-x-3 fade-in">
                <div class="bg-emerald-100 p-1.5 rounded-full">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-emerald-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-start space-x-3 fade-in">
                <div class="bg-red-100 p-1.5 rounded-full">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-red-600"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4 fade-in">
                <div class="flex items-start space-x-3">
                    <div class="bg-red-100 p-1.5 rounded-full">
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-red-800 mb-2">Please correct the following errors:</p>
                        <ul class="text-sm text-red-600 list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- VIEW CONTAINER -->
        <div class="max-w-7xl mx-auto fade-in">
            @yield('content')
        </div>

    </main>

    <!-- MODAL CONTAINER -->
    <div id="modal-container" class="fixed inset-0 z-[100] hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
        <div class="absolute right-0 top-0 bottom-0 w-full max-w-2xl bg-white shadow-2xl overflow-y-auto slide-in border-l border-slate-200">
            <div id="modal-content"></div>
        </div>
    </div>

    <!-- User Dropdown Menu -->
    <div id="user-dropdown" class="hidden fixed top-20 right-4 md:right-10 bg-white rounded-xl shadow-2xl border border-slate-200 w-64 z-[60] fade-in">
        <div class="p-4 border-b border-slate-100">
            <div class="text-sm font-bold text-slate-900">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
            <div class="text-xs text-slate-500">{{ auth()->user()->email }}</div>
            <div class="mt-2">
                <span class="text-[10px] font-bold uppercase px-2 py-1 rounded-md 
                    @if(in_array(auth()->user()->kyc_status, ['approved', 'verified'])) bg-emerald-100 text-emerald-700
                    @elseif(auth()->user()->kyc_status === 'pending') bg-amber-100 text-amber-700
                    @else bg-slate-100 text-slate-600 @endif">
                    KYC: {{ ucfirst(auth()->user()->kyc_status ?? 'Not Started') }}
                </span>
            </div>
        </div>
        <div class="p-2">
            <a href="{{ route('kyc.form') }}" class="flex items-center space-x-3 px-3 py-2.5 text-sm text-slate-600 hover:bg-slate-50 rounded-lg transition-colors">
                <i data-lucide="user-circle" class="w-4 h-4"></i>
                <span>Profile & KYC</span>
            </a>
            <a href="{{ route('member.portfolio') }}" class="flex items-center space-x-3 px-3 py-2.5 text-sm text-slate-600 hover:bg-slate-50 rounded-lg transition-colors">
                <i data-lucide="wallet" class="w-4 h-4"></i>
                <span>My Portfolio</span>
            </a>
            <hr class="my-2 border-slate-100">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center space-x-3 px-3 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors w-full">
                    <i data-lucide="log-out" class="w-4 h-4"></i>
                    <span>Sign Out</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Notification Dropdown -->
    <div id="notification-dropdown" class="hidden fixed top-20 right-4 md:right-10 bg-white rounded-xl shadow-2xl border border-slate-200 w-96 max-h-[600px] overflow-y-auto z-[60] fade-in">
        <div class="sticky top-0 bg-white border-b border-slate-100 p-4 z-10">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-bold text-slate-900">Notifications</h3>
                @if($unreadCount > 0)
                <span class="text-xs font-bold px-2 py-1 bg-amber-100 text-amber-700 rounded-md">{{ $unreadCount }} new</span>
                @endif
            </div>
        </div>
        
        <div class="divide-y divide-slate-100">
            @php
                $notifications = auth()->check() ? auth()->user()->notifications()->latest()->take(10)->get() : collect();
            @endphp
            
            @forelse($notifications as $notification)
            <a href="{{ $notification->action_url ?? '#' }}" 
               class="block p-4 hover:bg-slate-50 transition-colors {{ $notification->read ? 'opacity-60' : 'bg-amber-50/30' }}">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center
                        {{ $notification->type === 'kyc' ? 'bg-emerald-100' : 
                           ($notification->type === 'distribution' ? 'bg-purple-100' : 
                           ($notification->type === 'cohort' ? 'bg-blue-100' : 'bg-slate-100')) }}">
                        <i data-lucide="{{ $notification->type === 'kyc' ? 'shield-check' : 
                                          ($notification->type === 'distribution' ? 'banknote' : 
                                          ($notification->type === 'cohort' ? 'layers' : 'bell')) }}" 
                           class="w-5 h-5 
                           {{ $notification->type === 'kyc' ? 'text-emerald-600' : 
                              ($notification->type === 'distribution' ? 'text-purple-600' : 
                              ($notification->type === 'cohort' ? 'text-blue-600' : 'text-slate-600')) }}"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-900 mb-1">{{ $notification->title }}</p>
                        <p class="text-xs text-slate-600 line-clamp-2">{{ $notification->message }}</p>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="text-[10px] text-slate-400 font-mono">{{ $notification->created_at->diffForHumans() }}</span>
                            @if(!$notification->read)
                            <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
            @empty
            <div class="p-8 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="bell-off" class="w-8 h-8 text-slate-400"></i>
                </div>
                <p class="text-sm text-slate-500 font-medium">No notifications yet</p>
                <p class="text-xs text-slate-400 mt-1">We'll notify you of important updates</p>
            </div>
            @endforelse
        </div>
        
        @if($notifications->count() > 0)
        <div class="sticky bottom-0 bg-slate-50 border-t border-slate-100 p-3">
            <a href="{{ route('member.notifications') }}" class="block text-center text-xs font-bold text-amber-600 hover:text-amber-700 transition-colors">
                View All Notifications
            </a>
        </div>
        @endif
    </div>

    <!-- Scripts -->
    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.getElementById('main-header');
            if (window.scrollY > 10) {
                header.classList.add('border-slate-200', 'shadow-sm');
            } else {
                header.classList.remove('border-slate-200', 'shadow-sm');
            }
        });

        // User dropdown toggle
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userDropdown = document.getElementById('user-dropdown');
        const notificationBtn = document.getElementById('notification-btn');
        const notificationDropdown = document.getElementById('notification-dropdown');
        
        userMenuBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            notificationDropdown?.classList.add('hidden');
            userDropdown.classList.toggle('hidden');
        });

        notificationBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown?.classList.add('hidden');
            notificationDropdown.classList.toggle('hidden');
            // Re-initialize icons after showing dropdown
            setTimeout(() => lucide.createIcons(), 50);
        });

        document.addEventListener('click', () => {
            userDropdown?.classList.add('hidden');
            notificationDropdown?.classList.add('hidden');
        });

        // Modal functions
        function openModal(content) {
            document.getElementById('modal-content').innerHTML = content;
            document.getElementById('modal-container').classList.remove('hidden');
            lucide.createIcons();
        }

        function closeModal() {
            document.getElementById('modal-container').classList.add('hidden');
        }

        // Re-initialize icons after any dynamic content load
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>

    @stack('scripts')
</body>
</html>
