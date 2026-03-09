<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('csirt_processes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('report_id')
                ->constrained('reports')
                ->cascadeOnDelete();

            // CSIRT yang menangani
            $table->foreignId('handled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Status proses mitigasi CSIRT
            $table->enum('status', ['notified', 'in_progress', 'closed'])
                ->default('notified');

            // Catatan proses mitigasi
            $table->text('notes')->nullable();

            // File laporan mitigasi (PDF)
            $table->string('mitigation_file')->nullable();
            $table->string('mitigation_file_original')->nullable();

            // Timestamp per tahap
            $table->timestamp('notified_at')->nullable();  // saat tiket jadi VALID
            $table->timestamp('started_at')->nullable();   // saat CSIRT tekan PROSES
            $table->timestamp('closed_at')->nullable();    // saat CSIRT tekan CLOSED

            $table->timestamps();

            $table->index(['report_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('csirt_processes');
    }
};
