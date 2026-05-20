<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_registers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode_rr', 50)->unique();
            $table->uuid('asset_id')->index(); // FK ke assets (plain index, assets dibuat modul lain)
            $table->foreignUuid('tahunaktif_id')->constrained('tahunaktifs');
            $table->unsignedBigInteger('opd_id');
            $table->foreign('opd_id')->references('id')->on('opds');
            $table->unsignedInteger('versi')->default(1);
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('dibuat_oleh')->nullable();
            $table->foreign('dibuat_oleh')->references('id')->on('users');
            $table->unsignedBigInteger('difinalisasi_oleh')->nullable();
            $table->foreign('difinalisasi_oleh')->references('id')->on('users');
            $table->timestamp('difinalisasi_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['asset_id', 'versi'], 'uq_rr_asset_versi');
        });

        Schema::create('risk_register_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('risk_register_id')->constrained('risk_registers')->cascadeOnDelete();
            $table->unsignedInteger('risk_no');

            $table->string('jenis_risiko', 100)->nullable();
            $table->text('ancaman');
            $table->text('kerawanan');
            $table->string('kategori', 100)->nullable();
            $table->text('dampak_detail')->nullable();
            $table->json('area_dampak')->nullable();

            $table->uuid('vulnerability_item_id')->nullable()->index();

            $table->text('kontrol_saat_ini')->nullable();
            $table->text('rencana_aksi')->nullable();

            $table->unsignedTinyInteger('inherent_dampak')->nullable();
            $table->unsignedTinyInteger('inherent_kemungkinan')->nullable();
            $table->unsignedTinyInteger('inherent_skor')->nullable();
            $table->string('inherent_level', 30)->nullable();

            $table->string('keputusan_penanganan', 50)->nullable();
            $table->string('prioritas_risiko', 30)->nullable();

            $table->string('opsi_penanganan', 100)->nullable();
            $table->text('keluaran')->nullable();
            $table->string('target_jadwal', 100)->nullable();
            $table->string('penanggung_jawab', 150)->nullable();

            $table->boolean('ada_residual_risk')->default(false);
            $table->unsignedTinyInteger('residual_dampak')->nullable();
            $table->unsignedTinyInteger('residual_kemungkinan')->nullable();
            $table->unsignedTinyInteger('residual_skor')->nullable();
            $table->string('residual_level', 30)->nullable();
            $table->string('residual_status', 30)->nullable();

            $table->text('rencana_kontrol_tambahan')->nullable();
            $table->string('risk_owner', 150)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_register_items');
        Schema::dropIfExists('risk_registers');
    }
};
