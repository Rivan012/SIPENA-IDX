<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    // status => [label, badge classes]
    public const STATUS = [
        'menunggu_approval' => ['Menunggu Approval', 'bg-amber-50 text-amber-700'],
        'disetujui' => ['Disetujui', 'bg-emerald-50 text-emerald-700'],
        'dipublikasikan' => ['Dipublikasikan', 'bg-sky-50 text-sky-700'],
        'berlangsung' => ['Berlangsung', 'bg-rose-50 text-rose-700'],
        'selesai' => ['Selesai', 'bg-slate-100 text-slate-700'],
        'dibatalkan' => ['Dibatalkan', 'bg-red-100 text-red-800'],
    ];

    public const SELESAI = ['selesai', 'dibatalkan'];

    public function getKodeAttribute(): string
    {
        return sprintf('ACT-%s-%03d', $this->tanggal?->format('Y') ?? date('Y'), $this->id);
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS[$this->status][0] ?? ucfirst($this->status);
    }

    public function getStatusClassAttribute(): string
    {
        return self::STATUS[$this->status][1] ?? 'bg-slate-100 text-slate-600';
    }

    protected $fillable = [
        'pengajuan_kegiatan_id',
        'rencana_kegiatan_id',
        'nama_kegiatan',
        'deskripsi',
        'jenis_kegiatan',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'kuota_peserta',
        'status',
        'google_calendar_event_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'kuota_peserta' => 'integer',
        ];
    }

    public function pengajuanKegiatan()
    {
        return $this->belongsTo(PengajuanKegiatan::class, 'pengajuan_kegiatan_id');
    }

    public function rencanaKegiatan()
    {
        return $this->belongsTo(RencanaKegiatan::class, 'rencana_kegiatan_id');
    }

    public function agenda()
    {
        return $this->hasMany(Agenda::class, 'kegiatan_id');
    }

    public function peserta()
    {
        return $this->hasMany(Peserta::class, 'kegiatan_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'peserta', 'kegiatan_id', 'user_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function laporanKegiatan()
    {
        return $this->hasOne(LaporanKegiatan::class, 'kegiatan_id');
    }
}
