<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    protected $appends = ['kode', 'status_label', 'status_class'];

    // status => [label, badge classes]
    public const STATUS = [
        'menunggu_approval' => ['Menunggu Approval', 'bg-amber-50 text-amber-700'],
        'diproses' => ['Sedang Ditinjau', 'bg-sky-50 text-sky-700'],
        'disetujui' => ['Disetujui', 'bg-emerald-100 text-emerald-800 border-emerald-200'],
        'revisi' => ['Perlu Revisi', 'bg-amber-50 text-amber-700'],
        'ditolak' => ['Ditolak', 'bg-red-100 text-red-800'],
        'dipublikasikan' => ['Terjadwal', 'bg-sky-50 text-sky-700'],
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

    public function scopePublicSchedule(Builder $query): Builder
    {
        return $query
            ->where('status', 'dipublikasikan')
            ->whereDate('tanggal', '>=', today());
    }

    protected $fillable = [
        'user_id',
        'proposal_file_path',
        'reviewed_by_admin_id',
        'approval_note',
        'approval_processed_at',
        'nama_kegiatan',
        'deskripsi',
        'jenis_kegiatan',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'mitra',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewedByAdmin()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by_admin_id');
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
