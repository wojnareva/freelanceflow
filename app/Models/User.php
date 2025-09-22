<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PragmaRX\Google2FA\Google2FA;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'currency',
        'number_format',
        'date_format',
        'timezone',
        'two_factor_secret',
        'two_factor_confirmed_at',
        'two_factor_recovery_codes',
        'two_factor_enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

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
            'two_factor_confirmed_at' => 'datetime',
            'two_factor_recovery_codes' => 'array',
            'two_factor_enabled' => 'boolean',
        ];
    }

    /**
     * Generate a new 2FA secret
     */
    public function generateTwoFactorSecret(): string
    {
        $google2fa = new Google2FA();
        $secret = $google2fa->generateSecretKey();
        
        $this->update(['two_factor_secret' => $secret]);
        
        return $secret;
    }

    /**
     * Get QR code URL for 2FA setup
     */
    public function getTwoFactorQrCodeUrl(): string
    {
        $google2fa = new Google2FA();
        
        return $google2fa->getQRCodeUrl(
            'FreelanceFlow',
            $this->email,
            $this->two_factor_secret
        );
    }

    /**
     * Verify 2FA code
     */
    public function verifyTwoFactorCode(string $code): bool
    {
        if (!$this->two_factor_secret) {
            return false;
        }

        $google2fa = new Google2FA();
        
        return $google2fa->verifyKey($this->two_factor_secret, $code, 2); // 2 = tolerance window
    }

    /**
     * Generate recovery codes
     */
    public function generateRecoveryCodes(): array
    {
        $recoveryCodes = [];
        
        for ($i = 0; $i < 8; $i++) {
            $recoveryCodes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        
        $this->update(['two_factor_recovery_codes' => $recoveryCodes]);
        
        return $recoveryCodes;
    }

    /**
     * Use a recovery code
     */
    public function useRecoveryCode(string $code): bool
    {
        $codes = $this->two_factor_recovery_codes ?? [];
        $code = strtoupper($code);
        
        if (in_array($code, $codes)) {
            // Remove used code
            $codes = array_values(array_filter($codes, fn($c) => $c !== $code));
            $this->update(['two_factor_recovery_codes' => $codes]);
            return true;
        }
        
        return false;
    }

    /**
     * Enable 2FA
     */
    public function enableTwoFactor(): void
    {
        $this->update([
            'two_factor_enabled' => true,
            'two_factor_confirmed_at' => now(),
        ]);
    }

    /**
     * Disable 2FA
     */
    public function disableTwoFactor(): void
    {
        $this->update([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ]);
    }

    /**
     * Check if 2FA is enabled
     */
    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_enabled && $this->two_factor_secret;
    }
}
