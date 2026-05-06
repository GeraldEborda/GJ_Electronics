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
        $stockIns = StockIn::active()
            ->with(['supplier', 'employee', 'details.product'])
            ->latest('date_received')
            ->get();

        return view('stock-in.index', compact('stockIns'));
    }

    public function create()
    {
        return view('stock-in.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validateStockIn($request);

        DB::transaction(function () use ($validated) {
            $stockIn = StockIn::create([
                'supplier_id' => $validated['supplier_id'],
                'employee_id' => $validated['employee_id'],
                'date_received' => $validated['date_received'],
                'delivery_receipt_no' => $validated['delivery_receipt_no'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $this->syncDetails($stockIn, $validated['products']);
        });

        return redirect()->route('stock-in.index')->with('success', 'Stock In recorded successfully.');
    }

    public function show(StockIn $stockIn)
    {
        $stockIn->load(['supplier', 'employee', 'details.product.category']);
        return view('stock-in.show', compact('stockIn'));
    }

    public function edit(StockIn $stockIn)
    {
        $stockIn->load(['details.product.inventory']);

        return view('stock-in.edit', array_merge(
            $this->formData(),
            compact('stockIn')
        ));
    }

    public function update(Request $request, StockIn $stockIn)
    {
        $validated = $this->validateStockIn($request);
        $stockIn->load('details');

        DB::transaction(function () use ($stockIn, $validated) {
            $this->rollbackInventory($stockIn->details);
            $stockIn->details()->delete();

            $stockIn->update([
                'supplier_id' => $validated['supplier_id'],
                'employee_id' => $validated['employee_id'],
                'date_received' => $validated['date_received'],
                'delivery_receipt_no' => $validated['delivery_receipt_no'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $this->syncDetails($stockIn, $validated['products']);
        });

        return redirect()->route('stock-in.show', $stockIn)->with('success', 'Stock In transaction updated successfully.');
    }

    public function destroy(StockIn $stockIn)
    {
        $stockIn->update(['is_archived' => true]);

        return redirect()->route('stock-in.index')->with('success', 'Stock In transaction archived successfully.');
    }

    protected function formData(): array
    {
        return [
            'suppliers' => Supplier::active()->orderBy('supplier_name')->get(),
            'employees' => Employee::orderBy('first_name')->orderBy('last_name')->get(),
            'products' => Product::active()->with('inventory')->orderBy('product_name')->get(),
        ];
    }

    protected function validateStockIn(Request $request): array
    {
        return $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'delivery_receipt_no' => 'nullable|string|max:255',
            'date_received' => 'required|date',
            'employee_id' => 'required|exists:employees,id',
            'remarks' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.cost_per_unit' => 'required|numeric|min:0',
            'products.*.minimum_stock' => 'required|integer|min:0',
            'products.*.condition' => 'required|string|in:good,damaged,defective,expired,returned',
        ]);
    }

    protected function syncDetails(StockIn $stockIn, array $items): void
    {
        foreach ($items as $item) {
            $quantity = (int) $item['quantity'];
            $costPerUnit = (float) $item['cost_per_unit'];
            $minimumStock = (int) $item['minimum_stock'];

            $inventory = Inventory::firstOrCreate(
                ['product_id' => $item['product_id']],
                ['current_stock' => 0, 'minimum_stock' => 0]
            );

            $inventory->minimum_stock = $minimumStock;
            $inventory->save();

            StockInDetail::create([
                'stock_in_id' => $stockIn->id,
                'product_id' => $item['product_id'],
                'quantity_received' => $quantity,
                'cost_per_unit' => $costPerUnit,
                'total_amount' => $quantity * $costPerUnit,
                'condition_status' => $item['condition'],
            ]);
        }
    }

    protected function rollbackInventory($details): void
    {
        foreach ($details as $detail) {
            if ($detail->condition_status !== 'good') {
                continue;
            }

            $inventory = Inventory::where('product_id', $detail->product_id)->first();

            if (! $inventory) {
                continue;
            }

            $inventory->current_stock = max(0, $inventory->current_stock - $detail->quantity_received);
            $inventory->save();
        }
    }
}
