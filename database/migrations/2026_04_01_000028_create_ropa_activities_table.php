<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ropa_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('tahunaktif_id', 36);
            $table->foreign('tahunaktif_id')->references('id')->on('tahunaktifs')->restrictOnDelete();
            $table->unsignedBigInteger('opd_id');
            $table->foreign('opd_id')->references('id')->on('opds')->restrictOnDelete();

            $table->string('kode', 20);
            $table->string('nama_aktivitas');
            $table->string('penanggung_jawab');
            $table->text('deskripsi_tujuan');

            $table->string('subjek_data');
            $table->string('sumber_pemerolehan');

            $table->text('penyimpanan_data')->nullable();
            $table->boolean('metode_elektronik')->default(false);
            $table->boolean('metode_non_elektronik')->default(false);
            $table->text('referensi_dasar_hukum')->nullable();
            $table->text('masa_retensi')->nullable();

            $table->text('langkah_teknis')->nullable();
            $table->text('langkah_organisasi')->nullable();

            $table->text('proses_sebelumnya')->nullable();
            $table->text('proses_setelahnya')->nullable();
            $table->text('catatan')->nullable();

            $table->text('narasi_risiko')->nullable();

            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['kode', 'tahunaktif_id']);
            $table->index(['tahunaktif_id', 'opd_id']);
            $table->index('kode');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ropa_activities');
    }
};
