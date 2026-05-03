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
        Schema::create('klasifikasi_asets', function (Blueprint $table) {
            $table->uuid('id')->primary(); // ← pastikan sudah UUID
            $table->string('kodeklas', 10)->unique();
            $table->string('klasifikasiaset', 100);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klasifikasi_asets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
