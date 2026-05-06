<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('supplier_name');
            $table->string('last_name')->nullable()->after('first_name');
        });

        if (Schema::hasColumn('suppliers', 'contact_person')) {
            $suppliers = DB::table('suppliers')->select('id', 'contact_person')->get();

            foreach ($suppliers as $supplier) {
                if (!$supplier->contact_person) {
                    continue;
                }

                $parts = preg_split('/\s+/', trim($supplier->contact_person), 2);

                DB::table('suppliers')
                    ->where('id', $supplier->id)
                    ->update([
                        'first_name' => $parts[0] ?? null,
                        'last_name' => $parts[1] ?? null,
                    ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};
