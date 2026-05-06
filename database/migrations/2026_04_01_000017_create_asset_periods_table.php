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
        Schema::create('asset_periods', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama_periode', 100);
            $table->string('jenis_periode', 50);          // gunakan konstanta model
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->index(['jenis_periode', 'is_active']);
            $table->index(['jenis_periode', 'tanggal_mulai', 'tanggal_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_periods');
    }
};
