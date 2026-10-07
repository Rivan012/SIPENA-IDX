<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanKegiatan extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_kegiatan';

    public const STATUS = [
        'diajukan' => ['Menunggu Approval', 'bg-amber-50 text-amber-700 border border-amber-200'],
        'diproses' => ['Sedang Ditinjau', 'bg-sky-50 text-sky-700 border border-sky-200'],
        'revisi' => ['Perlu Revisi', 'bg-purple-50 text-purple-700 border border-purple-200'],
        'disetujui' => ['Disetujui', 'bg-emerald-50 text-emerald-700 border border-emerald-200'],
        'ditolak' => ['Ditolak', 'bg-rose-50 text-rose-700 border border-rose-200'],
    ];

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

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status][0] ?? ucfirst($this->status);
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS[$this->status][1] ?? 'bg-slate-100 text-slate-700 border border-slate-200';
    }

    public function getNamaKegiatanAttribute(): string
    {
        return $this->rencanaKegiatan?->nama_kegiatan ?? 'Kegiatan Belum Bernama';
    }

    public function getJenisKegiatanAttribute(): string
    {
        return $this->rencanaKegiatan?->jenis_kegiatan ?? 'Edukasi';
    }

    public function getAnggaranAttribute(): float
    {
        return (float) ($this->rencanaKegiatan?->detailKegiatan?->anggaran ?? 0);
    }

    public function getFormattedAnggaranAttribute(): string
    {
        return 'Rp ' . number_format($this->anggaran, 0, ',', '.');
    }
}
