<?php

namespace App\Models;

use App\Notifications\Auth\ResetPasswordNotification;
use App\Notifications\Auth\VerifyEmailNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'profile_photo_path',
        'role',
        'preferred_locale',
        'subscription_tier',
        'premium_granted_at',
        'premium_expires_at',
        'premium_duration',
        'premium_level_key',
        'premium_teacher_id',
        'premium_subject_key',
        'teacher_verified_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
            'teacher_verified_at' => 'datetime',
            'premium_granted_at' => 'datetime',
            'premium_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function hasActivePremium(): bool
    {
        if ($this->subscription_tier !== 'premium') {
            return false;
        }

        return $this->premium_expires_at?->isFuture() ?? false;
    }

    public function hasActivePremiumForLevel(string $levelKey): bool
    {
        if (! $this->hasActivePremium()) {
            return false;
        }

        return filled($this->premium_level_key) && \App\Support\LevelAudience::matches($levelKey, $this->premium_level_key);
    }

    public function hasActivePremiumForLesson(TeacherLesson $lesson): bool
    {
        if (! $this->hasActivePremiumForLevel($lesson->level_key)) {
            return false;
        }

        if (filled($this->premium_teacher_id) && (int) $this->premium_teacher_id !== (int) $lesson->teacher_id) {
            return false;
        }

        return blank($this->premium_subject_key) || $this->premium_subject_key === $lesson->subject_key;
    }

    public function teacherLessons(): HasMany
    {
        return $this->hasMany(TeacherLesson::class, 'teacher_id');
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isVerifiedTeacher(): bool
    {
        return $this->role === 'teacher' && $this->teacher_verified_at !== null;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification);
    }
}
