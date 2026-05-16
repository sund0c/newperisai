<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ropa_recipients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ropa_activity_id');
            $table->foreign('ropa_activity_id')->references('id')->on('ropa_activities')->cascadeOnDelete();
            $table->string('profil_penerima');
            $table->enum('tipe', ['internal', 'eksternal']);
            $table->enum('peran', ['pengendali', 'pengendali_bersama', 'prosesor'])->nullable();
            $table->string('kontak_pic')->nullable();
            $table->text('tujuan_pengiriman')->nullable();
            $table->text('jenis_data_dikirim')->nullable();
            $table->text('mekanisme_pengiriman')->nullable();
            $table->timestamps();
            $table->index('ropa_activity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ropa_recipients');
    }
};
