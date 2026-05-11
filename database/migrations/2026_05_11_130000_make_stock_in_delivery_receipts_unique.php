<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('stock_ins')
            ->orderBy('id')
            ->select('id', 'date_received')
            ->get()
            ->each(function ($stockIn) {
                $year = $stockIn->date_received ? date('Y', strtotime($stockIn->date_received)) : date('Y');

                DB::table('stock_ins')
                    ->where('id', $stockIn->id)
                    ->update([
                        'delivery_receipt_no' => 'DR-' . $year . '-' . str_pad($stockIn->id, 4, '0', STR_PAD_LEFT),
                    ]);
            });

        Schema::table('stock_ins', function (Blueprint $table) {
            $table->unique('delivery_receipt_no', 'stock_ins_delivery_receipt_no_unique');
        });
    }

    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropUnique('stock_ins_delivery_receipt_no_unique');
        });
    }
};
