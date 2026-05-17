<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dpias', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('tahunaktif_id', 36);
            $table->foreign('tahunaktif_id')->references('id')->on('tahunaktifs')->restrictOnDelete();
            $table->unsignedBigInteger('opd_id');
            $table->foreign('opd_id')->references('id')->on('opds')->restrictOnDelete();

            // Relasi ke RoPA — unique: 1 DPIA per RoPA
            $table->uuid('ropa_activity_id')->unique();
            $table->foreign('ropa_activity_id')->references('id')->on('ropa_activities')->restrictOnDelete();

            // Identitas
            $table->string('kode', 20);
            $table->string('nama_aktivitas');
            $table->string('penanggung_jawab');
            $table->string('ppd')->nullable();          // Pejabat Pelindung Data
            $table->date('tanggal_penyusunan');
            $table->string('versi', 10)->default('1.0');

            // Bagian B
            $table->text('konsultasi_stakeholder')->nullable();

            // Bagian C
            $table->text('kriteria_risiko')->nullable();
            $table->text('evaluasi_residual')->nullable();

            // Bagian D
            $table->text('kesimpulan')->nullable();

            // Audit
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['kode', 'tahunaktif_id']);
            $table->index(['tahunaktif_id', 'opd_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpias');
    }
};
