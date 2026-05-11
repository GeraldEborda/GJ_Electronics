<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('payments')
            ->where('status', 'unpaid')
            ->update(['status' => 'partial']);

        DB::statement("ALTER TABLE payments MODIFY status ENUM('paid','partial') NOT NULL DEFAULT 'partial'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY status ENUM('paid','partial','unpaid') NOT NULL DEFAULT 'unpaid'");
    }
};
