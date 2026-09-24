<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | StokClean</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Google reCAPTCHA v2 (Indonesian: Saya bukan robot) -->
    <script src="https://www.google.com/recaptcha/api.js?hl=id" async defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f3f4fa] text-slate-800 font-sans flex flex-col justify-between p-4 sm:p-6 md:p-8 antialiased">

    <!-- Top Spacer for vertical balance -->
    <div class="hidden sm:block"></div>

    <!-- MAIN LOGIN CONTAINER -->
    <main class="w-full max-w-[920px] mx-auto bg-white rounded-3xl border border-slate-200/80 shadow-[0_15px_45px_rgba(15,23,42,0.06)] overflow-hidden grid md:grid-cols-2 my-auto">
        <!-- LEFT PANEL: Frosted Glass Card (30% Blur) over Sharp Crisp Warehouse Background -->
        <section class="relative overflow-hidden p-6 sm:p-8 flex flex-col items-center justify-center text-center min-h-[380px] md:min-h-[520px] bg-slate-900 border-b md:border-b-0 md:border-r border-slate-200/60">
            <!-- Background Image (TAJAM / 100% TANPA BLUR) -->
            <img 
                src="{{ asset('images/warehouse.jpg') }}" 
                alt="Background Gudang" 
                class="absolute inset-0 w-full h-full object-cover opacity-95 pointer-events-none"
            >
            <!-- Soft Dark Overlay for Contrast -->
            <div class="absolute inset-0 bg-slate-900/25"></div>

            <!-- CARD STOKCLEAN DENGAN BLUR KACA HALUS (30% FROSTED GLASS BLUR) -->
            <div class="relative z-10 w-full max-w-[310px] bg-white/80 backdrop-blur-[6px] p-7 sm:p-8 rounded-3xl border border-white/70 shadow-[0_20px_50px_rgba(15,23,42,0.22)] flex flex-col items-center justify-center text-center">
                <!-- Logo Box -->
                <div class="w-24 h-24 sm:w-28 sm:h-28 bg-white rounded-2xl border border-slate-200/80 shadow-md flex items-center justify-center mb-5 p-3 transform transition duration-300 hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" alt="StokClean Logo" class="w-[68px] h-[68px] object-contain">
                </div>

                <!-- Brand Name & Description -->
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">StokClean</h2>
                <p class="mt-1.5 text-xs text-slate-600 font-medium max-w-[240px] leading-relaxed">
                    Manajemen Stok Kebersihan &amp; Sanitasi Modern
                </p>

                <!-- Indicator Dots -->
                <div class="mt-5 flex items-center gap-1.5">
                    <span class="h-1.5 w-6 rounded-full bg-[#1B2A4A] shadow-xs"></span>
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                </div>
            </div>
        </section>

        <!-- RIGHT PANEL: Login Form -->
        <section class="bg-white p-8 sm:p-12 flex flex-col justify-center">
            <div class="w-full max-w-[360px] mx-auto">
                
                <!-- Title & Subtitle -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Masuk ke Akun</h1>
                    <p class="mt-1 text-xs text-slate-500 font-normal">Silakan masuk untuk mengelola inventaris gudang</p>
                </div>

                <!-- Alert Messages -->
                @if ($errors->any())
                    <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 flex items-start gap-2.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
                        <div>
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-700 flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                @if (session('lockout_seconds'))
                    <div class="mb-4 flex items-start gap-2.5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800" role="alert">
                        <i data-lucide="timer" class="mt-0.5 h-4 w-4 shrink-0 text-amber-600"></i>
                        <div>
                            <p class="font-semibold">Login sementara dikunci.</p>
                            <p class="mt-0.5">Coba lagi dalam <strong id="lockout-countdown" data-seconds="{{ session('lockout_seconds') }}">00:{{ str_pad(session('lockout_seconds'), 2, '0', STR_PAD_LEFT) }}</strong>.</p>
                        </div>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login.submit') }}" class="space-y-4" id="login-form">
                    @csrf
                    
                    <!-- Role Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Login Sebagai
                        </label>
                        <div class="grid grid-cols-2 gap-1.5 p-1 bg-[#f1f5fa] rounded-xl border border-slate-200/50">
                            <label class="relative flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-bold cursor-pointer transition-all duration-150 text-slate-500 has-[:checked]:bg-white has-[:checked]:text-[#1b2942] has-[:checked]:shadow-sm">
                                <input type="radio" name="role" value="pemilik" class="sr-only" @checked(old('role', 'pemilik') === 'pemilik') required>
                                <i data-lucide="crown" class="w-3.5 h-3.5"></i>
                                <span>Pemilik</span>
                            </label>
                            <label class="relative flex items-center justify-center gap-2 py-2 px-3 rounded-lg text-xs font-bold cursor-pointer transition-all duration-150 text-slate-500 has-[:checked]:bg-white has-[:checked]:text-[#1b2942] has-[:checked]:shadow-sm">
                                <input type="radio" name="role" value="pegawai" class="sr-only" @checked(old('role') === 'pegawai')>
                                <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                                <span>Pegawai</span>
                            </label>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Email Perusahaan atau ID Petugas
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                            </div>
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                autocomplete="email" 
                                placeholder="nama@perusahaan.com" 
                                class="w-full bg-[#f1f5fa] focus:bg-white border border-transparent focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-xs text-slate-800 rounded-xl pl-10 pr-4 py-3 placeholder:text-slate-400 font-medium transition duration-150 outline-none"
                            >
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold text-slate-700">
                                Kata Sandi
                            </label>
                            <a href="#" onclick="alert('Silakan hubungi administrator untuk mereset kata sandi Anda.')" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition">
                                Lupa sandi?
                            </a>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                required 
                                autocomplete="current-password" 
                                placeholder="••••••••••••" 
                                class="w-full bg-[#f1f5fa] focus:bg-white border border-transparent focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-xs text-slate-800 rounded-xl pl-10 pr-10 py-3 placeholder:text-slate-400 font-medium transition duration-150 outline-none"
                            >
                            <button 
                                type="button" 
                                id="toggle-password" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition focus:outline-none"
                                aria-label="Tampilkan password"
                            >
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Google reCAPTCHA Field ("Saya bukan robot") -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Verifikasi Keamanan
                        </label>
                        <div class="overflow-x-auto py-0.5 flex justify-center sm:justify-start">
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        </div>
                        @error('g-recaptcha-response')
                            <p class="mt-1 text-[11px] font-semibold text-red-600 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center pt-1 pb-1">
                        <label class="flex items-center gap-2.5 text-xs text-slate-600 font-medium cursor-pointer select-none">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                value="1" 
                                class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0 transition cursor-pointer"
                            >
                            <span>Ingat sesi saya di perangkat ini</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        id="login-submit"
                        @disabled(session('lockout_seconds'))
                        class="w-full bg-[#1b2942] hover:bg-[#15223c] active:bg-[#0f192c] disabled:cursor-not-allowed disabled:opacity-60 active:bg-[#0f192c] text-white font-bold text-xs py-3.5 px-4 rounded-xl shadow-md shadow-slate-900/10 transition duration-150 cursor-pointer"
                    >
                        <span id="login-submit-label">{{ session('lockout_seconds') ? 'Tunggu sebentar...' : 'Masuk' }}</span>
                    </button>
                </form>

                <!-- Divider -->
                <div class="relative flex items-center justify-center my-4">
                    <div class="border-t border-slate-200/80 w-full"></div>
                    <span class="bg-white px-3 text-[10px] uppercase font-semibold text-slate-400 tracking-wider shrink-0">ATAU</span>
                    <div class="border-t border-slate-200/80 w-full"></div>
                </div>

                <!-- Google Login Button -->
                <button type="button" onclick="alert('Fitur login dengan Google belum dihubungkan ke server OAuth.')" class="w-full bg-[#f0f4fa] hover:bg-[#e4ebf5] active:bg-[#d8e3f2] text-slate-700 border border-slate-200/70 font-semibold text-xs py-2.5 px-4 rounded-xl flex items-center justify-center gap-2.5 transition duration-150 mb-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                    </svg>
                    <span>Lanjutkan dengan Google</span>
                </button>

                <!-- Help Link -->
                <p class="mt-5 text-center text-xs text-slate-500 font-medium">
                    Butuh bantuan? <a href="#" onclick="alert('Silakan hubungi Admin Gudang via Email: admin@stokclean.com atau WhatsApp: 0812-3456-7890')" class="font-semibold text-slate-700 hover:text-blue-600 transition">Hubungi Admin</a>
                </p>

            </div>
        </section>

    </main>

    <!-- FOOTER -->
    <footer class="w-full max-w-[920px] mx-auto py-4 flex flex-col sm:flex-row items-center justify-between text-[11px] sm:text-xs text-slate-400 font-medium gap-2">
        <p>© 2026 HigienisPro. Hak Cipta Dilindungi.</p>
        <div class="flex items-center gap-3">
            <a href="#" onclick="alert('Kebijakan Privasi StokClean')" class="hover:text-slate-600 transition">Kebijakan Privasi</a>
            <span>•</span>
            <a href="#" onclick="alert('Pusat Bantuan StokClean')" class="hover:text-slate-600 transition">Bantuan</a>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }

            const passwordInput = document.getElementById('password');
            const togglePasswordBtn = document.getElementById('toggle-password');

            if (togglePasswordBtn && passwordInput) {
                togglePasswordBtn.addEventListener('click', () => {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';
                    togglePasswordBtn.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
                    
                    const iconName = isPassword ? 'eye-off' : 'eye';
                    togglePasswordBtn.innerHTML = `<i data-lucide="${iconName}" class="w-4 h-4"></i>`;
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            }

            const countdown = document.getElementById('lockout-countdown');
            const loginForm = document.getElementById('login-form');
            const loginSubmit = document.getElementById('login-submit');
            const loginSubmitLabel = document.getElementById('login-submit-label');

            if (countdown && loginForm && loginSubmit && loginSubmitLabel) {
                let seconds = Number.parseInt(countdown.dataset.seconds, 10);
                const formatTime = (value) => {
                    const minutes = Math.floor(value / 60).toString().padStart(2, '0');
                    const remainingSeconds = (value % 60).toString().padStart(2, '0');
                    return `${minutes}:${remainingSeconds}`;
                };

                countdown.textContent = formatTime(seconds);
                const interval = window.setInterval(() => {
                    seconds -= 1;
                    countdown.textContent = formatTime(Math.max(seconds, 0));

                    if (seconds <= 0) {
                        window.clearInterval(interval);
                        loginSubmit.disabled = false;
                        loginSubmitLabel.textContent = 'Masuk';
                        loginSubmit.classList.remove('cursor-not-allowed', 'opacity-60');
                        loginForm.querySelectorAll('input, button').forEach((field) => {
                            field.disabled = false;
                        });
                    }
                }, 1000);

                loginSubmit.classList.add('cursor-not-allowed', 'opacity-60');
                loginForm.querySelectorAll('input, button').forEach((field) => {
                    field.disabled = true;
                });
                loginSubmit.disabled = true;
            }
        });
    </script>
</body>
</html>
