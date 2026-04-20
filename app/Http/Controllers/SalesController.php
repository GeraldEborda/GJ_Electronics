<?php

namespace App\Http\Controllers;

use App\Models\SalesTransaction;
use App\Models\SalesDetail;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesController extends Controller
{
    public function index()
    {
        $sales = SalesTransaction::with(['customer', 'employee', 'payment', 'details.product'])
            ->latest('sales_date')
            ->get();

        $totalSales = $sales->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalTransactions = $sales->count();
        $avgTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Top selling products
        $topProducts = SalesDetail::with('product')
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(3)
            ->get();

        return view('sales.index', compact('sales', 'totalSales', 'totalTransactions', 'avgTransaction', 'topProducts'));
    }

    public function create()
    {
        $customers = Customer::all();
        $employees = Employee::orderBy('first_name')->orderBy('last_name')->get();
        $products  = Product::with('inventory')->get();
        return view('sales.create', compact('customers', 'employees', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string',
            'contact_info'     => 'nullable|string',
            'address'          => 'nullable|string',
            'sales_date'       => 'required|date',
            'employee_id'      => 'required|exists:employees,id',
            'payment_method'   => 'required|string',
            'payment_status'   => 'required|string',
            'amount_paid'      => 'required|numeric|min:0',
            'products'         => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity'   => 'required|integer|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        foreach ($request->products as $index => $item) {
            $inventory = Inventory::where('product_id', $item['product_id'])->first();
            $availableStock = $inventory?->current_stock ?? 0;

            if ($item['quantity'] > $availableStock) {
                return back()
                    ->withErrors([
                        "products.{$index}.quantity" => 'Requested quantity exceeds available stock.',
                    ])
                    ->withInput();
            }
        }

        DB::transaction(function () use ($request) {
            // Find or create customer
            $nameParts = explode(' ', $request->customer_name, 2);
            $customer = Customer::firstOrCreate(
                ['first_name' => $nameParts[0], 'last_name' => $nameParts[1] ?? ''],
                ['contact_info' => $request->contact_info, 'address' => $request->address]
            );

            $total = collect($request->products)->sum(fn($p) => $p['quantity'] * $p['unit_price']);

            $paymentStatus = $request->payment_status;
            $saleStatus = match($paymentStatus) {
                'paid'    => 'completed',
                'partial' => 'pending',
                default   => 'pending',
            };

            $sale = SalesTransaction::create([
                'customer_id'  => $customer->id,
                'employee_id'  => $request->employee_id,
                'sales_date'   => $request->sales_date,
                'total_amount' => $total,
                'status'       => $saleStatus,
            ]);

            foreach ($request->products as $item) {
                $subtotal = $item['quantity'] * $item['unit_price'];

                SalesDetail::create([
                    'sales_transaction_id' => $sale->id,
                    'product_id'           => $item['product_id'],
                    'quantity'             => $item['quantity'],
                    'unit_price'           => $item['unit_price'],
                    'subtotal'             => $subtotal,
                ]);

                // Deduct from inventory
                $inventory = Inventory::where('product_id', $item['product_id'])->first();
                if ($inventory) {
                    $inventory->decrement('current_stock', $item['quantity']);
                }
            }

            Payment::create([
                'sales_transaction_id' => $sale->id,
                'amount_paid'          => $request->amount_paid,
                'payment_method'       => $request->payment_method,
                'status'               => $paymentStatus,
            ]);
        });

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function show(SalesTransaction $sale)
    {
        $sale->load(['customer', 'employee', 'payment', 'details.product.category']);
        return view('sales.show', compact('sale'));
    }
}
