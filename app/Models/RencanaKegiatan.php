<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaKegiatan extends Model
{
    use HasFactory;

    protected $table = 'rencana_kegiatan';

    public const STATUS = [
        'draft' => ['Draft Rencana', 'bg-slate-100 text-slate-700 border border-slate-200'],
        'diajukan' => ['Diajukan ke Review', 'bg-amber-50 text-amber-700 border border-amber-200'],
        'disetujui' => ['Disetujui', 'bg-emerald-50 text-emerald-700 border border-emerald-200'],
        'ditolak' => ['Ditolak', 'bg-rose-50 text-rose-700 border border-rose-200'],
    ];

    protected $fillable = [
        'admin_id',
        'nama_kegiatan',
        'jenis_kegiatan',
        'tanggal_rencana',
        'status',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_rencana' => 'date',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function detailKegiatan()
    {
        return $this->hasOne(DetailKegiatan::class, 'rencana_kegiatan_id');
    }

    public function pengajuanKegiatan()
    {
        return $this->hasMany(PengajuanKegiatan::class, 'rencana_kegiatan_id');
    }

    public function kegiatan()
    {
        return $this->hasOne(Kegiatan::class, 'rencana_kegiatan_id');
    }

    public function getKodeAttribute(): string
    {
        $year = $this->tanggal_rencana ? $this->tanggal_rencana->format('Y') : '2025';
        return sprintf('RK-%s-%03d', $year, $this->id);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status][0] ?? ucfirst($this->status);
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS[$this->status][1] ?? 'bg-slate-100 text-slate-700 border border-slate-200';
    }

    public function getAnggaranAttribute(): float
    {
        return (float) ($this->detailKegiatan?->anggaran ?? 0);
    }

    public function getFormattedAnggaranAttribute(): string
    {
        return 'Rp ' . number_format($this->anggaran, 0, ',', '.');
    }
}
