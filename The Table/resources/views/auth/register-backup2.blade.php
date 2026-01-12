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
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
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
        
        /* Ken Burns Animation */
        @keyframes kenburns {
            0% { transform: scale(1) translate(0, 0); }
            25% { transform: scale(1.1) translate(-2%, -1%); }
            50% { transform: scale(1.15) translate(-1%, 2%); }
            75% { transform: scale(1.1) translate(2%, 1%); }
            100% { transform: scale(1) translate(0, 0); }
        }
        
        .kenburns {
            animation: kenburns 30s ease-in-out infinite;
        }
        
        /* Cyber Border */
        .cyber-border {
            position: relative;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(217, 119, 6, 0.1));
            border-radius: 1rem;
            padding: 4px;
        }
        
        .cyber-border::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 1rem;
            padding: 2px;
            background: linear-gradient(135deg, #f59e0b, #d97706, #f59e0b);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: borderPulse 3s ease-in-out infinite;
        }
        
        @keyframes borderPulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        
        /* Pulse Glow */
        .pulse-glow {
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.3),
                        0 0 40px rgba(245, 158, 11, 0.1),
                        0 0 60px rgba(245, 158, 11, 0.05);
            animation: pulseGlow 3s ease-in-out infinite;
        }
        
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.3), 0 0 40px rgba(245, 158, 11, 0.1); }
            50% { box-shadow: 0 0 30px rgba(245, 158, 11, 0.5), 0 0 60px rgba(245, 158, 11, 0.2); }
        }
        
        /* Floating Card Animations */
        @keyframes floatCard1 {
            0%, 100% { transform: translate(0, 0) rotate(-3deg) scale(1); }
            50% { transform: translate(15px, -20px) rotate(3deg) scale(1.05); }
        }
        
        @keyframes floatCard2 {
            0%, 100% { transform: translate(0, 0) rotate(2deg) scale(1); }
            50% { transform: translate(-12px, -18px) rotate(-2deg) scale(1.08); }
        }
        
        @keyframes floatCard3 {
            0%, 100% { transform: translate(0, 0) rotate(-2deg) scale(1); }
            50% { transform: translate(18px, -15px) rotate(4deg) scale(1.06); }
        }
        
        @keyframes floatCard4 {
            0%, 100% { transform: translate(0, 0) rotate(1deg) scale(1); }
            50% { transform: translate(-10px, -22px) rotate(-3deg) scale(1.07); }
        }
        
        .float-card-1 { animation: floatCard1 8s ease-in-out infinite; }
        .float-card-2 { animation: floatCard2 9s ease-in-out infinite 0.5s; }
        .float-card-3 { animation: floatCard3 7s ease-in-out infinite 1s; }
        .float-card-4 { animation: floatCard4 10s ease-in-out infinite 1.5s; }
        
        /* Image Overlay */
        .image-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(15, 23, 42, 0.4));
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        
        .image-overlay:hover {
            opacity: 1;
        }
        
        /* Depth Shadow */
        .depth-shadow {
            box-shadow: 
                0 10px 30px -5px rgba(0, 0, 0, 0.5),
                0 20px 40px -10px rgba(245, 158, 11, 0.2),
                0 0 0 1px rgba(255, 255, 255, 0.05);
        }
        
        /* Overlay Gradient */
        .overlay-gradient {
            background: linear-gradient(135deg, 
                rgba(15, 23, 42, 0.98) 0%, 
                rgba(15, 23, 42, 0.85) 40%, 
                rgba(15, 23, 42, 0.6) 100%);
        }
        
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px #1e293b inset;
            -webkit-text-fill-color: #fff;
            transition: background-color 5000s ease-in-out 0s;
        }
        
        /* Stats Ticker */
        @keyframes slideUp {
            0%, 100% { transform: translateY(0); opacity: 1; }
            45% { transform: translateY(0); opacity: 1; }
            50% { transform: translateY(-100%); opacity: 0; }
            55% { transform: translateY(100%); opacity: 0; }
            60% { transform: translateY(0); opacity: 1; }
        }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen overflow-x-hidden">
    <!-- Background Video -->
    <div class="fixed inset-0 z-0">
        <video autoplay muted loop playsinline class="w-full h-full object-cover kenburns opacity-40">
            <source src="{{ asset('assets/img/showcase/1a5868ca2539d29b13300f52ab0a2e15.mp4') }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 overlay-gradient"></div>
        <div class="absolute inset-0 bg-grid"></div>
    </div>
    
    <!-- Floating Background Images -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden">
        <!-- Top Right Cluster -->
        <div class="absolute top-16 right-8 lg:right-16 float-card-1">
            <div class="cyber-border pulse-glow depth-shadow">
                <div class="relative rounded-xl overflow-hidden">
                    <img src="{{ asset('assets/img/showcase/mb1.jpg') }}" 
                         alt="Business Partnership" 
                         class="w-40 h-32 lg:w-56 lg:h-44 object-cover opacity-85">
                    <div class="image-overlay rounded-xl"></div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Right Large Card -->
        <div class="absolute bottom-24 right-12 lg:right-24 float-card-2 hidden md:block">
            <div class="cyber-border pulse-glow depth-shadow">
                <div class="relative rounded-xl overflow-hidden">
                    <img src="{{ asset('assets/img/showcase/mb2.jpg') }}" 
                         alt="Property Investment" 
                         class="w-48 h-36 lg:w-64 lg:h-48 object-cover opacity-85">
                    <div class="image-overlay rounded-xl"></div>
                </div>
            </div>
        </div>
        
        <!-- Middle Right Card -->
        <div class="absolute top-1/3 right-4 lg:right-12 float-card-3 hidden lg:block">
            <div class="cyber-border pulse-glow depth-shadow">
                <div class="relative rounded-xl overflow-hidden">
                    <img src="{{ asset('assets/img/showcase/mb3.jpg') }}" 
                         alt="Real Estate Growth" 
                         class="w-44 h-36 object-cover opacity-80">
                    <div class="image-overlay rounded-xl"></div>
                </div>
            </div>
        </div>
        
        <!-- Left Side Accent -->
        <div class="absolute bottom-40 left-8 lg:left-16 float-card-4 hidden xl:block">
            <div class="cyber-border pulse-glow depth-shadow">
                <div class="relative rounded-xl overflow-hidden">
                    <img src="{{ asset('assets/img/showcase/videoframe_4633.png') }}" 
                         alt="Investment Dashboard" 
                         class="w-36 h-28 object-cover opacity-70">
                    <div class="image-overlay rounded-xl"></div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="relative z-10 min-h-screen flex">
        <!-- Left Side: Registration Form -->
        <div class="w-full lg:w-3/5 flex items-center justify-center p-4 lg:p-8">
            <div class="w-full max-w-xl">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3 mb-8 group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:shadow-amber-500/50 transition-shadow">
                        <i data-lucide="landmark" class="w-6 h-6 text-white"></i>
                    </div>
                    <span class="text-2xl font-bold">
                        Round<span class="gradient-text">Table</span>
                    </span>
                </a>
                
                <!-- Form Card -->
                <div class="glass-effect rounded-2xl p-8 shadow-2xl">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold mb-2">Create Your Account</h1>
                        <p class="text-slate-400">Join the partnership platform and start building wealth together</p>
                    </div>
                    
                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
                            <div class="flex items-start gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5"></i>
                                <div class="space-y-1">
                                    @foreach($errors->all() as $error)
                                        <p class="text-sm text-red-400">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf
                        
                        <!-- Name Fields -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-slate-300 mb-2">
                                    First Name
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="user" class="w-5 h-5 text-slate-500"></i>
                                    </div>
                                    <input type="text" 
                                           name="first_name" 
                                           id="first_name"
                                           value="{{ old('first_name') }}"
                                           required
                                           class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 transition-all"
                                           placeholder="John">
                                </div>
                            </div>
                            
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-slate-300 mb-2">
                                    Last Name
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <i data-lucide="user" class="w-5 h-5 text-slate-500"></i>
                                    </div>
                                    <input type="text" 
                                           name="last_name" 
                                           id="last_name"
                                           value="{{ old('last_name') }}"
                                           required
                                           class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 transition-all"
                                           placeholder="Doe">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-slate-300 mb-2">
                                Email Address
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="mail" class="w-5 h-5 text-slate-500"></i>
                                </div>
                                <input type="email" 
                                       name="email" 
                                       id="email"
                                       value="{{ old('email') }}"
                                       required
                                       class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 transition-all"
                                       placeholder="you@example.com">
                            </div>
                        </div>
                        
                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-300 mb-2">
                                Phone Number
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="phone" class="w-5 h-5 text-slate-500"></i>
                                </div>
                                <input type="tel" 
                                       name="phone" 
                                       id="phone"
                                       value="{{ old('phone') }}"
                                       class="w-full pl-12 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 transition-all"
                                       placeholder="+27 123 456 7890">
                            </div>
                        </div>
                        
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="lock" class="w-5 h-5 text-slate-500"></i>
                                </div>
                                <input type="password" 
                                       name="password" 
                                       id="password"
                                       required
                                       class="w-full pl-12 pr-12 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 transition-all"
                                       placeholder="Min. 8 characters">
                                <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                    <i data-lucide="eye" id="password-toggle" class="w-5 h-5 text-slate-500 hover:text-slate-300 transition-colors"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-300 mb-2">
                                Confirm Password
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="shield-check" class="w-5 h-5 text-slate-500"></i>
                                </div>
                                <input type="password" 
                                       name="password_confirmation" 
                                       id="password_confirmation"
                                       required
                                       class="w-full pl-12 pr-12 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/20 transition-all"
                                       placeholder="Confirm your password">
                                <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                    <i data-lucide="eye" id="password_confirmation-toggle" class="w-5 h-5 text-slate-500 hover:text-slate-300 transition-colors"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Terms Checkbox -->
                        <div class="flex items-start gap-3">
                            <input type="checkbox" 
                                   name="terms" 
                                   id="terms"
                                   required
                                   class="w-5 h-5 rounded border-slate-700 bg-slate-800/50 text-amber-500 focus:ring-amber-500/20 focus:ring-offset-0 mt-0.5">
                            <label for="terms" class="text-sm text-slate-400">
                                I agree to the 
                                <a href="#" class="text-amber-400 hover:text-amber-300 transition-colors">Terms of Service</a> 
                                and 
                                <a href="#" class="text-amber-400 hover:text-amber-300 transition-colors">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full py-4 px-6 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-semibold rounded-xl transition-all duration-300 shadow-lg shadow-amber-500/30 hover:shadow-amber-500/50 flex items-center justify-center gap-2 group">
                            <span>Create Account</span>
                            <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
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
                
                <!-- Trust Badges -->
                <div class="mt-6 flex items-center justify-center gap-6 text-slate-500 text-sm">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-green-500"></i>
                        <span>SSL Secured</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="lock" class="w-4 h-4 text-amber-500"></i>
                        <span>USDT Payments</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4 text-blue-500"></i>
                        <span>KYC Verified</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Branding & Features (Hidden on Mobile) -->
        <div class="hidden lg:flex lg:w-2/5 relative">
            <!-- Feature Showcase -->
            <div class="absolute inset-0 flex flex-col justify-center pl-8 pr-16">
                <!-- Image Grid Layout -->
                <div class="mb-8 grid grid-cols-2 gap-4">
                    <!-- Large Featured Image -->
                    <div class="col-span-2 cyber-border pulse-glow depth-shadow transform hover:scale-[1.02] transition-all duration-500">
                        <div class="relative rounded-xl overflow-hidden group">
                            <img src="{{ asset('assets/img/showcase/inv5.jpg') }}" 
                                 alt="Property Investment" 
                                 class="w-full h-56 object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-60"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-6">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                                    <span class="text-xs font-semibold text-green-400 uppercase tracking-wider">Active Investments</span>
                                </div>
                                <h3 class="text-lg font-bold text-white">Premium Property Portfolio</h3>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Small Grid Images -->
                    <div class="cyber-border pulse-glow depth-shadow transform hover:scale-[1.05] transition-all duration-500">
                        <div class="relative rounded-xl overflow-hidden group">
                            <img src="{{ asset('assets/img/showcase/inv7.jpg') }}" 
                                 alt="Investment Opportunity" 
                                 class="w-full h-32 object-cover">
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                    </div>
                    
                    <div class="cyber-border pulse-glow depth-shadow transform hover:scale-[1.05] transition-all duration-500">
                        <div class="relative rounded-xl overflow-hidden group">
                            <img src="{{ asset('assets/img/showcase/videoframe_6207.png') }}" 
                                 alt="Partnership Growth" 
                                 class="w-full h-32 object-cover">
                            <div class="absolute inset-0 bg-gradient-to-br from-green-500/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Feature Cards -->
                <div class="space-y-4 mb-6">
                    <h3 class="text-lg font-bold text-white mb-3">Why Join <span class="gradient-text">RoundTable</span>?</h3>
                    
                    <div class="flex items-center gap-3 p-3 glass-effect rounded-lg hover:bg-slate-800/40 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-amber-500/20 to-amber-600/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i data-lucide="users" class="w-5 h-5 text-amber-400"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white text-sm">Partnership Investing</h4>
                            <p class="text-xs text-slate-400">Pool resources for larger opportunities</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 glass-effect rounded-lg hover:bg-slate-800/40 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-green-500/20 to-green-600/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i data-lucide="trending-up" class="w-5 h-5 text-green-400"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white text-sm">Transparent Returns</h4>
                            <p class="text-xs text-slate-400">Real-time investment tracking</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 glass-effect rounded-lg hover:bg-slate-800/40 transition-colors group">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500/20 to-blue-600/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i data-lucide="wallet" class="w-5 h-5 text-blue-400"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white text-sm">USDT Payments</h4>
                            <p class="text-xs text-slate-400">Secure crypto deposits</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Bar -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center p-3 glass-effect rounded-lg hover:bg-slate-800/40 transition-colors">
                        <div class="text-xl font-bold gradient-text">500+</div>
                        <div class="text-[10px] text-slate-400 uppercase tracking-wide">Partners</div>
                    </div>
                    <div class="text-center p-3 glass-effect rounded-lg hover:bg-slate-800/40 transition-colors">
                        <div class="text-xl font-bold text-green-400">$2M+</div>
                        <div class="text-[10px] text-slate-400 uppercase tracking-wide">Invested</div>
                    </div>
                    <div class="text-center p-3 glass-effect rounded-lg hover:bg-slate-800/40 transition-colors">
                        <div class="text-xl font-bold text-blue-400">18%</div>
                        <div class="text-[10px] text-slate-400 uppercase tracking-wide">Avg ROI</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Password toggle function
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-toggle');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.setAttribute('data-lucide', 'eye-off');
            } else {
                field.type = 'password';
                icon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
