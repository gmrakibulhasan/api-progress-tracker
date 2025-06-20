<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Progress Tracker - Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
        }

        @keyframes floating {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }
    </style>
</head>

<body class="gradient-bg min-h-screen flex items-center justify-center px-4">
    <!-- Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute -top-10 -left-10 w-40 h-40 bg-white bg-opacity-10 rounded-full floating"></div>
        <div class="absolute top-20 right-20 w-20 h-20 bg-white bg-opacity-10 rounded-full floating"
            style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 left-20 w-32 h-32 bg-white bg-opacity-10 rounded-full floating"
            style="animation-delay: 2s;"></div>
        <div class="absolute bottom-10 right-10 w-16 h-16 bg-white bg-opacity-10 rounded-full floating"
            style="animation-delay: 0.5s;"></div>
    </div>

    <!-- Login Container -->
    <div class="glass-effect rounded-2xl shadow-2xl w-full max-w-md p-8 relative z-10" x-data="loginForm()">
        <!-- Logo and Header -->
        <div class="text-center mb-8">
            <div
                class="bg-white bg-opacity-20 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 floating">
                <i class="fas fa-code text-white text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">API Progress Tracker</h1>
            <p class="text-white text-opacity-80">Welcome back! Please sign in to continue</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div
                class="bg-red-500 bg-opacity-20 border border-red-400 text-red-100 px-4 py-3 rounded-lg mb-6 backdrop-filter backdrop-blur-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-3"></i>
                    <div>
                        @foreach ($errors->all() as $error)
                            <p class="text-sm">{{ $error }}</p>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('apipt.login') }}" class="space-y-6">
            @csrf

            <!-- Email Field -->
            <div class="space-y-2">
                <label for="email" class="block text-white text-sm font-medium">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-white text-opacity-60"></i>
                    </div>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        x-model="form.email"
                        class="block w-full pl-12 pr-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white placeholder-white placeholder-opacity-60 backdrop-filter backdrop-blur-sm focus:ring-2 focus:ring-white focus:ring-opacity-50 focus:border-white focus:border-opacity-50 transition-all duration-200"
                        placeholder="Enter your email address">
                </div>
            </div>

            <!-- Password Field -->
            <div class="space-y-2">
                <label for="password" class="block text-white text-sm font-medium">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-lock text-white text-opacity-60"></i>
                    </div>
                    <input type="password" id="password" name="password" required x-model="form.password"
                        class="block w-full pl-12 pr-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-lg text-white placeholder-white placeholder-opacity-60 backdrop-filter backdrop-blur-sm focus:ring-2 focus:ring-white focus:ring-opacity-50 focus:border-white focus:border-opacity-50 transition-all duration-200"
                        placeholder="Enter your password">
                </div>
            </div>

            <!-- Login Button -->
            <button type="submit" :disabled="loading"
                class="w-full bg-white bg-opacity-20 hover:bg-opacity-30 text-white font-semibold py-3 px-4 rounded-lg backdrop-filter backdrop-blur-sm border border-white border-opacity-30 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transform transition-all duration-200 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                :class="loading ? 'opacity-50 cursor-not-allowed' : ''">
                <span x-show="!loading" class="flex items-center justify-center">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </span>
                <span x-show="loading" class="flex items-center justify-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Signing in...
                </span>
            </button>
        </form>

        <!-- Demo Credentials -->
        <div class="mt-8 pt-6 border-t border-white border-opacity-20">
            <div class="bg-white bg-opacity-10 rounded-lg p-4 backdrop-filter backdrop-blur-sm">
                <div class="text-center">
                    <p class="text-white text-opacity-80 text-sm mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Demo Account
                    </p>
                    <div class="text-white text-sm font-mono bg-black bg-opacity-20 rounded px-3 py-2">
                        <div>📧 admin@apipt.com</div>
                        <div>🔑 password</div>
                    </div>
                    <button @click="fillDemoCredentials()"
                        class="mt-3 text-white text-opacity-80 hover:text-opacity-100 text-xs underline transition-all duration-200">
                        Click to fill demo credentials
                    </button>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6">
            <p class="text-white text-opacity-60 text-xs">
                © 2025 API Progress Tracker. Built with ❤️ by Rakibul Hasan
            </p>
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                form: {
                    email: '',
                    password: ''
                },
                loading: false,

                fillDemoCredentials() {
                    this.form.email = 'admin@apipt.com';
                    this.form.password = 'password';
                }
            }
        }
    </script>
</body>

</html>
