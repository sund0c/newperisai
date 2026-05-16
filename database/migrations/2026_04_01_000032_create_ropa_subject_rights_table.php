<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ropa_subject_rights', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ropa_activity_id');
            $table->foreign('ropa_activity_id')->references('id')->on('ropa_activities')->cascadeOnDelete();
            $table->tinyInteger('pasal')->unsigned();
            $table->string('nama_hak');
            $table->timestamps();
            $table->unique(['ropa_activity_id', 'pasal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ropa_subject_rights');
    }
};
