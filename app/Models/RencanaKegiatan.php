<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RencanaKegiatan extends Model
{
    use HasFactory;

    protected $table = 'rencana_kegiatan';

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
}
