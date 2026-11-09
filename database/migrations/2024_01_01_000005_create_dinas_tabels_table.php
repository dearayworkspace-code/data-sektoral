<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dinas_tabels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dinas_id')->constrained('dinas')->cascadeOnDelete();
            $table->string('no_tabel')->nullable();
            $table->string('no_tabel_lsda2025')->nullable();
            $table->text('nama_tabel');
            $table->string('subjek_web')->nullable();
            $table->boolean('data_masuk')->default(false); // Status verifikasi dari BPS (True/False)
            $table->string('status_pengisian')->default('belum_diisi'); // Interaktif dinas: 'belum_diisi' / 'sudah_diisi'
            $table->text('catatan_bps')->nullable(); // Catatan/revisi dari BPS
            $table->text('catatan_dinas')->nullable(); // Keterangan/catatan dari pihak dinas
            $table->string('jumlah_tabel_web')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dinas_tabels');
    }
};

