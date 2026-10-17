<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'no_telepon', 'profile_photo_path', 'email_otp', 'email_otp_expires_at', 'email_verified_at'])]
#[Hidden(['password', 'remember_token', 'email_otp'])]
class User extends Authenticatable implements MustVerifyEmail
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
            'email_otp_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function peserta()
    {
        return $this->hasMany(Peserta::class, 'user_id');
    }

    public function kegiatan()
    {
        return $this->belongsToMany(Kegiatan::class, 'peserta', 'user_id', 'kegiatan_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function kegiatanDiajukan()
    {
        return $this->hasMany(Kegiatan::class, 'user_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'user_id');
    }
}
