<?php

namespace App\Http\Controllers;

use App\Models\StockIn;
use App\Models\StockInDetail;
use App\Models\Supplier;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockInController extends Controller
{
    public function index()
    {
        $stockIns = StockIn::with(['supplier', 'employee', 'details'])
            ->latest('date_received')
            ->get();

        return view('stock-in.index', compact('stockIns'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('supplier_name')->get();
        $employees = Employee::orderBy('first_name')->orderBy('last_name')->get();
        $products  = Product::with('inventory')->get();
        return view('stock-in.create', compact('suppliers', 'employees', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name'       => 'required|string',
            'delivery_receipt_no' => 'nullable|string',
            'date_received'       => 'required|date',
            'employee_id'         => 'required|exists:employees,id',
            'remarks'             => 'nullable|string',
            'products'            => 'required|array|min:1',
            'products.*.product_id'   => 'required|exists:products,id',
            'products.*.quantity'     => 'required|integer|min:1',
            'products.*.cost_per_unit'=> 'required|numeric|min:0',
            'products.*.condition'    => 'required|string',
        ]);

        DB::transaction(function () use ($request) {
            // Find or create supplier
            $supplier = \App\Models\Supplier::firstOrCreate(
                ['supplier_name' => $request->supplier_name]
            );

            $stockIn = StockIn::create([
                'supplier_id'         => $supplier->id,
                'employee_id'         => $request->employee_id,
                'date_received'       => $request->date_received,
                'delivery_receipt_no' => $request->delivery_receipt_no,
                'remarks'             => $request->remarks,
            ]);

            foreach ($request->products as $item) {
                $total = $item['quantity'] * $item['cost_per_unit'];

                StockInDetail::create([
                    'stock_in_id'      => $stockIn->id,
                    'product_id'       => $item['product_id'],
                    'quantity_received'=> $item['quantity'],
                    'cost_per_unit'    => $item['cost_per_unit'],
                    'total_amount'     => $total,
                    'condition_status' => $item['condition'],
                ]);

                // Update inventory
                $inventory = Inventory::firstOrCreate(
                    ['product_id' => $item['product_id']],
                    ['current_stock' => 0, 'minimum_stock' => 0]
                );

                if ($item['condition'] === 'good') {
                    $inventory->increment('current_stock', $item['quantity']);
                }
            }
        });

        return redirect()->route('stock-in.index')->with('success', 'Stock In recorded successfully.');
    }

    public function show(StockIn $stockIn)
    {
        $stockIn->load(['supplier', 'employee', 'details.product.category']);
        return view('stock-in.show', compact('stockIn'));
    }
}
