<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE VIEW vw_product_stock_status AS
            SELECT
                p.id AS product_id,
                p.product_name,
                i.current_stock,
                i.minimum_stock,
                CASE
                    WHEN i.current_stock = 0 THEN 'OUT OF STOCK'
                    WHEN i.current_stock <= i.minimum_stock THEN 'LOW STOCK'
                    ELSE 'IN STOCK'
                END AS stock_status
            FROM products p
            JOIN inventories i ON p.id = i.product_id
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE VIEW vw_sales_detailed AS
            SELECT
                st.id AS sales_id,
                st.sales_date,
                p.product_name,
                sd.quantity,
                sd.unit_price,
                sd.subtotal
            FROM sales_transactions st
            JOIN sales_details sd ON st.id = sd.sales_transaction_id
            JOIN products p ON sd.product_id = p.id
            WHERE st.status != 'cancelled'
            ORDER BY st.sales_date DESC
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE OR REPLACE VIEW vw_unsold_products AS
            SELECT
                p.id,
                p.product_name
            FROM products p
            WHERE NOT EXISTS (
                SELECT 1
                FROM sales_details sd
                JOIN sales_transactions st ON st.id = sd.sales_transaction_id
                WHERE sd.product_id = p.id
                    AND st.status != 'cancelled'
            )
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP VIEW IF EXISTS vw_unsold_products');
        DB::unprepared('DROP VIEW IF EXISTS vw_sales_detailed');
        DB::unprepared('DROP VIEW IF EXISTS vw_product_stock_status');
    }
};
