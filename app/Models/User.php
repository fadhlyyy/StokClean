<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'nomor_telepon', 'foto_profil', 'two_factor_code', 'two_factor_expires_at'])]
#[Hidden(['password', 'remember_token', 'two_factor_code'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_expires_at' => 'datetime',
        ];
    }

    /**
     * Generate a new 6-digit 2FA code valid for 5 minutes.
     */
    public function generateTwoFactorCode(): string
    {
        $code = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        $this->forceFill([
            'two_factor_code' => $code,
            'two_factor_expires_at' => now()->addMinutes(5),
        ])->save();

        return $code;
    }

    /**
     * Clear the 2FA code and expiration.
     */
    public function resetTwoFactorCode(): void
    {
        $this->forceFill([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ])->save();
    }

    /**
     * Get masked email: 2 huruf pertama, sensor bintang, 2 huruf sebelum @, lalu domain.
     * Contoh: mangfaattt@gmail.com -> ma******tt@gmail.com
     */
    public function getMaskedEmailAttribute(): string
    {
        return static::maskEmailAddress($this->email);
    }

    /**
     * Helper to mask email address: 2 first chars and 2 chars before @.
     */
    public static function maskEmailAddress(?string $email): string
    {
        if (! $email || ! str_contains($email, '@')) {
            return (string) $email;
        }

        [$name, $domain] = explode('@', $email, 2);
        $len = strlen($name);

        if ($len <= 4) {
            return substr($name, 0, 1) . str_repeat('*', max(2, $len - 1)) . '@' . $domain;
        }

        $firstTwo = substr($name, 0, 2);
        $lastTwo = substr($name, -2);
        $maskedMiddle = str_repeat('*', max(1, $len - 4));

        return $firstTwo . $maskedMiddle . $lastTwo . '@' . $domain;
    }
}
