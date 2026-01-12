<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>RoundTable | Cohort Capital</title>
    
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
        
        .gradient-text {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
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
        
        /* Animations */
        .fade-in { animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        .slide-up { animation: slideUp 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
        
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.3); }
            50% { box-shadow: 0 0 50px rgba(245, 158, 11, 0.6); }
        }
        
        .pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }
        
        @keyframes ken-burns {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }
        
        .ken-burns { animation: ken-burns 30s ease-out forwards; }
        
        .image-card {
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }
        
        .image-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
        }
        
        .cyber-border {
            position: relative;
        }
        
        .cyber-border::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(135deg, #f59e0b, #10b981, #6366f1, #f59e0b);
            background-size: 400% 400%;
            border-radius: inherit;
            z-index: -1;
            animation: border-animation 8s ease infinite;
        }
        
        @keyframes border-animation {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .video-overlay::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, rgba(2, 6, 23, 0.3) 0%, rgba(2, 6, 23, 0.8) 100%);
        }
    </style>
</head>
<body class="bg-slate-950 text-white overflow-x-hidden antialiased selection:bg-amber-200 selection:text-amber-900 font-sans min-h-screen">
    <!-- Hero Background Video -->
    <div class="fixed inset-0 z-0">
        <video autoplay muted loop playsinline class="w-full h-full object-cover ken-burns opacity-40">
            <source src="{{ asset('assets/img/showcase/1a5868ca2539d29b13300f52ab0a2e15.mp4') }}" type="video/mp4">
        </video>
        <div class="absolute inset-0 bg-gradient-to-b from-slate-950/70 via-slate-950/90 to-slate-950"></div>
    </div>
    
    <!-- Background Pattern -->
    <div class="fixed inset-0 bg-grid pointer-events-none z-10"></div>
    
    <!-- Gradient Orbs -->
    <div class="fixed top-20 right-20 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none animate-float z-10"></div>
    <div class="fixed bottom-20 left-20 w-80 h-80 bg-amber-600/10 rounded-full blur-3xl pointer-events-none animate-float z-10" style="animation-delay: -3s;"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 bg-slate-950/80 backdrop-blur-xl border-b border-slate-800/50">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-slate-900 font-extrabold shadow-lg shadow-amber-500/20">R</div>
                    <div>
                        <span class="text-xl font-bold tracking-tight block leading-none">RoundTable</span>
                        <span class="text-[10px] text-slate-400 uppercase tracking-widest font-mono">Cohort Capital</span>
                    </div>
                </div>
                
                <!-- Desktop Nav -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('cohorts.index') }}" class="text-sm text-slate-400 hover:text-white transition-colors">Browse Cohorts</a>
                    <a href="#how-it-works" class="text-sm text-slate-400 hover:text-white transition-colors">How It Works</a>
                    <a href="#features" class="text-sm text-slate-400 hover:text-white transition-colors">Features</a>
                </div>
                
                <!-- Auth Buttons -->
                <div class="flex items-center space-x-3">
                    @auth
                        <a href="{{ route('member.dashboard') }}" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white text-sm font-bold rounded-xl hover:from-amber-600 hover:to-amber-700 transition-all shadow-lg shadow-amber-900/20">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-slate-400 hover:text-white transition-colors hidden sm:block">Sign In</a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-amber-600 text-white text-sm font-bold rounded-xl hover:from-amber-600 hover:to-amber-700 transition-all shadow-lg shadow-amber-900/20">
                            Get Started
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="min-h-screen flex items-center justify-center pt-20 px-6 relative z-20">
        <div class="max-w-7xl mx-auto slide-up">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <!-- Left: Text Content -->
                <div class="text-center lg:text-left">
                    <!-- Badge -->
                    <div class="inline-flex items-center space-x-2 bg-slate-800/50 border border-slate-700 px-4 py-2 rounded-full mb-8">
                        <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                        <span class="text-xs font-mono text-slate-300">Algorithmic Funding Protocol</span>
                    </div>
                    
                    <!-- Main Headline -->
                    <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-6 leading-tight">
                        Cooperative Partnership<br>
                        <span class="gradient-text">Made Simple</span>
                    </h1>
                    
                    <p class="text-xl text-slate-400 max-w-xl mb-12 leading-relaxed">
                        Join cohorts of partners pooling capital for asset-backed projects.
                        Transparent, secure, and designed for collective success.
                    </p>
                    
                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center lg:items-start justify-center lg:justify-start space-y-4 sm:space-y-0 sm:space-x-4 mb-12">
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-bold rounded-xl text-lg hover:from-amber-600 hover:to-amber-700 transition-all shadow-xl shadow-amber-900/30 flex items-center justify-center space-x-2 group pulse-glow">
                            <span>Join as a Partner</span>
                            <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                        </a>
                        <a href="{{ route('cohorts.index') }}" class="w-full sm:w-auto px-8 py-4 bg-slate-800/80 backdrop-blur text-white font-bold rounded-xl text-lg hover:bg-slate-700 transition-all border border-slate-700 flex items-center justify-center space-x-2">
                            <span>Browse Cohorts</span>
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </a>
                    </div>
                    
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-4 gap-3 max-w-lg mx-auto lg:mx-0">
                        <div class="bg-slate-800/30 backdrop-blur border border-slate-700/50 p-4 rounded-xl text-center">
                            <div class="text-2xl font-bold font-mono text-amber-400 mb-1">$3k</div>
                            <div class="text-[10px] text-slate-500 uppercase font-bold">Min Entry</div>
                        </div>
                        <div class="bg-slate-800/30 backdrop-blur border border-slate-700/50 p-4 rounded-xl text-center">
                            <div class="text-2xl font-bold font-mono text-emerald-400 mb-1">18%</div>
                            <div class="text-[10px] text-slate-500 uppercase font-bold">Avg. Yield</div>
                        </div>
                        <div class="bg-slate-800/30 backdrop-blur border border-slate-700/50 p-4 rounded-xl text-center">
                            <div class="text-2xl font-bold font-mono text-white mb-1">50+</div>
                            <div class="text-[10px] text-slate-500 uppercase font-bold">Partners</div>
                        </div>
                        <div class="bg-slate-800/30 backdrop-blur border border-slate-700/50 p-4 rounded-xl text-center">
                            <div class="text-2xl font-bold font-mono text-white mb-1">100%</div>
                            <div class="text-[10px] text-slate-500 uppercase font-bold">Transparent</div>
                        </div>
                    </div>
                </div>
                
                <!-- Right: Image Showcase -->
                <div class="relative hidden lg:block">
                    <!-- Main Featured Image -->
                    <div class="relative z-20">
                        <div class="cyber-border rounded-3xl overflow-hidden image-card">
                            <img src="{{ asset('assets/img/showcase/inv5.jpg') }}" alt="Investment Partnership" class="w-full h-80 object-cover">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                            <div class="absolute bottom-6 left-6 right-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="text-xs text-amber-400 font-mono mb-1">FEATURED PROJECT</div>
                                        <div class="text-lg font-bold text-white">Real Estate Portfolio</div>
                                    </div>
                                    <div class="px-3 py-1.5 bg-emerald-500/20 border border-emerald-500/30 rounded-lg">
                                        <span class="text-emerald-400 text-sm font-bold">+24.5%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Cards -->
                    <div class="absolute -top-8 -right-8 z-30 image-card">
                        <div class="w-40 h-40 rounded-2xl overflow-hidden border-2 border-slate-700 shadow-2xl">
                            <img src="{{ asset('assets/img/showcase/videoframe_14198.png') }}" alt="Tech Investment" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-transparent"></div>
                        </div>
                    </div>
                    
                    <div class="absolute -bottom-4 -left-8 z-10 image-card">
                        <div class="w-48 h-32 rounded-2xl overflow-hidden border-2 border-slate-700 shadow-2xl">
                            <img src="{{ asset('assets/img/showcase/inv7.jpg') }}" alt="Business Partnership" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/20 to-transparent"></div>
                        </div>
                    </div>
                    
                    <!-- Tech Elements -->
                    <div class="absolute top-1/4 -left-16 w-12 h-12 border-2 border-amber-500/30 rounded-xl rotate-12 animate-float"></div>
                    <div class="absolute bottom-1/4 -right-12 w-8 h-8 border-2 border-emerald-500/30 rounded-lg -rotate-12 animate-float" style="animation-delay: -2s;"></div>
                    <div class="absolute top-10 left-1/4 w-3 h-3 bg-amber-400 rounded-full pulse-glow"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-24 px-6 relative z-20">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold mb-4">How It Works</h2>
                <p class="text-slate-400 max-w-xl mx-auto">Join the collective in three simple steps</p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="bg-slate-800/30 backdrop-blur border border-slate-700/50 p-8 rounded-3xl relative group hover:border-amber-500/30 transition-all image-card overflow-hidden">
                    <div class="absolute inset-0 opacity-20">
                        <img src="{{ asset('assets/img/showcase/videoframe_28362.png') }}" alt="" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-slate-900/60"></div>
                    </div>
                    <div class="relative z-10">
                        <div class="absolute -top-4 -left-4 w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-slate-900 font-bold text-lg shadow-lg shadow-amber-900/20">1</div>
                        <div class="w-14 h-14 bg-slate-800 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-500/10 transition-colors">
                            <i data-lucide="user-plus" class="w-7 h-7 text-amber-400"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Create Account</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Sign up and complete KYC verification to become a verified member of the collective.</p>
                    </div>
                </div>
                
                <!-- Step 2 -->
                <div class="bg-slate-800/30 backdrop-blur border border-slate-700/50 p-8 rounded-3xl relative group hover:border-amber-500/30 transition-all image-card overflow-hidden">
                    <div class="absolute inset-0 opacity-20">
                        <img src="{{ asset('assets/img/showcase/videoframe_4633.png') }}" alt="" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-slate-900/60"></div>
                    </div>
                    <div class="relative z-10">
                        <div class="absolute -top-4 -left-4 w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-slate-900 font-bold text-lg shadow-lg shadow-amber-900/20">2</div>
                        <div class="w-14 h-14 bg-slate-800 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-500/10 transition-colors">
                            <i data-lucide="search" class="w-7 h-7 text-amber-400"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Browse Cohorts</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Explore asset-backed investment opportunities with transparent projections and risk profiles.</p>
                    </div>
                </div>
                
                <!-- Step 3 -->
                <div class="bg-slate-800/30 backdrop-blur border border-slate-700/50 p-8 rounded-3xl relative group hover:border-amber-500/30 transition-all image-card overflow-hidden">
                    <div class="absolute inset-0 opacity-20">
                        <img src="{{ asset('assets/img/showcase/videoframe_6207.png') }}" alt="" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-slate-900/60"></div>
                    </div>
                    <div class="relative z-10">
                        <div class="absolute -top-4 -left-4 w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-slate-900 font-bold text-lg shadow-lg shadow-amber-900/20">3</div>
                        <div class="w-14 h-14 bg-slate-800 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-amber-500/10 transition-colors">
                            <i data-lucide="trending-up" class="w-7 h-7 text-amber-400"></i>
                        </div>
                        <h3 class="text-xl font-bold mb-3">Contribute & Earn</h3>
                        <p class="text-slate-400 text-sm leading-relaxed">Reserve your seat, contribute capital, and track performance through your dashboard.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Portfolio Showcase Section -->
    <section class="py-24 px-6 relative z-20 overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <div class="inline-flex items-center space-x-2 bg-amber-500/10 border border-amber-500/30 px-4 py-2 rounded-full mb-6">
                    <i data-lucide="image" class="w-4 h-4 text-amber-400"></i>
                    <span class="text-xs font-mono text-amber-400 uppercase">Portfolio Showcase</span>
                </div>
                <h2 class="text-4xl font-extrabold mb-4">Asset-Backed Opportunities</h2>
                <p class="text-slate-400 max-w-xl mx-auto">Real projects, real returns, transparent tracking</p>
            </div>
            
            <!-- Masonry Gallery -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Large Feature -->
                <div class="col-span-2 row-span-2 image-card group">
                    <div class="relative h-full min-h-[400px] rounded-3xl overflow-hidden cyber-border">
                        <img src="{{ asset('assets/img/showcase/1d1c59e10dc609feb4988bd1245d765e.jpg') }}" alt="Premium Investment" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-transparent"></div>
                        <div class="absolute bottom-6 left-6 right-6">
                            <div class="flex items-center space-x-2 mb-3">
                                <span class="px-2 py-1 bg-emerald-500/20 border border-emerald-500/30 rounded-md text-emerald-400 text-xs font-bold">ACTIVE</span>
                                <span class="px-2 py-1 bg-slate-800/80 rounded-md text-slate-400 text-xs font-mono">Real Estate</span>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-2">Premium Property Fund</h3>
                            <div class="flex items-center justify-between">
                                <div class="text-slate-400 text-sm">12-month duration</div>
                                <div class="text-emerald-400 font-bold text-lg">+18.5% APY</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Smaller Cards -->
                <div class="image-card group">
                    <div class="relative h-48 rounded-2xl overflow-hidden">
                        <img src="{{ asset('assets/img/showcase/6371eb0e5abb1bf3b103a54f488be0df.jpg') }}" alt="Tech Investment" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="text-xs text-amber-400 font-mono mb-1">EQUIPMENT</div>
                            <div class="text-sm font-bold text-white">Tech Leasing</div>
                        </div>
                    </div>
                </div>
                
                <div class="image-card group">
                    <div class="relative h-48 rounded-2xl overflow-hidden">
                        <img src="{{ asset('assets/img/showcase/inv7.jpg') }}" alt="Business Partnership" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="text-xs text-purple-400 font-mono mb-1">BUSINESS</div>
                            <div class="text-sm font-bold text-white">SME Growth</div>
                        </div>
                    </div>
                </div>
                
                <div class="image-card group">
                    <div class="relative h-48 rounded-2xl overflow-hidden">
                        <img src="{{ asset('assets/img/showcase/videoframe_14198.png') }}" alt="Energy Project" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="text-xs text-emerald-400 font-mono mb-1">RENEWABLE</div>
                            <div class="text-sm font-bold text-white">Solar Farm</div>
                        </div>
                    </div>
                </div>
                
                <div class="image-card group">
                    <div class="relative h-48 rounded-2xl overflow-hidden">
                        <img src="{{ asset('assets/img/showcase/inv5.jpg') }}" alt="Property Fund" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-transparent to-transparent"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <div class="text-xs text-blue-400 font-mono mb-1">PROPERTY</div>
                            <div class="text-sm font-bold text-white">Urban Dev</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 px-6 bg-slate-900/50 relative z-20">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-extrabold mb-4">Platform Features</h2>
                <p class="text-slate-400 max-w-xl mx-auto">Built for transparency and collective success</p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="bg-slate-800/30 border border-slate-700/50 p-6 rounded-2xl image-card">
                    <div class="w-12 h-12 bg-emerald-500/10 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="shield-check" class="w-6 h-6 text-emerald-400"></i>
                    </div>
                    <h4 class="font-bold mb-2">KYC Verified Members</h4>
                    <p class="text-sm text-slate-400">All members undergo identity verification for a trusted community.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-slate-800/30 border border-slate-700/50 p-6 rounded-2xl image-card">
                    <div class="w-12 h-12 bg-blue-500/10 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="lock" class="w-6 h-6 text-blue-400"></i>
                    </div>
                    <h4 class="font-bold mb-2">Escrow Protection</h4>
                    <p class="text-sm text-slate-400">Capital held in escrow until MVC threshold is met.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-slate-800/30 border border-slate-700/50 p-6 rounded-2xl image-card">
                    <div class="w-12 h-12 bg-purple-500/10 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="vote" class="w-6 h-6 text-purple-400"></i>
                    </div>
                    <h4 class="font-bold mb-2">Democratic Voting</h4>
                    <p class="text-sm text-slate-400">Weighted voting on key decisions based on contribution.</p>
                </div>
                
                <!-- Feature 4 -->
                <div class="bg-slate-800/30 border border-slate-700/50 p-6 rounded-2xl image-card">
                    <div class="w-12 h-12 bg-amber-500/10 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="bitcoin" class="w-6 h-6 text-amber-400"></i>
                    </div>
                    <h4 class="font-bold mb-2">USDT Crypto Payments</h4>
                    <p class="text-sm text-slate-400">Instant deposits and withdrawals via TRC20/BEP20/ERC20.</p>
                </div>
                
                <!-- Feature 5 -->
                <div class="bg-slate-800/30 border border-slate-700/50 p-6 rounded-2xl image-card">
                    <div class="w-12 h-12 bg-rose-500/10 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="pie-chart" class="w-6 h-6 text-rose-400"></i>
                    </div>
                    <h4 class="font-bold mb-2">50/50 Profit Split</h4>
                    <p class="text-sm text-slate-400">Automatic weekly distributions every Friday.</p>
                </div>
                
                <!-- Feature 6 -->
                <div class="bg-slate-800/30 border border-slate-700/50 p-6 rounded-2xl image-card">
                    <div class="w-12 h-12 bg-cyan-500/10 rounded-xl flex items-center justify-center mb-4">
                        <i data-lucide="smartphone" class="w-6 h-6 text-cyan-400"></i>
                    </div>
                    <h4 class="font-bold mb-2">Mobile Ready</h4>
                    <p class="text-sm text-slate-400">Track your portfolio and vote on the go.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 px-6 relative z-20">
        <div class="max-w-4xl mx-auto">
            <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 rounded-3xl p-12 text-center relative overflow-hidden image-card">
                <!-- Background Image -->
                <div class="absolute inset-0 opacity-10">
                    <img src="{{ asset('assets/img/showcase/videoframe_28362.png') }}" alt="" class="w-full h-full object-cover">
                </div>
                <div class="absolute -right-20 -top-20 w-64 h-64 bg-amber-500/10 rounded-full blur-3xl"></div>
                <div class="absolute -left-20 -bottom-20 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center space-x-2 bg-amber-500/10 border border-amber-500/30 px-4 py-2 rounded-full mb-6">
                        <i data-lucide="rocket" class="w-4 h-4 text-amber-400"></i>
                        <span class="text-xs font-mono text-amber-400 uppercase">Start Today</span>
                    </div>
                    <h2 class="text-4xl font-extrabold mb-4">Ready to Join the Collective?</h2>
                    <p class="text-slate-400 max-w-xl mx-auto mb-8">Start your partnership journey today with as little as $3,000 USDT.</p>
                    <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-4">
                        <a href="{{ route('register') }}" class="px-8 py-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-bold rounded-xl text-lg hover:from-amber-600 hover:to-amber-700 transition-all shadow-xl shadow-amber-900/30 flex items-center space-x-2 pulse-glow">
                            <span>Create Free Account</span>
                            <i data-lucide="arrow-right" class="w-5 h-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 px-6 border-t border-slate-800 relative z-20">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-6 md:space-y-0">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-slate-900 font-bold shadow-lg shadow-amber-500/20">R</div>
                    <span class="font-bold">RoundTable</span>
                </div>
                
                <!-- Links -->
                <div class="flex items-center space-x-8 text-sm text-slate-400">
                    <a href="#" class="hover:text-white transition-colors">Terms</a>
                    <a href="#" class="hover:text-white transition-colors">Privacy</a>
                    <a href="#" class="hover:text-white transition-colors">Support</a>
                </div>
                
                <!-- Security Badge -->
                <div class="flex items-center space-x-2 text-xs text-emerald-400 font-mono">
                    <i data-lucide="shield-check" class="w-4 h-4"></i>
                    <span>AES-256 Encrypted</span>
                </div>
            </div>
            
            <div class="mt-8 pt-8 border-t border-slate-800 text-center">
                <p class="text-xs text-slate-500">© {{ date('Y') }} RoundTable. All rights reserved. RoundTable is an algorithmic funding protocol. Capital is deployed at your own risk.</p>
            </div>
        </div>
    </footer>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>
