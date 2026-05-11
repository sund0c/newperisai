<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_detail_sk', function (Blueprint $table) {
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('asset_id');
            $table->string('jabatan', 200);                             // required
            $table->string('unit_kerja', 200);                         // required
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->enum('tipe', ['Internal', 'Vendor', 'Kontraktor']);   // required
            $table->string('akses_sistem', 1000)->nullable();
            $table->date('tgl_kontrak_berakhir')->nullable();           // NULL = ASN/permanen
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->unique('asset_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('asset_detail_sk');
    }
};
