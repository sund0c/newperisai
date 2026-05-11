<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_criticalities', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // FK ke tabel assets (master aset)
            $table->uuid('asset_id');
            $table->foreign('asset_id')
                ->references('id')
                ->on('assets')
                ->onDelete('cascade');

            // CIA scores: 1=Rendah, 2=Sedang, 3=Tinggi
            $table->unsignedTinyInteger('confidentiality')->comment('1=Rendah, 2=Sedang, 3=Tinggi');
            $table->unsignedTinyInteger('integrity')->comment('1=Rendah, 2=Sedang, 3=Tinggi');
            $table->unsignedTinyInteger('availability')->comment('1=Rendah, 2=Sedang, 3=Tinggi');

            // Computed: max(C,I,A) → 1=Rendah, 2=Sedang, 3=Tinggi
            $table->unsignedTinyInteger('kritikalitas')->comment('Nilai tertinggi dari C, I, A');

            // Audit trail — users.id adalah unsignedBigInteger (default Laravel $table->id())
            $table->unsignedBigInteger('assessed_by')->nullable();
            $table->foreign('assessed_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->timestamps();

            // Satu aset hanya punya satu record kritikalitas
            $table->unique('asset_id');

            // Index untuk sorting dan filtering
            $table->index('kritikalitas');
            $table->index('assessed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_criticalities');
    }
};
