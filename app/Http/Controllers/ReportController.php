<?php

namespace App\Http\Controllers;

use App\Models\SalesTransaction;
use App\Models\SalesDetail;
use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $totalRevenue      = SalesTransaction::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalTransactions = SalesTransaction::count();
        $totalProducts     = Product::count();
        $avgTransaction    = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Monthly sales summary
        $monthlySales = SalesTransaction::where('status', '!=', 'cancelled')
            ->select(
                DB::raw('YEAR(sales_date) as year'),
                DB::raw('MONTH(sales_date) as month'),
                DB::raw('SUM(total_amount) as total_sales'),
                DB::raw('COUNT(*) as transactions')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')->orderBy('month')
            ->get()
            ->map(function ($row) {
                $row->avg_per_transaction = $row->transactions > 0
                    ? $row->total_sales / $row->transactions : 0;
                return $row;
            });

        // Top products by revenue
        $topProducts = SalesDetail::with('product')
            ->select('product_id',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_revenue')
            ->take(5)
            ->get();

        // Inventory report
        $inventories = Inventory::with(['product.category', 'product.supplier'])->get();

        // SQL view-backed reports
        $productStockStatuses = DB::table('vw_product_stock_status')
            ->orderBy('product_name')
            ->get();

        $detailedSalesView = DB::table('vw_sales_detailed')
            ->orderByDesc('sales_date')
            ->get();

        $unsoldProductsView = DB::table('vw_unsold_products')
            ->orderBy('product_name')
            ->get();

        return view('reports.index', compact(
            'totalRevenue', 'totalTransactions', 'totalProducts', 'avgTransaction',
            'monthlySales', 'topProducts', 'inventories',
            'productStockStatuses', 'detailedSalesView', 'unsoldProductsView'
        ));
    }
}
