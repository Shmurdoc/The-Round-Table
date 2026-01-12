<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account - RoundTable</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                        mono: ['Space Mono', 'monospace'],
                    },
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style>
        .gradient-text {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glass-effect {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .bg-grid {
            background-image: 
                linear-gradient(rgba(148, 163, 184, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(148, 163, 184, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-sans">
    <!-- Background Pattern -->
    <div class="fixed inset-0 bg-grid pointer-events-none"></div>
    
    <!-- Gradient Orbs -->
    <div class="fixed top-20 left-20 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none animate-float"></div>
    <div class="fixed bottom-20 right-20 w-80 h-80 bg-amber-600/10 rounded-full blur-3xl pointer-events-none animate-float" style="animation-delay: -3s;"></div>
    
    <div class="min-h-screen flex">
        <!-- Left Side: Registration Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-lg">
                <!-- Mobile Logo -->
                <div class="lg:hidden flex items-center justify-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/30 flex items-center justify-center">
                        <i data-lucide="coins" class="w-5 h-5 text-amber-400"></i>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight">Round<span class="gradient-text">Table</span></span>
                </div>
                
                <!-- Form Card -->
                <div class="glass-effect rounded-3xl border border-slate-700/50 p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-white mb-2">Create your account</h2>
                        <p class="text-slate-400">Join the cooperative partnership community</p>
                    </div>
                    
                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30">
                            <div class="flex items-center gap-2 text-red-400">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                <span class="text-sm font-medium">Please fix the following errors:</span>
                            </div>
                            <ul class="mt-2 text-sm text-red-300 list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf
                        
                        <!-- Name Fields -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-slate-300 mb-2">First Name</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="user" class="w-4 h-4 text-slate-500"></i>
                                    </div>
                                    <input 
                                        type="text" 
                                        id="first_name" 
                                        name="first_name" 
                                        value="{{ old('first_name') }}"
                                        required
                                        class="w-full pl-11 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all"
                                        placeholder="John"
                                    >
                                </div>
                            </div>
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-slate-300 mb-2">Last Name</label>
                                <input 
                                    type="text" 
                                    id="last_name" 
                                    name="last_name" 
                                    value="{{ old('last_name') }}"
                                    required
                                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all"
                                    placeholder="Doe"
                                >
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="mail" class="w-4 h-4 text-slate-500"></i>
                                </div>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}"
                                    required
                                    class="w-full pl-11 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all"
                                    placeholder="you@example.com"
                                >
                            </div>
                        </div>
                        
                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-300 mb-2">Phone Number <span class="text-slate-500">(Optional)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="phone" class="w-4 h-4 text-slate-500"></i>
                                </div>
                                <input 
                                    type="tel" 
                                    id="phone" 
                                    name="phone" 
                                    value="{{ old('phone') }}"
                                    class="w-full pl-11 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all"
                                    placeholder="+27 12 345 6789"
                                >
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="lock" class="w-4 h-4 text-slate-500"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    required
                                    class="w-full pl-11 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all"
                                    placeholder="••••••••"
                                >
                            </div>
                            <p class="mt-1 text-xs text-slate-500">Must be at least 8 characters</p>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">Confirm Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="lock" class="w-4 h-4 text-slate-500"></i>
                                </div>
                                <input 
                                    type="password" 
                                    id="password_confirmation" 
                                    name="password_confirmation" 
                                    required
                                    class="w-full pl-11 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all"
                                    placeholder="••••••••"
                                >
                            </div>
                        </div>
                        
                        <!-- Terms -->
                        <div class="flex items-start gap-3">
                            <input type="checkbox" name="terms" id="terms" required class="mt-1 w-4 h-4 rounded border-slate-600 bg-slate-800 text-amber-500 focus:ring-amber-500/50">
                            <label for="terms" class="text-sm text-slate-400">
                                I agree to the <a href="#" class="text-amber-400 hover:text-amber-300">Terms of Service</a> and <a href="#" class="text-amber-400 hover:text-amber-300">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="w-full py-3 px-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-semibold rounded-xl hover:from-amber-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transform hover:scale-[1.02] active:scale-[0.98] transition-all duration-200"
                        >
                            Create Account
                        </button>
                    </form>
                    
                    <!-- Login Link -->
                    <p class="text-center text-slate-400 mt-6">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-amber-400 hover:text-amber-300 font-medium transition-colors">
                            Sign in
                        </a>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Branding -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900"></div>
            <div class="relative z-10 flex flex-col justify-center px-16">
                <!-- Logo -->
                <div class="flex items-center gap-3 mb-12">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/30 flex items-center justify-center">
                        <i data-lucide="coins" class="w-6 h-6 text-amber-400"></i>
                    </div>
                    <span class="text-2xl font-bold text-white tracking-tight">Round<span class="gradient-text">Table</span></span>
                </div>
                
                <!-- Tagline -->
                <h1 class="text-4xl font-bold text-white mb-6 leading-tight">
                    Build Wealth<br>
                    <span class="gradient-text">Together</span>
                </h1>
                
                <p class="text-lg text-slate-400 mb-12 max-w-md">
                    Cooperative property partnerships with complete transparency. Pool resources, share risks, and grow wealth as a community.
                </p>
                
                <!-- How It Works -->
                <div class="space-y-6">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">How It Works</h3>
                    <div class="space-y-4">
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-amber-400">1</span>
                            </div>
                            <div>
                                <p class="text-white font-medium">Complete KYC Verification</p>
                                <p class="text-sm text-slate-400">Verify your identity to join cohorts</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-amber-400">2</span>
                            </div>
                            <div>
                                <p class="text-white font-medium">Join a Cohort</p>
                                <p class="text-sm text-slate-400">Browse and join open partnership cohorts</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-amber-400">3</span>
                            </div>
                            <div>
                                <p class="text-white font-medium">Contribute Capital</p>
                                <p class="text-sm text-slate-400">Pool funds with other verified members</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-lg bg-green-500/20 flex items-center justify-center flex-shrink-0">
                                <span class="text-sm font-bold text-green-400">4</span>
                            </div>
                            <div>
                                <p class="text-white font-medium">Receive Distributions</p>
                                <p class="text-sm text-slate-400">Get your share of partnership distributions</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
