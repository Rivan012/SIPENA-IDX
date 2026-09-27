<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKegiatan extends Model
{
    use HasFactory;

    protected $table = 'detail_kegiatan';

    protected $fillable = [
        'rencana_kegiatan_id',
        'deskripsi',
        'jenis_kegiatan',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'target_peserta',
        'anggaran',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'anggaran' => 'decimal:2',
            'target_peserta' => 'integer',
        ];
    }

    public function rencanaKegiatan()
    {
        return $this->belongsTo(RencanaKegiatan::class, 'rencana_kegiatan_id');
    }
}
