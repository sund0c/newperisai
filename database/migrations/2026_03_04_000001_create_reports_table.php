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
            $table->text('description');
            $table->string('affected_system')->nullable();

            // Jenis Insiden (enum - pilih 1)
            $table->enum('incident_type_reporter', [
                'data_breach',
                'web_defacement',
                'ransomware',
                'phishing',
                'malicious_software',
                'exploit',
                'account_hijacking',
                'advanced_persistence_threat',
                'peringatan_keamanan',
                'lainnya'
            ]);
            $table->string('incident_type_other')->nullable();

            $table->enum('incident_type_verified', [
                'data_breach',
                'web_defacement',
                'ransomware',
                'phishing',
                'malicious_software',
                'exploit',
                'account_hijacking',
                'advanced_persistence_threat',
                'peringatan_keamanan',
                'lainnya'
            ])->nullable();

            $table->string('poc_video_url');

            // Severity: pelapor pilih, support/admin bisa override
            $table->enum('severity_reporter', ['critical', 'high', 'medium', 'low']);
            $table->enum('severity_verified', ['critical', 'high', 'medium', 'low'])->nullable();

            // Status alur
            $table->enum('status', [
                'submitted',    // Diterima
                'validated',    // Divalidasi
                'certificate',  // Penerbitan e-Sertifikat
                'closed',       // Selesai
            ])->default('submitted');

            // Hasil validasi oleh support
            $table->enum('validation_result', ['valid', 'invalid', 'duplicate'])->nullable();

            // Alasan jika invalid atau duplicate
            $table->text('closed_reason')->nullable();

            // Path file e-certificate (PDF) di private storage
            $table->string('certificate_file')->nullable();
            $table->string('certificate_file_original')->nullable();

            // Catatan dari support/admin
            $table->text('admin_notes')->nullable();

            // Handler (support/admin yang menangani)
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();

            // Timestamp per tahap
            $table->timestamp('handled_at')->nullable();      // mulai ditangani
            $table->timestamp('validated_at')->nullable();    // saat divalidasi
            $table->timestamp('certificated_at')->nullable(); // saat e-cert diterbitkan
            $table->timestamp('closed_at')->nullable();       // saat selesai

            $table->boolean('is_historical')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa
            $table->index(['user_id', 'status']);
            $table->index(['status', 'validation_result']);
            $table->index('ticket_number');
            $table->index('incident_type_reporter');
        });

        Schema::create('report_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->cascadeOnDelete();

            $table->enum('type', ['image', 'document']);

            $table->string('original_name');
            $table->string('stored_name');
            $table->string('disk')->default('local');
            $table->string('path');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_attachments');
        Schema::dropIfExists('reports');
    }
};
