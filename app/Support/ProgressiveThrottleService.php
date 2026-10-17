<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class ProgressiveThrottleService
{
    /**
     * Check if the action for the key is currently locked.
     *
     * @throws ValidationException
     */
    public static function ensureNotLocked(string $action, string $identifier, string $errorField = 'email'): void
    {
        $lockKey = self::lockKey($action, $identifier);
        $lockout = Cache::get($lockKey);

        if ($lockout && now()->timestamp < $lockout['locked_until']) {
            $remainingSeconds = $lockout['locked_until'] - now()->timestamp;
            $remainingMinutes = max(1, (int) ceil($remainingSeconds / 60));

            $timeText = $remainingMinutes >= 60
                ? ((int) round($remainingMinutes / 60)).' jam'
                : "{$remainingMinutes} menit";

            throw ValidationException::withMessages([
                $errorField => "Terlalu banyak percobaan (3x). Akses dibatasi sementara. Silakan coba lagi dalam {$timeText}.",
            ]);
        }
    }

    /**
     * Record a failed attempt. If attempts reach 3, trigger progressive lockout (10m, 30m, 60m+).
     *
     * @throws ValidationException
     */
    public static function hit(string $action, string $identifier, string $errorField = 'email', ?string $customFailMessage = null): void
    {
        $attemptsKey = self::attemptsKey($action, $identifier);
        $levelKey = self::levelKey($action, $identifier);
        $lockKey = self::lockKey($action, $identifier);

        $attempts = Cache::increment($attemptsKey, 1);
        if ($attempts === 1) {
            Cache::put($attemptsKey, 1, 86400);
        }

        if ($attempts >= 3) {
            $currentLevel = (int) Cache::get($levelKey, 0) + 1;
            Cache::put($levelKey, $currentLevel, 86400);

            // Progressive cooldown: Level 1 = 10m (600s), Level 2 = 30m (1800s), Level 3+ = 60m (3600s)
            $cooldownSeconds = match ($currentLevel) {
                1 => 600,
                2 => 1800,
                default => 3600,
            };

            Cache::put($lockKey, [
                'locked_until' => now()->addSeconds($cooldownSeconds)->timestamp,
                'level' => $currentLevel,
            ], $cooldownSeconds);

            Cache::forget($attemptsKey);

            $minutes = (int) ($cooldownSeconds / 60);
            $timeText = $minutes >= 60 ? '1 jam' : "{$minutes} menit";

            throw ValidationException::withMessages([
                $errorField => "Batas 3 kali percobaan terlampaui. Silakan menunggu {$timeText} sebelum mencoba kembali.",
            ]);
        }

        $sisa = 3 - $attempts;
        $failText = $customFailMessage ?: 'Percobaan gagal.';
        throw ValidationException::withMessages([
            $errorField => "{$failText} Sisa percobaan: {$sisa} kali.",
        ]);
    }

    /**
     * Clear all attempts and lockouts upon successful action.
     */
    public static function clear(string $action, string $identifier): void
    {
        Cache::forget(self::attemptsKey($action, $identifier));
        Cache::forget(self::lockKey($action, $identifier));
        Cache::forget(self::levelKey($action, $identifier));
    }

    protected static function lockKey(string $action, string $identifier): string
    {
        return 'throttle_lock_'.md5("{$action}|{$identifier}");
    }

    protected static function attemptsKey(string $action, string $identifier): string
    {
        return 'throttle_attempts_'.md5("{$action}|{$identifier}");
    }

    protected static function levelKey(string $action, string $identifier): string
    {
        return 'throttle_level_'.md5("{$action}|{$identifier}");
    }
}
