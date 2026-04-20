<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Category;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with(['product.category', 'product.supplier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('model_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category') && $request->category !== 'all') {
            $query->whereHas('product.category', function ($q) use ($request) {
                $q->where('category_name', $request->category);
            });
        }

        $inventories   = $query->get();
        $categories    = Category::all();
        $totalItems    = $inventories->count();
        $totalValue    = $inventories->sum(fn($i) => $i->current_stock * $i->product->unit_price);
        $lowStock      = $inventories->filter(fn($i) => $i->current_stock < $i->minimum_stock && $i->current_stock > 0)->count();
        $outOfStock    = $inventories->filter(fn($i) => $i->current_stock <= 0)->count();

        return view('inventory.index', compact(
            'inventories', 'categories', 'totalItems', 'totalValue', 'lowStock', 'outOfStock'
        ));
    }
}