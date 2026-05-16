<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ropa_legal_bases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ropa_activity_id');
            $table->foreign('ropa_activity_id')->references('id')->on('ropa_activities')->cascadeOnDelete();
            $table->enum('dasar_pemrosesan', [
                'consent',
                'contractual',
                'legal_obligation',
                'vital_interests',
                'public_interests',
                'legitimate_interests',
                'keseimbangan_kepentingan',
            ]);
            $table->string('keterangan')->nullable();
            $table->timestamps();
            $table->unique(['ropa_activity_id', 'dasar_pemrosesan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ropa_legal_bases');
    }
};
