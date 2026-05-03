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
            $table->year('tahun')->unique();      // ← Tahun periode penilaian
            $table->boolean('is_active')->default(false); // hanya 1 aktif sekaligus
            $table->timestamps();
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
