<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Sign In - RoundTable</title>
    
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
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(245, 158, 11, 0.3); }
            50% { box-shadow: 0 0 40px rgba(245, 158, 11, 0.5); }
        }
        
        .pulse-glow {
            animation: pulse-glow 3s ease-in-out infinite;
        }
        
        @keyframes slide-up {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .slide-up {
            animation: slide-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        
        .image-overlay::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(15, 23, 42, 0.4) 50%, rgba(15, 23, 42, 0.8) 100%);
        }
        
        @keyframes ken-burns {
            0% { transform: scale(1); }
            100% { transform: scale(1.1); }
        }
        
        .ken-burns {
            animation: ken-burns 20s ease-out forwards;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-950 text-slate-100 font-sans">
    <!-- Background Pattern -->
    <div class="fixed inset-0 bg-grid pointer-events-none"></div>
    
    <!-- Gradient Orbs -->
    <div class="fixed top-20 right-20 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none animate-float"></div>
    <div class="fixed bottom-20 left-20 w-80 h-80 bg-amber-600/10 rounded-full blur-3xl pointer-events-none animate-float" style="animation-delay: -3s;"></div>
    
    <div class="min-h-screen flex">
        <!-- Left Side: Branding with Video/Image Background -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <!-- Background Video -->
            <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover ken-burns">
                <source src="<?php echo e(Storage::url('cohorts/images/1a5868ca2539d29b13300f52ab0a2e15.mp4')); ?>" type="video/mp4">
            </video>
            
            <!-- Overlay Gradient -->
            <div class="absolute inset-0 bg-gradient-to-br from-slate-950/95 via-slate-900/80 to-slate-950/90"></div>
            
            <!-- Animated Grid Lines -->
            <div class="absolute inset-0 bg-grid opacity-30"></div>
            
            <!-- Floating Tech Elements -->
            <div class="absolute top-20 right-10 w-32 h-32 border border-amber-500/20 rounded-2xl rotate-12 animate-float"></div>
            <div class="absolute bottom-32 right-20 w-20 h-20 border border-emerald-500/20 rounded-xl -rotate-6 animate-float" style="animation-delay: -2s;"></div>
            <div class="absolute top-1/3 right-1/4 w-3 h-3 bg-amber-400 rounded-full pulse-glow"></div>
            
            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center px-16 slide-up">
                <!-- Logo -->
                <div class="flex items-center gap-3 mb-12">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-xl shadow-amber-500/30 pulse-glow">
                        <i data-lucide="coins" class="w-7 h-7 text-slate-900"></i>
                    </div>
                    <span class="text-3xl font-bold text-white tracking-tight">Round<span class="gradient-text">Table</span></span>
                </div>
                
                <!-- Tagline -->
                <h1 class="text-5xl font-extrabold text-white mb-6 leading-tight">
                    Cooperative Partnership<br>
                    <span class="gradient-text">Made Simple</span>
                </h1>
                
                <p class="text-lg text-slate-300 mb-12 max-w-md leading-relaxed">
                    Join cohorts of partners pooling capital for property partnerships. Transparent, secure, and collaborative.
                </p>
                
                <!-- Features with Icons -->
                <div class="space-y-5">
                    <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center group-hover:bg-emerald-500/30 transition-all">
                            <i data-lucide="shield-check" class="w-5 h-5 text-emerald-400"></i>
                        </div>
                        <div>
                            <span class="text-white font-semibold block">KYC Verified Members</span>
                            <span class="text-slate-400 text-sm">Secure identity verification</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-xl bg-blue-500/20 border border-blue-500/30 flex items-center justify-center group-hover:bg-blue-500/30 transition-all">
                            <i data-lucide="lock" class="w-5 h-5 text-blue-400"></i>
                        </div>
                        <div>
                            <span class="text-white font-semibold block">USDT Crypto Payments</span>
                            <span class="text-slate-400 text-sm">Instant, borderless transactions</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 group">
                        <div class="w-12 h-12 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center group-hover:bg-purple-500/30 transition-all">
                            <i data-lucide="chart-line" class="w-5 h-5 text-purple-400"></i>
                        </div>
                        <div>
                            <span class="text-white font-semibold block">Real-Time Portfolio Tracking</span>
                            <span class="text-slate-400 text-sm">Monitor your investments 24/7</span>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Bar -->
                <div class="mt-12 flex items-center gap-8 py-6 border-t border-slate-700/50">
                    <div>
                        <div class="text-2xl font-bold text-amber-400 font-mono">$3k</div>
                        <div class="text-xs text-slate-500 uppercase">Min Entry</div>
                    </div>
                    <div class="w-px h-10 bg-slate-700"></div>
                    <div>
                        <div class="text-2xl font-bold text-emerald-400 font-mono">50+</div>
                        <div class="text-xs text-slate-500 uppercase">Partners</div>
                    </div>
                    <div class="w-px h-10 bg-slate-700"></div>
                    <div>
                        <div class="text-2xl font-bold text-white font-mono">100%</div>
                        <div class="text-xs text-slate-500 uppercase">Transparent</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
            <div class="w-full max-w-md">
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
                        <h2 class="text-2xl font-bold text-white mb-2">Welcome back</h2>
                        <p class="text-slate-400">Sign in to access your portfolio</p>
                    </div>
                    
                    <!-- Error Messages -->
                    <?php if($errors->any()): ?>
                        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30">
                            <div class="flex items-center gap-2 text-red-400">
                                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                                <span class="text-sm font-medium">Please fix the following errors:</span>
                            </div>
                            <ul class="mt-2 text-sm text-red-300 list-disc list-inside">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-5">
                        <?php echo csrf_field(); ?>
                        
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
                                    value="<?php echo e(old('email')); ?>"
                                    required 
                                    autofocus
                                    class="w-full pl-11 pr-4 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all"
                                    placeholder="you@example.com"
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
                                    class="w-full pl-11 pr-12 py-3 bg-slate-800/50 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-500/50 focus:border-amber-500 transition-all"
                                    placeholder="••••••••"
                                >
                                <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                    <i data-lucide="eye" id="eye-icon" class="w-4 h-4 text-slate-500 hover:text-slate-400"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-amber-500 focus:ring-amber-500/50">
                                <span class="text-sm text-slate-400">Remember me</span>
                            </label>
                            <a href="#" class="text-sm text-amber-400 hover:text-amber-300 transition-colors">Forgot password?</a>
                        </div>
                        
                        <!-- Submit Button -->
                        <button 
                            type="submit"
                            class="w-full py-3 px-4 bg-gradient-to-r from-amber-500 to-amber-600 text-white font-semibold rounded-xl hover:from-amber-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500/50 transform hover:scale-[1.02] active:scale-[0.98] transition-all duration-200"
                        >
                            Sign In
                        </button>
                    </form>
                    
                    <!-- Divider -->
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-700"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-3 bg-slate-900 text-slate-500">or</span>
                        </div>
                    </div>
                    
                    <!-- Register Link -->
                    <p class="text-center text-slate-400">
                        Don't have an account? 
                        <a href="<?php echo e(route('register')); ?>" class="text-amber-400 hover:text-amber-300 font-medium transition-colors">
                            Create account
                        </a>
                    </p>
                </div>
                
                <!-- Footer -->
                <p class="text-center text-xs text-slate-500 mt-6">
                    By signing in, you agree to our 
                    <a href="#" class="text-slate-400 hover:text-slate-300">Terms of Service</a> and 
                    <a href="#" class="text-slate-400 hover:text-slate-300">Privacy Policy</a>
                </p>
            </div>
        </div>
    </div>
    
    <script>
        // Initialize Lucide icons
        lucide.createIcons();
        
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>
</body>
</html>
<?php /**PATH C:\wamp64\www\The round table\The Table\resources\views/auth/login.blade.php ENDPATH**/ ?>