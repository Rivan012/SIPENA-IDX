<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanKegiatan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_kegiatan';

    protected $fillable = [
        'rencana_kegiatan_id',
        'admin_id',
        'nomor_pengajuan',
        'tanggal_pengajuan',
        'status',
        'catatan',
        'catatan_revisi',
        'diproses_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_pengajuan' => 'date',
            'diproses_at' => 'datetime',
        ];
    }

    public function rencanaKegiatan()
    {
        return $this->belongsTo(RencanaKegiatan::class, 'rencana_kegiatan_id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function kegiatan()
    {
        return $this->hasOne(Kegiatan::class, 'pengajuan_kegiatan_id');
    }
}
