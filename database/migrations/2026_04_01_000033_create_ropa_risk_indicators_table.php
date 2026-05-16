<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ropa_risk_indicators', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ropa_activity_id');
            $table->foreign('ropa_activity_id')
                  ->references('id')->on('ropa_activities')
                  ->cascadeOnDelete();

            // 7 indikator risiko tinggi Pasal 34 ayat 2 UU PDP
            $table->enum('indikator', [
                'keputusan_otomatis',
                'data_spesifik',
                'skala_besar',
                'evaluasi_penskoran',
                'pencocokan_data',
                'teknologi_baru',
                'membatasi_hak',
            ]);
            $table->timestamps();

            $table->unique(['ropa_activity_id', 'indikator']);
            $table->index('ropa_activity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ropa_risk_indicators');
    }
};
