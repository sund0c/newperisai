<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ropa_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ropa_activity_id');
            $table->foreign('ropa_activity_id')->references('id')->on('ropa_activities')->cascadeOnDelete();
            $table->uuid('asset_instance_id')->nullable();
            $table->foreign('asset_instance_id')->references('id')->on('assets')->nullOnDelete();
            $table->string('nama_manual')->nullable();
            $table->enum('peran_aset', ['primer', 'pendukung', 'penyimpanan', 'transmisi'])->default('primer');
            $table->timestamps();
            $table->index('ropa_activity_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ropa_assets');
    }
};
