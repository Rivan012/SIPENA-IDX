<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Aktivitas extends Model
{
    use HasFactory;

    protected $table = 'aktivitas';

    protected $fillable = [
        'user_id',
        'admin_id',
        'kegiatan_id',
        'aktor_nama',
        'aktor_role',
        'aksi',
        'deskripsi',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public static function log(
        string $aksi,
        string $deskripsi,
        ?Kegiatan $kegiatan = null,
        ?array $properties = null,
        ?User $user = null,
        ?Admin $admin = null,
    ): self {
        $admin = $admin ?? Auth::guard('admin')->user();
        $user = $user ?? Auth::guard('web')->user();

        $role = $admin ? 'admin' : ($user ? 'mitra' : 'sistem');
        $nama = $admin?->name ?? $user?->name ?? 'Sistem';

        return self::create([
            'user_id' => $user?->id,
            'admin_id' => $admin?->id,
            'kegiatan_id' => $kegiatan?->id,
            'aktor_nama' => $nama,
            'aktor_role' => $role,
            'aksi' => $aksi,
            'deskripsi' => $deskripsi,
            'properties' => $properties,
        ]);
    }
}
