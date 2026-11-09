<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dinas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_dinas')->unique();
            $table->text('pin'); // Enkripsi simetris AES-256
            $table->text('spreadsheet_url')->nullable();
            $table->string('shortlink')->nullable();
            $table->string('petugas_bps')->nullable();
            $table->text('evaluasi')->nullable();
            $table->boolean('is_first_login')->default(true);
            $table->string('pic_name')->nullable();
            $table->string('pic_phone')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dinas');
    }
};

