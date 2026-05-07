<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('opd_id')->constrained()->restrictOnDelete();         // ← fix
            $table->foreignUuid('sub_klasifikasi_id')->constrained('sub_klasifikasi_asets')->restrictOnDelete();
            $table->foreignUuid('tahunaktif_id')->constrained('tahunaktifs')->restrictOnDelete();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            $table->string('kode_aset', 30)->unique();
            $table->string('nama_aset', 200);
            $table->text('keterangan')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['opd_id', 'tahunaktif_id', 'sub_klasifikasi_id']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
