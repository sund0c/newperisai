<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_detail_pl', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('asset_id');
            $table->string('url', 500)->nullable();
            $table->string('versi', 50)->nullable();
            $table->enum('lisensi', ['Proprietary', 'Open Source', 'Freeware', 'In-House'])->nullable();
            $table->date('tgl_lisensi_berakhir')->nullable();
            $table->enum('vendor', ['Diskominfos Prov Bali', 'Mandiri', 'Pihak Ketiga'])->nullable();
            $table->string('lead_developer', 200)->nullable();
            $table->enum('platform', ['Web', 'Mobile', 'Desktop'])->nullable();
            $table->enum('lokasi_hosting', ['Pusat Data BALIPROV', 'PDN KOMDIGI', 'Cloud AWS Diskominfos Prov Bali', 'Lain-lain'])->nullable();
            $table->string('nama_server_lainnya', 200)->nullable();
            $table->string('nama_server', 200)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->unique('asset_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('asset_detail_pl');
    }
};
