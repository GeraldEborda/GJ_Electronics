<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->date('payment_date')->nullable()->after('sales_transaction_id');
            $table->foreignId('payment_method_id')->nullable()->after('amount_paid')->constrained('payment_methods')->nullOnDelete();
        });

        if (Schema::hasColumn('payments', 'payment_method') && Schema::hasTable('payment_methods')) {
            $methods = DB::table('payments')
                ->select('payment_method')
                ->whereNotNull('payment_method')
                ->distinct()
                ->pluck('payment_method');

            foreach ($methods as $methodName) {
                $id = DB::table('payment_methods')->insertGetId([
                    'payment_method_name' => ucwords(str_replace('_', ' ', $methodName)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('payments')
                    ->where('payment_method', $methodName)
                    ->update(['payment_method_id' => $id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('payment_method_id');
            $table->dropColumn('payment_date');
        });
    }
};
