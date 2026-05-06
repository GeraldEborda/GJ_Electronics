<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_sales_deduct_inventory');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_sales_update_total');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_stockin_add_inventory');

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_sales_deduct_inventory
            AFTER INSERT ON sales_details
            FOR EACH ROW
            BEGIN
                UPDATE inventories
                SET current_stock = current_stock - NEW.quantity
                WHERE product_id = NEW.product_id;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_sales_update_total
            AFTER INSERT ON sales_details
            FOR EACH ROW
            BEGIN
                UPDATE sales_transactions
                SET total_amount = (
                    SELECT COALESCE(SUM(subtotal), 0)
                    FROM sales_details
                    WHERE sales_transaction_id = NEW.sales_transaction_id
                )
                WHERE id = NEW.sales_transaction_id;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_stockin_add_inventory
            AFTER INSERT ON stock_in_details
            FOR EACH ROW
            BEGIN
                IF NEW.condition_status = 'good' THEN
                    UPDATE inventories
                    SET current_stock = current_stock + NEW.quantity_received
                    WHERE product_id = NEW.product_id;
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_stockin_add_inventory');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_sales_update_total');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_sales_deduct_inventory');
    }
};
