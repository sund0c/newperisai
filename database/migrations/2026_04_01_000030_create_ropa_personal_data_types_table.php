<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ropa_personal_data_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ropa_activity_id');
            $table->foreign('ropa_activity_id')->references('id')->on('ropa_activities')->cascadeOnDelete();
            $table->boolean('is_spesifik')->default(false);
            $table->string('jenis_data');
            $table->timestamps();
            $table->index(['ropa_activity_id', 'is_spesifik']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ropa_personal_data_types');
    }
};
