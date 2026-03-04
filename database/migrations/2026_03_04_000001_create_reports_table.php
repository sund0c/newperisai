<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // Nomor tiket unik: BALIPROV-CSIRT-YYYY-XXXX
            $table->string('ticket_number', 30)->unique();

            // Pelapor
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Informasi laporan
            $table->string('title');
            $table->text('description');                    // Deskripsi aduan (mandatory)
            $table->string('affected_system')->nullable();  // Sistem/URL yang terdampak
            $table->string('poc_video_url');                // Link video PoC (mandatory)

            // Severity: pelapor pilih, support/admin bisa override
            $table->enum('severity_reporter', ['critical', 'high', 'medium', 'low']);
            $table->enum('severity_verified', ['critical', 'high', 'medium', 'low'])->nullable();

            // Status alur
            $table->enum('status', [
                'submitted',    // Diterima
                'processing',   // Diproses Kelengkapan
                'validated',    // Divalidasi
                'certificate',  // Penerbitan e-Sertifikat
                'closed',       // Selesai
            ])->default('submitted');

            // Catatan dari support/admin per status
            $table->text('admin_notes')->nullable();

            // Handler (support/admin yang menangani)
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa
            $table->index(['user_id', 'status']);
            $table->index('ticket_number');
        });

        Schema::create('report_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();

            // Tipe attachment
            $table->enum('type', ['image', 'document']); // image = JPG/PNG, document = PDF

            $table->string('original_name');   // nama file asli
            $table->string('stored_name');     // nama file di storage (UUID)
            $table->string('disk')->default('local'); // storage disk
            $table->string('path');            // path di storage
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size'); // bytes

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_attachments');
        Schema::dropIfExists('reports');
    }
};
