<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('csirt_activity_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('csirt_process_id')
                ->constrained('csirt_processes')
                ->cascadeOnDelete();

            $table->foreignId('logged_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // Jenis aktivitas (opsional, untuk filter/ikon)
            $table->enum('type', [
                'update',       // update umum
                'notification', // kirim surat/notifikasi ke pemilik aset
                'coordination', // koordinasi dengan pihak lain
                'technical',    // tindakan teknis
                'other',
            ])->default('update');

            $table->string('title', 200);     // judul singkat aktivitas
            $table->text('body')->nullable();  // detail/penjelasan

            $table->timestamps();

            $table->index('csirt_process_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('csirt_activity_logs');
    }
};
