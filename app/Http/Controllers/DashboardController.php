<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\SalesTransaction;
use App\Models\SalesDetail;
use App\Models\StockIn;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();

        $lowStockItems = Inventory::whereColumn('current_stock', '<', 'minimum_stock')->count();

        $totalSalesThisMonth = SalesTransaction::whereMonth('sales_date', now()->month)
            ->whereYear('sales_date', now()->year)
            ->where('status', '!=', 'cancelled')
            ->sum('total_amount');

        $weeklyTransactions = SalesTransaction::whereBetween('sales_date', [
            now()->startOfWeek(), now()->endOfWeek()
        ])->count();

        // Recent activity: last 5 sales
        $recentSales = SalesTransaction::with(['customer', 'details.product'])
            ->latest('sales_date')
            ->take(5)
            ->get();

        // Low stock alerts
        $lowStockAlerts = Inventory::with('product')
            ->whereColumn('current_stock', '<', 'minimum_stock')
            ->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'lowStockItems',
            'totalSalesThisMonth',
            'weeklyTransactions',
            'recentSales',
            'lowStockAlerts'
        ));
    }
}