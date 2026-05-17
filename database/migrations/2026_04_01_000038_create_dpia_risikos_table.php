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

            $table->text('ancaman');
            $table->enum('likelihood', ['Rendah', 'Sedang', 'Tinggi']);
            $table->enum('dampak',     ['Rendah', 'Sedang', 'Tinggi']);
            $table->enum('level',      ['Rendah', 'Sedang', 'Tinggi']);
            $table->text('rencana_mitigasi')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->index('dpia_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpia_risikos');
    }
};
