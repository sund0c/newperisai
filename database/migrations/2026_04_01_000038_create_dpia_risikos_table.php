<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dpia_risikos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('dpia_id');
            $table->foreign('dpia_id')->references('id')->on('dpias')->cascadeOnDelete();

            // C.2 — satu ancaman utama
            $table->text('ancaman');
            $table->enum('likelihood', ['Rendah', 'Sedang', 'Tinggi']);
            $table->enum('dampak',     ['Rendah', 'Sedang', 'Tinggi']);
            $table->enum('level',      ['Rendah', 'Sedang', 'Tinggi']);
            // Mitigasi merujuk ke RoPA Bab IV
            $table->string('referensi_mitigasi')->nullable(); // e.g. "RoPA-0001 Bab IV Pengamanan Data"

            // C.3 — evaluasi residual per kategori kontrol
            $table->text('residual_technical')->nullable();
            $table->text('residual_privacy')->nullable();
            $table->text('residual_organizational')->nullable();

            $table->timestamps();
            $table->unique('dpia_id'); // 1 risiko per DPIA
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpia_risikos');
    }
};
