<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_detail_pk', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('asset_id');
            $table->string('merk', 100)->nullable();
            $table->string('model', 100)->nullable();
            $table->string('serial_number', 100)->nullable();
            $table->year('tahun_perolehan')->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->nullable();
            $table->string('lokasi_fisik', 300)->nullable();
            $table->string('ip_address', 50)->nullable();
            $table->string('spesifikasi', 1000)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->unique('asset_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_detail_pk');
    }
};
