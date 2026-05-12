<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * IIV = Infrastruktur Informasi Vital
     *
     * 5 dimensi dampak, masing-masing bernilai:
     *   1 = MINOR    → Gangguan sangat kecil / tidak berdampak
     *   2 = TERBATAS → Gangguan terbatas pada layanan, skup PROVINSI
     *   3 = KRITIS   → Gangguan skala NASIONAL, pemulihan > 24 jam
     *
     * Nilai IIV final:
     *   Ada satu KRITIS (3)          → KRITIS
     *   Ada satu TERBATAS tanpa KRITIS → TERBATAS
     *   Semua MINOR                   → MINOR
     */
    public function up(): void
    {
        Schema::create('asset_iivs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // FK ke tabel assets (master aset), 1-to-1
            $table->uuid('asset_id');
            $table->foreign('asset_id')
                ->references('id')
                ->on('assets')
                ->onDelete('cascade');

            /**
             * Dimensi 1: Dampak Operasional
             * Seberapa besar gangguan terhadap kelangsungan operasional
             * layanan/sistem pemerintahan.
             */
            $table->unsignedTinyInteger('dampak_operasional')
                ->comment('1=MINOR, 2=TERBATAS, 3=KRITIS');

            /**
             * Dimensi 2: Dampak terhadap Data/Informasi
             * Seberapa besar potensi kerugian atas integritas, kerahasiaan,
             * atau ketersediaan data/informasi milik pemerintah/masyarakat.
             */
            $table->unsignedTinyInteger('dampak_data_informasi')
                ->comment('1=MINOR, 2=TERBATAS, 3=KRITIS');

            /**
             * Dimensi 3: Dampak Finansial
             * Estimasi kerugian finansial yang ditimbulkan jika aset
             * mengalami gangguan atau kegagalan.
             */
            $table->unsignedTinyInteger('dampak_finansial')
                ->comment('1=MINOR, 2=TERBATAS, 3=KRITIS');

            /**
             * Dimensi 4: Dampak Umum / Sosial
             * Potensi kegaduhan, kepanikan, atau gangguan terhadap
             * ketentraman masyarakat secara luas.
             */
            $table->unsignedTinyInteger('dampak_umum')
                ->comment('1=MINOR, 2=TERBATAS, 3=KRITIS');

            /**
             * Dimensi 5: Dampak Saling Ketergantungan
             * Seberapa besar ketergantungan sistem/layanan lain terhadap
             * aset ini; kegagalan satu memicu kegagalan berantai.
             */
            $table->unsignedTinyInteger('dampak_ketergantungan')
                ->comment('1=MINOR, 2=TERBATAS, 3=KRITIS');

            /**
             * Nilai IIV final: max dari ke-5 dimensi di atas.
             * 3=KRITIS, 2=TERBATAS, 1=MINOR
             */
            $table->unsignedTinyInteger('nilai_iiv')
                ->comment('Nilai IIV final: max(5 dimensi)');

            // Audit trail
            $table->uuid('assessed_by')->nullable();
            $table->foreign('assessed_by')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Constraint: satu aset hanya punya satu record IIV
            $table->unique('asset_id');

            // Index untuk filter/sort
            $table->index('nilai_iiv');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_iivs');
    }
};
