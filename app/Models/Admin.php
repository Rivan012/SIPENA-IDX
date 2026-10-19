<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admins';

    protected $fillable = [
        'name',
        'email',
        'password',
        'no_telepon',
        'jabatan',
        'profile_photo_path',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function laporanKegiatan()
    {
        return $this->hasMany(LaporanKegiatan::class, 'admin_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'admin_id');
    }
}
