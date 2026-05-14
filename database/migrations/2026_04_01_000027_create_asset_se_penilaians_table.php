<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::create('se_penilaians', function (Blueprint $table) {
        //     $table->uuid('id')->primary();
        //     // FK ke asset_instances ditambahkan via migration terpisah
        //     $table->uuid('asset_instance_id')->nullable()->index();
        //     $table->foreignUuid('se_version_id')->constrained('se_versions')->restrictOnDelete();
        //     $table->enum('status', ['draft', 'final'])->default('draft');
        //     $table->text('catatan')->nullable();
        //     $table->decimal('total_nilai', 5, 2)->nullable();
        //     $table->string('kategori_se', 50)->nullable();
        //     $table->unsignedBigInteger('dinilai_oleh')->nullable();
        //     $table->unsignedBigInteger('diverifikasi_oleh')->nullable();
        //     $table->foreign('dinilai_oleh')->references('id')->on('users')->nullOnDelete();
        //     $table->foreign('diverifikasi_oleh')->references('id')->on('users')->nullOnDelete();
        //     $table->timestamp('dinilai_at')->nullable();
        //     $table->timestamp('diverifikasi_at')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // Schema::create('se_penilaian_jawabans', function (Blueprint $table) {
        //     $table->uuid('id')->primary();
        //     $table->foreignUuid('se_penilaian_id')->constrained('se_penilaians')->cascadeOnDelete();
        //     $table->foreignUuid('se_indikator_id')->constrained('se_indikators')->restrictOnDelete();
        //     $table->unsignedSmallInteger('urutan_indikator');
        //     $table->text('pertanyaan_snapshot');
        //     $table->string('pilihan_dipilih', 255);
        //     $table->tinyInteger('nilai_dipilih');
        //     $table->text('catatan_jawaban')->nullable();
        //     $table->timestamps();
        //     $table->unique(['se_penilaian_id', 'se_indikator_id']);
        // });

        Schema::create('asset_se_penilaians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('asset_id')->constrained('assets')->cascadeOnDelete();
            $table->foreignUuid('se_version_id')->constrained('se_versions')->restrictOnDelete();
            $table->foreignUuid('tahunaktif_id')->constrained('tahunaktifs')->restrictOnDelete();

            // Jawaban: JSON map {indikator_id: "a"|"b"|"c"}
            $table->json('jawabans');

            // Hasil kalkulasi (denormalized untuk performa query)
            $table->unsignedSmallInteger('total_nilai')->default(0);
            $table->enum('kategori_se', ['STRATEGIS', 'TINGGI', 'RENDAH'])->nullable();

            // Audit
            $table->unsignedBigInteger('dinilai_oleh')->nullable();
            $table->foreign('dinilai_oleh')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('dinilai_pada')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // Satu aset hanya bisa dinilai satu kali per tahun (non-deleted)
            $table->unique(['asset_id', 'tahunaktif_id']);
        });
    }

    public function down(): void
    {
        //Schema::dropIfExists('se_penilaian_jawabans');
        Schema::dropIfExists('se_penilaians');
    }
};
