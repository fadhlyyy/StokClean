<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'role' => ['required', 'in:pemilik,pegawai'],
            'g-recaptcha-response' => ['required'],
        ], [
            'email.required' => 'Email atau ID Petugas wajib diisi.',
            'email.email' => 'Masukkan alamat email yang valid.',
            'password.required' => 'Kata sandi wajib diisi.',
            'role.required' => 'Pilih role terlebih dahulu.',
            'g-recaptcha-response.required' => 'Silakan centang verifikasi "Saya bukan robot".',
        ]);

        $throttleKey = $this->loginThrottleKey($request, $validated['email']);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            return back()
                ->withErrors(['email' => 'Terlalu banyak percobaan login. Silakan tunggu sebelum mencoba lagi.'])
                ->with('lockout_seconds', RateLimiter::availableIn($throttleKey))
                ->onlyInput('email', 'role');
        }

        $recaptchaSecret = config('services.recaptcha.secret_key');
        if ($recaptchaSecret) {
            $recaptchaSuccess = false;
            try {
                $response = Http::withoutVerifying()->asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => $recaptchaSecret,
                    'response' => $request->input('g-recaptcha-response'),
                    'remoteip' => $request->ip(),
                ]);

                $recaptchaSuccess = (bool) $response->json('success');
            } catch (\Throwable $e) {
                logger()->error('reCAPTCHA verification error: ' . $e->getMessage());
            }

            if (! $recaptchaSuccess) {
                $lockoutSeconds = $this->registerFailedLogin($throttleKey);

                return back()
                    ->withErrors(['g-recaptcha-response' => 'Verifikasi reCAPTCHA tidak valid. Silakan coba lagi.'])
                    ->with('lockout_seconds', $lockoutSeconds)
                    ->onlyInput('email', 'role');
            }
        }

        $credentials = $request->only('email', 'password', 'role');

        if (! Auth::validate($credentials)) {
            $lockoutSeconds = $this->registerFailedLogin($throttleKey);

            return back()
                ->withErrors(['email' => 'Email, kata sandi, atau role tidak sesuai.'])
                ->with('lockout_seconds', $lockoutSeconds)
                ->onlyInput('email', 'role');
        }

        $user = User::where('email', $credentials['email'])->where('role', $credentials['role'])->first();
        if (! $user) {
            $lockoutSeconds = $this->registerFailedLogin($throttleKey);

            return back()
                ->withErrors(['email' => 'Akun pengguna tidak ditemukan.'])
                ->with('lockout_seconds', $lockoutSeconds)
                ->onlyInput('email', 'role');
        }

        RateLimiter::clear($throttleKey);

        // Generate 6-digit OTP and set expiration (5 minutes)
        $otp = $user->generateTwoFactorCode();

        // Save pending 2FA state to session
        $request->session()->put('2fa_user_id', $user->id);
        $request->session()->put('2fa_remember', $request->boolean('remember'));

        // Attempt to send email
        $mailSent = false;
        try {
            Mail::to($user->email)->send(new SendOtpMail($otp, $user->name));
            $mailSent = true;
        } catch (\Throwable $e) {
            logger()->error('Gagal mengirim email OTP: ' . $e->getMessage());
        }

        $statusMsg = 'Kode verifikasi 2 langkah (OTP) telah dikirimkan ke email Anda.';

        return redirect()->route('login.otp')->with('status', $statusMsg);
    }

    /**
     * Show the 2FA OTP verification page.
     */
    public function showOtpForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find($request->session()->get('2fa_user_id'));
        if (! $user) {
            $request->session()->forget(['2fa_user_id', '2fa_remember']);
            return redirect()->route('login');
        }

        $maskedEmail = $this->maskEmail($user->email);
        $secondsRemaining = $user->two_factor_expires_at
            ? max(0, (int) now()->diffInSeconds($user->two_factor_expires_at, false))
            : 0;

        return view('auth.otp', [
            'maskedEmail' => $maskedEmail,
            'secondsRemaining' => $secondsRemaining,
            'user' => $user,
        ]);
    }

    /**
     * Verify the submitted 2FA OTP code.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login')->withErrors(['email' => 'Sesi verifikasi telah berakhir. Silakan masuk kembali.']);
        }

        $user = User::find($request->session()->get('2fa_user_id'));
        if (! $user) {
            $request->session()->forget(['2fa_user_id', '2fa_remember']);
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Kode OTP wajib diisi.',
            'otp.size' => 'Kode OTP harus berjumlah 6 digit.',
        ]);

        $otpThrottleKey = 'otp-verify:' . $user->id . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($otpThrottleKey, 5)) {
            return back()->withErrors([
                'otp' => 'Terlalu banyak percobaan OTP salah. Tunggu ' . RateLimiter::availableIn($otpThrottleKey) . ' detik sebelum mencoba lagi.',
            ]);
        }

        if (! $user->two_factor_code || ! $user->two_factor_expires_at || now()->isAfter($user->two_factor_expires_at)) {
            return back()->withErrors([
                'otp' => 'Kode OTP telah kedaluwarsa. Silakan klik "Kirim Ulang Kode".',
            ]);
        }

        if ($user->two_factor_code !== $validated['otp']) {
            RateLimiter::hit($otpThrottleKey, 60);

            return back()->withErrors([
                'otp' => 'Kode OTP yang Anda masukkan salah. Periksa kembali email Anda.',
            ]);
        }

        // Verification successful
        RateLimiter::clear($otpThrottleKey);
        $user->resetTwoFactorCode();

        $remember = (bool) $request->session()->get('2fa_remember', false);
        $request->session()->forget(['2fa_user_id', '2fa_remember']);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', 'Verifikasi berhasil! Selamat datang, ' . $user->name . '.');
    }

    /**
     * Resend a fresh 2FA OTP code.
     */
    public function resendOtp(Request $request): RedirectResponse
    {
        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        $user = User::find($request->session()->get('2fa_user_id'));
        if (! $user) {
            $request->session()->forget(['2fa_user_id', '2fa_remember']);
            return redirect()->route('login');
        }

        $resendThrottleKey = 'otp-resend:' . $user->id;
        if (RateLimiter::tooManyAttempts($resendThrottleKey, 1)) {
            return back()->withErrors([
                'otp' => 'Mohon tunggu ' . RateLimiter::availableIn($resendThrottleKey) . ' detik sebelum meminta kode baru.',
            ]);
        }

        RateLimiter::hit($resendThrottleKey, 60);

        $otp = $user->generateTwoFactorCode();

        $mailSent = false;
        try {
            Mail::to($user->email)->send(new SendOtpMail($otp, $user->name));
            $mailSent = true;
        } catch (\Throwable $e) {
            logger()->error('Gagal mengirim ulang email OTP: ' . $e->getMessage());
        }

        $statusMsg = 'Kode OTP baru telah berhasil dikirimkan ke email Anda.';

        return back()->with('status', $statusMsg);
    }

    /**
     * Cancel the 2FA login process and return to login.
     */
    public function cancelOtp(Request $request): RedirectResponse
    {
        $request->session()->forget(['2fa_user_id', '2fa_remember']);
        return redirect()->route('login');
    }

    /**
     * Mask an email address for privacy (e.g. pgfadhly@gmail.com -> p***y@gmail.com).
     */
    private function maskEmail(string $email): string
    {
        return User::maskEmailAddress($email);
    }

    private function loginThrottleKey(Request $request, string $email): string
    {
        return 'login:'.strtolower($email).'|'.$request->ip();
    }

    private function registerFailedLogin(string $throttleKey): ?int
    {
        RateLimiter::hit($throttleKey, 60);

        return RateLimiter::tooManyAttempts($throttleKey, 5)
            ? RateLimiter::availableIn($throttleKey)
            : null;
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function storeUser(Request $request): RedirectResponse
    {
        if (Auth::user()?->role !== 'pemilik') {
            abort(403, 'Hanya Pemilik (Admin) yang memiliki hak akses untuk mengelola pengguna.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:pemilik,pegawai'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->to(route('dashboard') . '#pengguna')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        if (Auth::user()?->role !== 'pemilik') {
            abort(403, 'Hanya Pemilik (Admin) yang memiliki hak akses untuk mengelola pengguna.');
        }

        // Akun pemilik tidak bisa diedit melalui menu ini
        if ($user->role === 'pemilik') {
            return back()->withErrors(['error' => 'Akun Pemilik (Admin) dilindungi dan tidak dapat diedit melalui menu ini.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'nomor_telepon' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8'],
        ], [
            'name.required' => 'Nama pengguna wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nomor_telepon' => $validated['nomor_telepon'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->to(route('dashboard') . '#pengguna')->with('success', 'Data akun pegawai ' . $user->name . ' berhasil diperbarui.');
    }
}