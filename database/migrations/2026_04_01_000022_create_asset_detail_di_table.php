<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_detail_di', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('asset_id');
            $table->enum('bentuk', ['Elektronik', 'Fisik', 'Keduanya'])->nullable();
            $table->string('lokasi_fisik', 300)->nullable();
            $table->string('lokasi_elektronik', 300)->nullable();
            $table->enum('format', [
                'Dokumen',
                'Spreadsheet',
                'Database',
                'Laporan',
                'Rekaman',
                'Sertifikat',
                'Source Code',
                'Lainnya'
            ])->nullable();
            $table->enum('klasifikasi_data', [
                'Publik',
                'Terbatas',
                'Rahasia',
                'Sangat Rahasia'
            ])->nullable();
            $table->string('retensi', 100)->nullable();
            $table->enum('enkripsi', ['Ya', 'Tidak'])->nullable();
            $table->string('metode_enkripsi', 200)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->unique('asset_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_detail_di');
    }
};
