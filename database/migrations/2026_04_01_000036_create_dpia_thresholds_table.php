<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dpia_thresholds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('dpia_id');
            $table->foreign('dpia_id')->references('id')->on('dpias')->cascadeOnDelete();

            // 7 trigger Pasal 34 ayat 2 UU PDP
            $table->enum('indikator', [
                'keputusan_otomatis',
                'data_spesifik',
                'skala_besar',
                'evaluasi_penskoran',
                'pencocokan_data',
                'teknologi_baru',
                'membatasi_hak',
            ]);
            // Otomatis dari RoPA, tapi bisa dioverride
            $table->boolean('terpenuhi')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['dpia_id', 'indikator']);
            $table->index('dpia_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpia_thresholds');
    }
};
