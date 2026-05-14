<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('se_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('kode', 20)->unique(); // e.g. "SE-V001", "SE-V002"
            $table->string('nama', 100);           // e.g. "Versi 1", "Versi 2"
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(false)->index();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('activated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('activated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Hanya boleh 1 versi aktif sekaligus — di-enforce di aplikasi level
            // (MySQL tidak support partial unique index, jadi guard di Controller/Service)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('se_versions');
    }
};
