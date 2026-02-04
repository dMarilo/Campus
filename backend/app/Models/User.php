<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function professor()
    {
        return $this->hasOne(Professor::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    */

    // User types
    public const TYPE_ADMIN  = 'admin';
    public const TYPE_PROFESSOR = 'professor';
    public const TYPE_STUDENT = 'student';

    // User statuses
    public const STATUS_PENDING  = 'pending';
    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_BLOCKED  = 'blocked';

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'email',
        'password',
        'type',
        'status',
        'verification_token',
        'verification_token_expires_at',
        'must_reset_password',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
        'verification_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verification_token_expires_at' => 'datetime',
            'must_reset_password' => 'boolean',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public function isAdmin(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function needsPasswordReset(): bool
    {
        return $this->must_reset_password === true;
    }

    /**
     * Generate a verification token for the user.
     */
    public function generateVerificationToken(): string
    {
        $token = Str::random(64);

        $this->update([
            'verification_token' => hash('sha256', $token),
            'verification_token_expires_at' => now()->addHours(48),
            'must_reset_password' => true,
        ]);

        return $token;
    }

    /**
     * Verify the token and mark email as verified.
     */
    public function verifyEmail(string $token): bool
    {
        if ($this->verification_token !== hash('sha256', $token)) {
            return false;
        }

        if ($this->verification_token_expires_at && $this->verification_token_expires_at->isPast()) {
            return false;
        }

        // ✅ Make sure we're actually updating and saving
        $this->email_verified_at = now();
        $this->verification_token = null;
        $this->verification_token_expires_at = null;
        // Don't change status here - it should stay 'pending' until password is set

        $this->save(); // ✅ Explicitly call save()

        return true;
    }

    /**
     * Complete password reset after verification.
     */
    public function completePasswordReset(string $newPassword): void
    {
        $this->update([
            'password' => $newPassword,
            'must_reset_password' => false,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | JWT
    |--------------------------------------------------------------------------
    */

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }
}
