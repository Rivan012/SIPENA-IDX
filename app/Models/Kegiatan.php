<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

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
