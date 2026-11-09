<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DinasTabel extends Model
{
    use HasFactory;

    protected $table = 'dinas_tabels';

    protected $fillable = [
        'dinas_id',
        'no_tabel',
        'no_tabel_lsda2025',
        'nama_tabel',
        'subjek_web',
        'data_masuk',
        'status_pengisian',
        'catatan_bps',
        'catatan_dinas',
        'jumlah_tabel_web',
    ];

    protected $casts = [
        'data_masuk' => 'boolean',
    ];

    /**
     * Relasi ke instansi/dinas pemilik tabel
     */
    public function dinas(): BelongsTo
    {
        return $this->belongsTo(Dinas::class, 'dinas_id');
    }

    /**
     * Scope filter status pengisian dinas
     */
    public function scopeSudahDiisi($query)
    {
        return $query->where('status_pengisian', 'sudah_diisi');
    }

    public function scopeBelumDiisi($query)
    {
        return $query->where('status_pengisian', '!=', 'sudah_diisi');
    }

    /**
     * Scope filter data masuk verifikasi BPS
     */
    public function scopeDataMasuk($query)
    {
        return $query->where('data_masuk', true);
    }
}

