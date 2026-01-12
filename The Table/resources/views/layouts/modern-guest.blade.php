<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'RoundTable - Cooperative Investment Platform')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideUp {
            from { 
                opacity: 0;
                transform: translateY(20px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }
        .fade-in { animation: fadeIn 0.3s ease-out; }
        .slide-up { animation: slideUp 0.4s ease-out; }
        
        /* Gradient backgrounds */
        .gradient-warm {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
        }
    </style>
    
    @stack('styles')
</head>

<body class="bg-slate-50 min-h-screen antialiased">
    <!-- TOP NAVIGATION -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3">
                    <div class="h-10 w-10 rounded-xl bg-slate-900 text-amber-400 flex items-center justify-center font-black text-lg">
                        RT
                    </div>
                    <span class="text-xl font-bold text-slate-900">RoundTable</span>
                </a>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-6">
                    <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Home</a>
                    <a href="{{ route('about') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">About</a>
                    <a href="{{ route('how-it-works') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">How It Works</a>
                </div>
                
                <!-- Auth Buttons -->
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ route('member.dashboard') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Sign In</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    
    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @yield('content')
    </main>
    
    <!-- FOOTER -->
    <footer class="bg-slate-900 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="h-10 w-10 rounded-xl bg-amber-400 text-slate-900 flex items-center justify-center font-black text-lg">
                            RT
                        </div>
                        <span class="text-xl font-bold">RoundTable</span>
                    </div>
                    <p class="text-slate-400 text-sm max-w-md">
                        A cooperative investment platform enabling collective ownership of income-generating assets through transparent, blockchain-verified pooling.
                    </p>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-wider text-slate-400 mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('about') }}" class="text-slate-400 hover:text-white text-sm transition-colors">About Us</a></li>
                        <li><a href="{{ route('how-it-works') }}" class="text-slate-400 hover:text-white text-sm transition-colors">How It Works</a></li>
                        <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-white text-sm transition-colors">Sign In</a></li>
                    </ul>
                </div>
                
                <!-- Legal -->
                <div>
                    <h4 class="font-bold text-sm uppercase tracking-wider text-slate-400 mb-4">Legal</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-slate-400 hover:text-white text-sm transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white text-sm transition-colors">Terms of Service</a></li>
                        <li><a href="#" class="text-slate-400 hover:text-white text-sm transition-colors">Risk Disclosure</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-slate-800 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-slate-400 text-sm">&copy; {{ date('Y') }} RoundTable. All rights reserved.</p>
                <p class="text-slate-500 text-xs mt-2 md:mt-0">Not financial advice. Investments carry risk.</p>
            </div>
        </div>
    </footer>
    
    <!-- Lucide Icons Initialization -->
    <script>
        lucide.createIcons();
    </script>
    
    @stack('scripts')
</body>
</html>
