<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_unauthenticated_user_is_redirected(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'pemilik',
        ]);

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }

    public function test_login_requires_recaptcha(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => 'pemilik',
        ]);

        $response->assertSessionHasErrors('g-recaptcha-response');
    }

    public function test_login_requires_two_factor_otp_and_sends_email(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => \Illuminate\Support\Facades\Http::response(['success' => true], 200),
        ]);
        \Illuminate\Support\Facades\Mail::fake();

        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'pemilik',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => 'pemilik',
            'g-recaptcha-response' => 'mocked-valid-token',
        ]);

        $response->assertRedirect(route('login.otp'));
        $response->assertSessionHas('2fa_user_id', $user->id);

        $user->refresh();
        $this->assertNotNull($user->two_factor_code);

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\SendOtpMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->otp === $user->two_factor_code;
        });

        // Test OTP screen rendering
        $otpScreen = $this->withSession(['2fa_user_id' => $user->id])->get('/login/otp');
        $otpScreen->assertStatus(200);
        $otpScreen->assertSee('Verifikasi 2 Langkah');

        // Test invalid OTP code
        $badOtpResponse = $this->withSession(['2fa_user_id' => $user->id])->post('/login/otp', [
            'otp' => '000000',
        ]);
        $badOtpResponse->assertSessionHasErrors('otp');
        $this->assertGuest();

        // Test valid OTP code
        $validOtpResponse = $this->withSession(['2fa_user_id' => $user->id])->post('/login/otp', [
            'otp' => $user->two_factor_code,
        ]);
        $validOtpResponse->assertRedirect('/');
        $this->assertAuthenticatedAs($user);

        // Ensure OTP code was cleared after successful login
        $user->refresh();
        $this->assertNull($user->two_factor_code);
    }

    public function test_profile_update_rejects_gif_file(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'pemilik',
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->create('avatar.gif', 500, 'image/gif');

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Nama Baru',
            'email' => $user->email,
            'foto_profil' => $file,
        ]);

        $response->assertSessionHasErrors('foto_profil');
    }

    public function test_profile_update_accepts_png_file(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create([
            'role' => 'pemilik',
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.png');

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Nama Baru',
            'email' => $user->email,
            'foto_profil' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertEquals('Nama Baru', $user->name);
        $this->assertNotNull($user->foto_profil);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($user->foto_profil);
    }

    public function test_pemilik_can_update_pegawai_account(): void
    {
        $pemilik = User::factory()->create(['role' => 'pemilik']);
        $pegawai = User::factory()->create([
            'name' => 'Pegawai Lama',
            'email' => 'lama@stokclean.com',
            'role' => 'pegawai',
        ]);

        $response = $this->actingAs($pemilik)->put('/users/' . $pegawai->id, [
            'name' => 'Pegawai Diedit',
            'email' => 'diedit@stokclean.com',
            'nomor_telepon' => '08123456789',
            'password' => 'newpassword123',
        ]);

        $response->assertRedirect(route('dashboard') . '#pengguna')->assertSessionHas('success');
        $pegawai->refresh();
        $this->assertEquals('Pegawai Diedit', $pegawai->name);
        $this->assertEquals('diedit@stokclean.com', $pegawai->email);
        $this->assertEquals('08123456789', $pegawai->nomor_telepon);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $pegawai->password));
    }

    public function test_pemilik_cannot_update_pemilik_account_via_user_management(): void
    {
        $pemilik = User::factory()->create(['role' => 'pemilik']);

        $response = $this->actingAs($pemilik)->put('/users/' . $pemilik->id, [
            'name' => 'Hacker Name',
            'email' => 'hacked@stokclean.com',
        ]);

        $response->assertSessionHasErrors('error');
        $pemilik->refresh();
        $this->assertNotEquals('Hacker Name', $pemilik->name);
    }

    public function test_pegawai_cannot_update_user_accounts(): void
    {
        $pegawai1 = User::factory()->create(['role' => 'pegawai']);
        $pegawai2 = User::factory()->create(['role' => 'pegawai']);

        $response = $this->actingAs($pegawai1)->put('/users/' . $pegawai2->id, [
            'name' => 'Ubah Nama',
            'email' => 'ubah@stokclean.com',
        ]);

        $response->assertStatus(403);
    }

    public function test_email_masking_shows_first_two_and_last_two_before_at(): void
    {
        $this->assertEquals('ma******tt@gmail.com', User::maskEmailAddress('mangfaattt@gmail.com'));
        $this->assertEquals('sh*****e0@gmail.com', User::maskEmailAddress('shaaweee0@gmail.com'));
        $this->assertEquals('pg****ly@gmail.com', User::maskEmailAddress('pgfadhly@gmail.com'));
        $this->assertEquals('ab*de@gmail.com', User::maskEmailAddress('abcde@gmail.com'));

        $user = User::factory()->make(['email' => 'mangfaattt@gmail.com']);
        $this->assertEquals('ma******tt@gmail.com', $user->masked_email);
    }
}
