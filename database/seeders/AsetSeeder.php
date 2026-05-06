<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AsetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedAssetPeriods();
    }

    // =========================================================================
    // ASSET PERIODS
    // =========================================================================

    private function seedAssetPeriods(): void
    {
        $periods = [
            [
                'id'              => 'a1b6e420-0918-457d-a41d-3f9a122c4c6d',
                'nama_periode'    => 'DIA 2026',
                'jenis_periode'   => 'aset',
                'tanggal_mulai'   => '2026-01-01',
                'tanggal_selesai' => '2026-05-31',
                'is_active'       => true,
                'created_at'      => '2026-05-06 20:00:34',
                'updated_at'      => '2026-05-06 20:00:34',
            ],
        ];

        DB::table('asset_periods')->upsert(
            $periods,
            ['id'],
            ['nama_periode', 'jenis_periode', 'tanggal_mulai', 'tanggal_selesai', 'is_active', 'updated_at']
        );

        $this->command->info('  ✓ asset_periods — ' . count($periods) . ' record(s) seeded.');
    }
}
