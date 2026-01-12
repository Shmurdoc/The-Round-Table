<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Create Account - RoundTable Partnership Platform</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    
    <style>
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-text {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .glass-effect {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(251, 191, 36, 0.1);
        }
        
        .bg-grid {
            background-image: 
                linear-gradient(rgba(251, 191, 36, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(251, 191, 36, 0.03) 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: center center;
        }
        
        /* Ken Burns Animation */
        @keyframes kenburns {
            0% { transform: scale(1) translate(0, 0); }
            50% { transform: scale(1.1) translate(-2%, 1%); }
            100% { transform: scale(1) translate(0, 0); }
        }
        
        .kenburns {
            animation: kenburns 25s ease-in-out infinite;
        }
        
        /* Cyber Border with Gradient */
        .cyber-border {
            position: relative;
            border-radius: 0.75rem;
            padding: 2px;
            background: linear-gradient(135deg, #fbbf24, #f59e0b, #d97706);
            background-size: 200% 200%;
            animation: gradientShift 4s ease infinite;
        }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        .cyber-border-inner {
            border-radius: 0.625rem;
            overflow: hidden;
            background: rgba(15, 23, 42, 0.4);
        }
        
        /* Pulse Glow Effect */
        .pulse-glow {
            animation: pulseGlow 3s ease-in-out infinite;
        }
        
        @keyframes pulseGlow {
            0%, 100% { 
                box-shadow: 0 0 20px rgba(251, 191, 36, 0.3),
                            0 0 40px rgba(251, 191, 36, 0.2),
                            0 10px 30px rgba(0, 0, 0, 0.4);
            }
            50% { 
                box-shadow: 0 0 30px rgba(251, 191, 36, 0.5),
                            0 0 60px rgba(251, 191, 36, 0.3),
                            0 15px 40px rgba(0, 0, 0, 0.5);
            }
        }
        
        /* Floating Card Animations */
        @keyframes floatCard1 {
            0%, 100% { transform: translate(0, 0) rotate(-2deg); }
            50% { transform: translate(10px, -15px) rotate(2deg); }
        }
        
        @keyframes floatCard2 {
            0%, 100% { transform: translate(0, 0) rotate(1deg); }
            50% { transform: translate(-8px, -12px) rotate(-2deg); }
        }
        
        @keyframes floatCard3 {
            0%, 100% { transform: translate(0, 0) rotate(-1deg); }
            50% { transform: translate(12px, -18px) rotate(3deg); }
        }
        
        .float-card-1 { animation: floatCard1 8s ease-in-out infinite; }
        .float-card-2 { animation: floatCard2 9s ease-in-out infinite 1s; }
        .float-card-3 { animation: floatCard3 7s ease-in-out infinite 2s; }
        
        /* Overlay Gradient */
        .overlay-gradient {
            background: linear-gradient(135deg, 
                rgba(15, 23, 42, 0.97) 0%, 
                rgba(15, 23, 42, 0.90) 30%,
                rgba(15, 23, 42, 0.75) 60%,
                rgba(15, 23, 42, 0.60) 100%);
        }
        
        /* Form Input Focus */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0px 1000px #1e293b inset;
            -webkit-text-fill-color: #fff;
            transition: background-color 5000s ease-in-out 0s;
        }
        
        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: rgba(251, 191, 36, 0.5);
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.1);
        }
        
        /* Button Hover Effect */
        .btn-gradient {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            background-size: 200% 200%;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            background-position: 100% 0;
            box-shadow: 0 10px 30px rgba(251, 191, 36, 0.4);
            transform: translateY(-2px);
        }
        
        /* Image Hover Effects */
        .hover-zoom {
            transition: transform 0.5s ease;
        }
        
        .hover-zoom:hover {
            transform: scale(1.05);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 1024px) {
            .floating-images { display: none; }
        }
        
        @media (max-width: 640px) {
            .glass-effect { padding: 1.5rem; }
        }
    </style>
</head>
<body class="bg-slate-950 text-white min-h-screen overflow-x-hidden">
    <!-- Background Video Layer -->
    <div class="fixed inset-0 z-0">
        <video autoplay muted loop playsinline class="w-full h-full object-cover kenburns opacity-30">
            <source src="<?php echo e(asset('assets/img/showcase/1a5868ca2539d29b13300f52ab0a2e15.mp4')); ?>" type="video/mp4">
        </video>
        <div class="absolute inset-0 overlay-gradient"></div>
        <div class="absolute inset-0 bg-grid"></div>
    </div>
    
    <!-- Floating Background Images (Desktop Only) -->
    <div class="fixed inset-0 z-0 pointer-events-none overflow-hidden floating-images">
        <!-- Top Right -->
        <div class="absolute top-20 right-10 float-card-1">
            <div class="cyber-border pulse-glow">
                <div class="cyber-border-inner">
                    <img src="<?php echo e(asset('assets/img/showcase/mb1.jpg')); ?>" 
                         alt="Business Partnership" 
                         class="w-48 h-36 object-cover opacity-80">
                </div>
            </div>
        </div>
        
        <!-- Bottom Right -->
        <div class="absolute bottom-28 right-16 float-card-2">
            <div class="cyber-border pulse-glow">
                <div class="cyber-border-inner">
                    <img src="<?php echo e(asset('assets/img/showcase/mb2.jpg')); ?>" 
                         alt="Property Investment" 
                         class="w-56 h-44 object-cover opacity-80">
                </div>
            </div>
        </div>
        
        <!-- Middle Right -->
        <div class="absolute top-1/2 -translate-y-1/2 right-8 float-card-3">
            <div class="cyber-border pulse-glow">
                <div class="cyber-border-inner">
                    <img src="<?php echo e(asset('assets/img/showcase/mb3.jpg')); ?>" 
                         alt="Real Estate" 
                         class="w-44 h-36 object-cover opacity-75">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="relative z-10 min-h-screen flex flex-col lg:flex-row">
        <!-- Left Side: Registration Form -->
        <div class="w-full lg:w-3/5 flex items-center justify-center p-4 sm:p-6 lg:p-8 py-12">
            <div class="w-full max-w-xl">
                <!-- Logo -->
                <a href="<?php echo e(url('/')); ?>" class="inline-flex items-center gap-3 mb-6 lg:mb-8 group">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-500/30 group-hover:shadow-amber-500/50 transition-all duration-300">
                        <i data-lucide="landmark" class="w-5 h-5 sm:w-6 sm:h-6 text-white"></i>
                    </div>
                    <span class="text-xl sm:text-2xl font-bold">
                        Round<span class="gradient-text">Table</span>
                    </span>
                </a>
                
                <!-- Form Card -->
                <div class="glass-effect rounded-2xl p-6 sm:p-8 shadow-2xl">
                    <div class="mb-6 sm:mb-8">
                        <h1 class="text-2xl sm:text-3xl font-bold mb-2">Create Your Account</h1>
                        <p class="text-sm sm:text-base text-slate-400">Join the partnership platform and start building wealth together</p>
                    </div>
                    
                    <!-- Error Messages -->
                    <?php if($errors->any()): ?>
                        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20">
                            <div class="flex items-start gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-red-400 flex-shrink-0 mt-0.5"></i>
                                <div class="space-y-1">
                                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <p class="text-sm text-red-400"><?php echo e($error); ?></p>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="<?php echo e(route('register')); ?>" class="space-y-4 sm:space-y-5">
                        <?php echo csrf_field(); ?>
                        
                        <!-- Name Fields -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                                           value="<?php echo e(old('first_name')); ?>"
                                           required
                                           class="w-full pl-11 pr-4 py-3 bg-slate-800/60 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 transition-all duration-300"
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
                                           value="<?php echo e(old('last_name')); ?>"
                                           required
                                           class="w-full pl-11 pr-4 py-3 bg-slate-800/60 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 transition-all duration-300"
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
                                       value="<?php echo e(old('email')); ?>"
                                       required
                                       class="w-full pl-11 pr-4 py-3 bg-slate-800/60 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 transition-all duration-300"
                                       placeholder="you@example.com">
                            </div>
                        </div>
                        
                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-slate-300 mb-2">
                                Phone Number <span class="text-slate-500 text-xs">(Optional)</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i data-lucide="phone" class="w-5 h-5 text-slate-500"></i>
                                </div>
                                <input type="tel" 
                                       name="phone" 
                                       id="phone"
                                       value="<?php echo e(old('phone')); ?>"
                                       class="w-full pl-11 pr-4 py-3 bg-slate-800/60 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 transition-all duration-300"
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
                                       class="w-full pl-11 pr-12 py-3 bg-slate-800/60 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 transition-all duration-300"
                                       placeholder="Min. 8 characters">
                                <button type="button" onclick="togglePassword('password')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-slate-300 transition-colors">
                                    <i data-lucide="eye" id="password-toggle" class="w-5 h-5"></i>
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
                                       class="w-full pl-11 pr-12 py-3 bg-slate-800/60 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 transition-all duration-300"
                                       placeholder="Confirm your password">
                                <button type="button" onclick="togglePassword('password_confirmation')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-500 hover:text-slate-300 transition-colors">
                                    <i data-lucide="eye" id="password_confirmation-toggle" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Terms Checkbox -->
                        <div class="flex items-start gap-3 pt-2">
                            <input type="checkbox" 
                                   name="terms" 
                                   id="terms"
                                   required
                                   class="w-5 h-5 rounded border-slate-700 bg-slate-800/50 text-amber-500 focus:ring-amber-500/20 focus:ring-offset-0 mt-0.5 cursor-pointer">
                            <label for="terms" class="text-sm text-slate-400 cursor-pointer select-none">
                                I agree to the 
                                <a href="#" class="text-amber-400 hover:text-amber-300 transition-colors font-medium">Terms of Service</a> 
                                and 
                                <a href="#" class="text-amber-400 hover:text-amber-300 transition-colors font-medium">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" 
                                class="w-full py-3.5 px-6 btn-gradient text-white font-semibold rounded-xl shadow-lg flex items-center justify-center gap-2 group mt-6">
                            <span>Create Account</span>
                            <i data-lucide="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300"></i>
                        </button>
                    </form>
                    
                    <!-- Login Link -->
                    <p class="text-center text-slate-400 mt-6 text-sm">
                        Already have an account? 
                        <a href="<?php echo e(route('login')); ?>" class="text-amber-400 hover:text-amber-300 font-semibold transition-colors">
                            Sign in
                        </a>
                    </p>
                </div>
                
                <!-- Trust Badges -->
                <div class="mt-6 flex flex-wrap items-center justify-center gap-4 sm:gap-6 text-slate-500 text-xs sm:text-sm">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-green-400"></i>
                        <span>SSL Secured</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="lock" class="w-4 h-4 text-amber-400"></i>
                        <span>USDT Payments</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4 text-blue-400"></i>
                        <span>KYC Verified</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Side: Branding & Features (Desktop Only) -->
        <div class="hidden lg:flex lg:w-2/5 relative items-center justify-center p-12">
            <div class="w-full max-w-lg space-y-8">
                <!-- Featured Images Grid -->
                <div class="space-y-4">
                    <!-- Large Featured Image -->
                    <div class="cyber-border pulse-glow">
                        <div class="cyber-border-inner">
                            <div class="relative group overflow-hidden">
                                <img src="<?php echo e(asset('assets/img/showcase/inv5.jpg')); ?>" 
                                     alt="Premium Property Portfolio" 
                                     class="w-full h-64 object-cover hover-zoom">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/50 to-transparent opacity-80"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-6 z-10">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
                                        <span class="text-xs font-semibold text-green-400 uppercase tracking-wider">Active Investments</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-white mb-1">Premium Property Portfolio</h3>
                                    <p class="text-sm text-slate-300">Verified partnership opportunities</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Small Images Grid -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="cyber-border pulse-glow">
                            <div class="cyber-border-inner">
                                <img src="<?php echo e(asset('assets/img/showcase/inv7.jpg')); ?>" 
                                     alt="Investment" 
                                     class="w-full h-40 object-cover hover-zoom">
                            </div>
                        </div>
                        <div class="cyber-border pulse-glow">
                            <div class="cyber-border-inner">
                                <img src="<?php echo e(asset('assets/img/showcase/videoframe_6207.png')); ?>" 
                                     alt="Partnership" 
                                     class="w-full h-40 object-cover hover-zoom">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Features List -->
                <div class="space-y-3">
                    <h3 class="text-lg font-bold text-white mb-4">Why Join <span class="gradient-text">RoundTable</span>?</h3>
                    
                    <div class="flex items-center gap-4 p-4 glass-effect rounded-xl hover:bg-slate-800/50 transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="users" class="w-6 h-6 text-amber-400"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white">Partnership Investing</h4>
                            <p class="text-sm text-slate-400">Pool resources for larger opportunities</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 p-4 glass-effect rounded-xl hover:bg-slate-800/50 transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-green-500/20 to-green-600/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="trending-up" class="w-6 h-6 text-green-400"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white">Transparent Returns</h4>
                            <p class="text-sm text-slate-400">Real-time tracking & distributions</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 p-4 glass-effect rounded-xl hover:bg-slate-800/50 transition-all duration-300 group cursor-pointer">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500/20 to-blue-600/30 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                            <i data-lucide="wallet" class="w-6 h-6 text-blue-400"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-white">USDT Payments</h4>
                            <p class="text-sm text-slate-400">Secure crypto deposits & withdrawals</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="text-center p-4 glass-effect rounded-xl hover:bg-slate-800/50 transition-all duration-300 group cursor-pointer">
                        <div class="text-3xl font-bold gradient-text mb-1 group-hover:scale-110 transition-transform duration-300">500+</div>
                        <div class="text-xs text-slate-400 uppercase tracking-wide">Partners</div>
                    </div>
                    <div class="text-center p-4 glass-effect rounded-xl hover:bg-slate-800/50 transition-all duration-300 group cursor-pointer">
                        <div class="text-3xl font-bold text-green-400 mb-1 group-hover:scale-110 transition-transform duration-300">$2M+</div>
                        <div class="text-xs text-slate-400 uppercase tracking-wide">Invested</div>
                    </div>
                    <div class="text-center p-4 glass-effect rounded-xl hover:bg-slate-800/50 transition-all duration-300 group cursor-pointer">
                        <div class="text-3xl font-bold text-blue-400 mb-1 group-hover:scale-110 transition-transform duration-300">18%</div>
                        <div class="text-xs text-slate-400 uppercase tracking-wide">Avg ROI</div>
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
        
        // Enhanced form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const inputs = form.querySelectorAll('input[required]');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.classList.add('border-red-500/50');
                    } else {
                        this.classList.remove('border-red-500/50');
                    }
                });
            });
        });
    </script>
</body>
</html>
<?php /**PATH C:\wamp64\www\The round table\The Table\resources\views/auth/register.blade.php ENDPATH**/ ?>