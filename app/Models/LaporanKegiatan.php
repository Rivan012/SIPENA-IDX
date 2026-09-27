<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanKegiatan extends Model
{
    use HasFactory;

    protected $table = 'laporan_kegiatan';

    protected $fillable = [
        'kegiatan_id',
        'admin_id',
        'judul_laporan',
        'tanggal_laporan',
        'isi_laporan',
        'file_laporan',
        'total_peserta_hadir',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_laporan' => 'date',
            'total_peserta_hadir' => 'integer',
        ];
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
