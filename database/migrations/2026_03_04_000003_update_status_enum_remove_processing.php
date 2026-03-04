<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Update data lama yang masih 'processing' jadi 'validated'
        DB::table('reports')->where('status', 'processing')->update(['status' => 'validated']);
        DB::table('report_status_logs')->where('status', 'processing')->update(['status' => 'validated']);

        // Update enum kolom reports.status
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('submitted','validated','certificate','closed') NOT NULL DEFAULT 'submitted'");

        // Update enum kolom report_status_logs.status
        DB::statement("ALTER TABLE report_status_logs MODIFY COLUMN status ENUM('submitted','validated','certificate','closed') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE reports MODIFY COLUMN status ENUM('submitted','processing','validated','certificate','closed') NOT NULL DEFAULT 'submitted'");
        DB::statement("ALTER TABLE report_status_logs MODIFY COLUMN status ENUM('submitted','processing','validated','certificate','closed') NOT NULL");
    }
};
