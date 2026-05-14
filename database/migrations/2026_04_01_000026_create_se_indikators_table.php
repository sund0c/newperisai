<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('se_indikators', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('se_version_id')->constrained('se_versions')->cascadeOnDelete();
            $table->unsignedSmallInteger('urutan');
            $table->text('pertanyaan');
            $table->string('keterangan', 500)->nullable();

            // 3 pilihan jawaban — nilai otomatis by position (1/2/3)
            $table->string('pilihan_1', 255);
            $table->string('pilihan_2', 255);
            $table->string('pilihan_3', 255);

            $table->timestamps();
            $table->softDeletes();

            // Urutan tidak pakai unique constraint di DB — di-enforce di aplikasi level
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('se_indikators');
    }
};
