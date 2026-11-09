<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;
use Throwable;

class Dinas extends Authenticatable
{
    use HasFactory;

    protected $table = 'dinas';

    protected $fillable = [
        'nama_dinas',
        'pin',
        'spreadsheet_url',
        'shortlink',
        'petugas_bps',
        'evaluasi',
        'is_first_login',
        'pic_name',
        'pic_phone',
        'last_login_at',
    ];

    /**
     * Relasi ke daftar tabel yang menjadi tanggung jawab dinas ini
     */
    public function tabels()
    {
        return $this->hasMany(DinasTabel::class, 'dinas_id');
    }

    protected $casts = [
        'is_first_login' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Set PIN terenkripsi secara otomatis
     */
    public function setPinAttribute($value): void
    {
        $this->attributes['pin'] = Crypt::encryptString(trim($value));
    }

    /**
     * Dapatkan PIN asli yang telah didekripsi untuk kebutuhan Admin BPS
     */
    public function getPlainPinAttribute(): string
    {
        try {
            return Crypt::decryptString($this->attributes['pin']);
        } catch (Throwable $e) {
            return '---';
        }
    }

    /**
     * Verifikasi kecocokan PIN saat dinas login
     */
    public function verifyPin(string $inputPin): bool
    {
        return $this->plain_pin === trim($inputPin);
    }

    /**
     * Konversi URL Google Spreadsheet menjadi URL yang siap di-embed dalam iframe
     */
    public function getEmbedSpreadsheetUrlAttribute(): ?string
    {
        if (!$this->spreadsheet_url) {
            return null;
        }

        $url = trim($this->spreadsheet_url);

        // Jika URL Google Spreadsheet
        if (str_contains($url, 'docs.google.com/spreadsheets')) {
            // Hapus parameter hash fragment atau edit akhir jika ada
            if (str_contains($url, '/edit')) {
                // Tambahkan parameter widget=true&headers=false agar tampilan bersih di iframe
                if (str_contains($url, '?')) {
                    if (!str_contains($url, 'widget=')) {
                        $url .= '&widget=true&headers=false';
                    }
                } else {
                    $url .= '?widget=true&headers=false';
                }
            }
        }

        return $url;
    }
}

