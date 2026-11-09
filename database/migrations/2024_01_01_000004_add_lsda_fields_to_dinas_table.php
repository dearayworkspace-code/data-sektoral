<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dinas', function (Blueprint $table) {
            if (!Schema::hasColumn('dinas', 'shortlink')) {
                $table->string('shortlink')->nullable()->after('spreadsheet_url');
            }
            if (!Schema::hasColumn('dinas', 'petugas_bps')) {
                $table->string('petugas_bps')->nullable()->after('shortlink');
            }
            if (!Schema::hasColumn('dinas', 'evaluasi')) {
                $table->text('evaluasi')->nullable()->after('petugas_bps');
            }
        });
    }

    public function down(): void
    {
        Schema::table('dinas', function (Blueprint $table) {
            $table->dropColumn(['shortlink', 'petugas_bps', 'evaluasi']);
        });
    }
};

