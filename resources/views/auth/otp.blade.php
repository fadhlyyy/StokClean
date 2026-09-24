<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi 2 Langkah | StokClean</title>
    
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f3f4fa] text-slate-800 font-sans flex flex-col justify-between p-4 sm:p-6 md:p-8 antialiased">

    <!-- Top Spacer -->
    <div class="hidden sm:block"></div>

    <!-- MAIN CONTAINER -->
    <main class="w-full max-w-[920px] mx-auto bg-white rounded-3xl border border-slate-200/80 shadow-[0_15px_45px_rgba(15,23,42,0.06)] overflow-hidden grid md:grid-cols-2 my-auto">
        <!-- LEFT PANEL: Frosted Glass Card over Warehouse Background -->
        <section class="relative overflow-hidden p-6 sm:p-8 flex flex-col items-center justify-center text-center min-h-[340px] md:min-h-[520px] bg-slate-900 border-b md:border-b-0 md:border-r border-slate-200/60">
            <!-- Background Image -->
            <img 
                src="{{ asset('images/warehouse.jpg') }}" 
                alt="Background Gudang" 
                class="absolute inset-0 w-full h-full object-cover opacity-95 pointer-events-none"
            >
            <!-- Soft Dark Overlay -->
            <div class="absolute inset-0 bg-slate-900/30"></div>

            <!-- CARD STOKCLEAN DENGAN BLUR KACA -->
            <div class="relative z-10 w-full max-w-[310px] bg-white/85 backdrop-blur-[8px] p-7 sm:p-8 rounded-3xl border border-white/70 shadow-[0_20px_50px_rgba(15,23,42,0.22)] flex flex-col items-center justify-center text-center">
                <!-- Logo Box -->
                <div class="w-20 h-20 sm:w-24 sm:h-24 bg-white rounded-2xl border border-slate-200/80 shadow-md flex items-center justify-center mb-4 p-2.5 transform transition duration-300 hover:scale-105">
                    <img src="{{ asset('images/logo.png') }}" alt="StokClean Logo" class="w-[58px] h-[58px] object-contain">
                </div>

                <!-- Brand Name & Security Status -->
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">StokClean</h2>
                <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100/80 text-emerald-800 text-[11px] font-semibold rounded-full border border-emerald-200/60">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>Autentikasi Aman</span>
                </div>
                <p class="mt-3 text-xs text-slate-600 font-medium leading-relaxed">
                    Sistem perlindungan ganda untuk menjaga keamanan data inventaris Anda.
                </p>

                <!-- Indicator Dots -->
                <div class="mt-5 flex items-center gap-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                    <span class="h-1.5 w-6 rounded-full bg-[#1B2A4A] shadow-xs"></span>
                    <span class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                </div>
            </div>
        </section>

        <!-- RIGHT PANEL: OTP Form -->
        <section class="bg-white p-8 sm:p-12 flex flex-col justify-center">
            <div class="w-full max-w-[380px] mx-auto">
                
                <!-- Security Icon & Title -->
                <div class="text-center sm:text-left mb-6">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 mb-3.5 border border-blue-100 shadow-sm">
                        <i data-lucide="mail-check" class="w-6 h-6"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Verifikasi 2 Langkah</h1>
                    <p class="mt-1.5 text-xs text-slate-500 font-normal leading-relaxed">
                        Kami telah mengirimkan kode verifikasi 6-digit ke alamat email:
                    </p>
                    <div class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-800 rounded-lg text-xs font-semibold border border-slate-200/80">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-slate-500"></i>
                        <span>{{ $maskedEmail }}</span>
                    </div>
                </div>

                <!-- Alert Messages -->
                @if (isset($errors) && $errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-red-50 border border-red-200 text-xs text-red-700 flex items-start gap-2.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-red-500 shrink-0 mt-0.5"></i>
                        <div class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-700 flex items-start gap-2.5">
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5"></i>
                        <p class="leading-relaxed">{{ session('status') }}</p>
                    </div>
                @endif

                <!-- OTP Verification Form -->
                <form method="POST" action="{{ route('login.otp.verify') }}" id="otp-form" class="space-y-5">
                    @csrf
                    
                    <!-- Hidden input to store full 6-digit code -->
                    <input type="hidden" name="otp" id="full-otp" value="">

                    <!-- 6 Digit Inputs -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2 text-center sm:text-left">
                            Masukkan 6 Digit Kode OTP
                        </label>
                        <div class="grid grid-cols-6 gap-2 sm:gap-2.5" id="otp-inputs-container">
                            @for ($i = 0; $i < 6; $i++)
                                <input 
                                    type="text" 
                                    inputmode="numeric" 
                                    maxlength="1" 
                                    pattern="[0-9]"
                                    data-index="{{ $i }}"
                                    class="otp-digit w-full h-12 sm:h-14 text-center font-extrabold text-xl sm:text-2xl text-slate-900 bg-[#f1f5fa] focus:bg-white border-2 border-slate-200/80 focus:border-blue-600 focus:ring-4 focus:ring-blue-600/15 rounded-xl outline-none transition-all duration-150 shadow-inner"
                                    autocomplete="one-time-code"
                                    required
                                >
                            @endfor
                        </div>
                        <p class="mt-2 text-[11px] text-slate-400 text-center sm:text-left">
                            Tip: Anda dapat langsung menempelkan (paste) kode yang Anda salin.
                        </p>
                    </div>

                    <!-- Countdown Expiration Info -->
                    <div class="flex items-center justify-between py-1.5 px-3 rounded-xl bg-slate-50 border border-slate-200/60 text-xs">
                        <div class="flex items-center gap-1.5 text-slate-600 font-medium">
                            <i data-lucide="clock" class="w-4 h-4 text-slate-400"></i>
                            <span>Berlaku hingga:</span>
                        </div>
                        <span id="countdown-timer" data-seconds="{{ $secondsRemaining }}" class="font-bold font-mono text-slate-800">
                            --:--
                        </span>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        id="verify-submit-btn"
                        class="w-full bg-[#1b2942] hover:bg-[#15223c] active:bg-[#0f192c] text-white font-bold text-xs py-3.5 px-4 rounded-xl shadow-md shadow-slate-900/10 transition duration-150 flex items-center justify-center gap-2 cursor-pointer"
                    >
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Verifikasi & Masuk</span>
                    </button>
                </form>

                <!-- Resend Code Form -->
                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col items-center justify-center gap-3">
                    <form method="POST" action="{{ route('login.otp.resend') }}" id="resend-form" class="w-full text-center">
                        @csrf
                        <button 
                            type="submit" 
                            id="resend-btn"
                            class="text-xs font-semibold text-blue-600 hover:text-blue-700 disabled:text-slate-400 disabled:cursor-not-allowed transition inline-flex items-center gap-1.5 cursor-pointer"
                        >
                            <i data-lucide="rotate-cw" class="w-3.5 h-3.5" id="resend-icon"></i>
                            <span id="resend-label">Kirim Ulang Kode OTP</span>
                        </button>
                    </form>

                    <!-- Cancel / Back to Login -->
                    <form method="POST" action="{{ route('login.otp.cancel') }}" class="w-full text-center">
                        @csrf
                        <button 
                            type="submit" 
                            class="text-xs font-medium text-slate-500 hover:text-slate-700 transition inline-flex items-center gap-1.5 cursor-pointer"
                        >
                            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                            <span>Ganti akun atau kembali ke login</span>
                        </button>
                    </form>
                </div>

            </div>
        </section>
    </main>

    <!-- FOOTER -->
    <footer class="w-full max-w-[920px] mx-auto text-center text-xs text-slate-500 py-4">
        &copy; {{ date('Y') }} StokClean. Hak cipta dilindungi undang-undang.
    </footer>

    <!-- Interactive OTP & Timer Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (window.lucide) {
                window.lucide.createIcons();
            }

            const digits = Array.from(document.querySelectorAll('.otp-digit'));
            const fullOtpInput = document.getElementById('full-otp');
            const form = document.getElementById('otp-form');

            // Auto-focus first input
            if (digits.length > 0) {
                digits[0].focus();
            }

            function updateFullOtp() {
                const code = digits.map(input => input.value).join('');
                fullOtpInput.value = code;
                return code;
            }

            digits.forEach((input, idx) => {
                // Keydown handling: Backspace and navigation
                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace') {
                        if (!input.value && idx > 0) {
                            digits[idx - 1].focus();
                            digits[idx - 1].value = '';
                            updateFullOtp();
                            e.preventDefault();
                        } else {
                            input.value = '';
                            updateFullOtp();
                        }
                    } else if (e.key === 'ArrowLeft' && idx > 0) {
                        digits[idx - 1].focus();
                        e.preventDefault();
                    } else if (e.key === 'ArrowRight' && idx < digits.length - 1) {
                        digits[idx + 1].focus();
                        e.preventDefault();
                    }
                });

                // Input handling: Only allow single numeric digit & auto advance
                input.addEventListener('input', (e) => {
                    const val = input.value.replace(/[^0-9]/g, '');
                    input.value = val ? val.slice(-1) : '';
                    
                    const code = updateFullOtp();

                    if (input.value && idx < digits.length - 1) {
                        digits[idx + 1].focus();
                    }

                    // If all 6 digits are filled, automatically submit
                    if (code.length === 6) {
                        form.submit();
                    }
                });

                // Paste handling across inputs
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pastedData = (e.clipboardData || window.clipboardData).getData('text');
                    const numbers = pastedData.replace(/[^0-9]/g, '').slice(0, 6);
                    
                    if (numbers.length > 0) {
                        numbers.split('').forEach((num, i) => {
                            if (digits[i]) {
                                digits[i].value = num;
                            }
                        });
                        const code = updateFullOtp();
                        if (numbers.length < digits.length) {
                            digits[numbers.length].focus();
                        } else {
                            digits[digits.length - 1].focus();
                            form.submit();
                        }
                    }
                });
            });

            form.addEventListener('submit', (e) => {
                const code = updateFullOtp();
                if (code.length !== 6) {
                    e.preventDefault();
                    alert('Silakan lengkapi 6 digit kode OTP.');
                    const emptyIdx = digits.findIndex(d => !d.value);
                    if (emptyIdx !== -1) digits[emptyIdx].focus();
                }
            });

            // Countdown Timer
            const timerEl = document.getElementById('countdown-timer');
            let remainingSeconds = parseInt(timerEl.dataset.seconds || '0', 10);

            function renderCountdown() {
                if (remainingSeconds <= 0) {
                    timerEl.textContent = 'Kedaluwarsa';
                    timerEl.classList.add('text-red-600');
                    return;
                }
                const mins = Math.floor(remainingSeconds / 60);
                const secs = remainingSeconds % 60;
                timerEl.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }

            renderCountdown();

            if (remainingSeconds > 0) {
                const interval = setInterval(() => {
                    remainingSeconds--;
                    renderCountdown();
                    if (remainingSeconds <= 0) {
                        clearInterval(interval);
                    }
                }, 1000);
            }

            // Resend Button Cooldown (60 seconds)
            const resendBtn = document.getElementById('resend-btn');
            const resendLabel = document.getElementById('resend-label');
            let resendCooldown = 30; // 30 seconds default initial wait on render

            // Check if user recently requested resend via sessionStorage
            const lastResend = sessionStorage.getItem('stokclean_otp_resend_time');
            if (lastResend) {
                const elapsed = Math.floor((Date.now() - parseInt(lastResend, 10)) / 1000);
                if (elapsed < 60) {
                    resendCooldown = 60 - elapsed;
                } else {
                    resendCooldown = 0;
                }
            }

            if (resendCooldown > 0) {
                resendBtn.disabled = true;
                const resendInterval = setInterval(() => {
                    resendCooldown--;
                    if (resendCooldown <= 0) {
                        clearInterval(resendInterval);
                        resendBtn.disabled = false;
                        resendLabel.textContent = 'Kirim Ulang Kode OTP';
                    } else {
                        resendLabel.textContent = `Kirim Ulang Kode (${resendCooldown}s)`;
                    }
                }, 1000);
            }

            document.getElementById('resend-form').addEventListener('submit', () => {
                sessionStorage.setItem('stokclean_otp_resend_time', Date.now().toString());
            });
        });
    </script>
</body>
</html>
