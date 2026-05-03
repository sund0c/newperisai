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
        Schema::create('sub_klasifikasi_asets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('klasifikasi_aset_id')->constrained('klasifikasi_asets')->restrictOnDelete();
            $table->string('subklasifikasiaset');
            $table->text('penjelasan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sub_klasifikasi_asets', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
