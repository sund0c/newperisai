<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();

            // Kode unik template — digunakan di kode untuk memanggil template
            $table->string('code')->unique();

            // Nama deskriptif untuk ditampilkan di UI admin
            $table->string('name');

            // Deskripsi singkat kegunaan template
            $table->text('description')->nullable();

            // Subject email — bisa mengandung placeholder: {{ticket_number}}, {{name}}, dll
            $table->string('subject');

            // Body email dalam format HTML — bisa mengandung placeholder
            $table->longText('body');

            // Variabel yang tersedia untuk template ini (JSON array)
            $table->json('available_variables')->nullable();

            // Apakah template aktif
            $table->boolean('is_active')->default(true);

            // Siapa yang terakhir update
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
