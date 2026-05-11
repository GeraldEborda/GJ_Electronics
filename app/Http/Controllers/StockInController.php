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
use Illuminate\Validation\Rule;

class StockInController extends Controller
{
    public function index()
    {
        $stockIns = StockIn::active()
            ->with(['supplier', 'employee', 'details.product'])
            ->orderByDesc('date_received')
            ->orderByDesc('id')
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
                'delivery_receipt_no' => null,
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $stockIn->update([
                'delivery_receipt_no' => StockIn::makeDeliveryReceiptNo($stockIn->id, $validated['date_received']),
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
            $oldGoodQuantities = $this->goodQuantitiesByProduct($stockIn->details);

            $stockIn->details()->delete();

            $stockIn->update([
                'supplier_id' => $validated['supplier_id'],
                'employee_id' => $validated['employee_id'],
                'date_received' => $validated['date_received'],
                'remarks' => $validated['remarks'] ?? null,
            ]);

            $this->syncDetails($stockIn, $validated['products']);
            $this->subtractOldGoodQuantities($oldGoodQuantities);
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
            'nextDeliveryReceiptNo' => StockIn::makeDeliveryReceiptNo((int) StockIn::max('id') + 1),
        ];
    }

    protected function validateStockIn(Request $request): array
    {
        return $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'date_received' => 'required|date',
            'employee_id' => 'required|exists:employees,id',
            'remarks' => 'nullable|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => [
                'required',
                Rule::exists('products', 'id')->where(fn ($query) => $query
                    ->where('supplier_id', $request->input('supplier_id'))
                    ->where('is_archived', false)
                ),
            ],
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

    protected function goodQuantitiesByProduct($details)
    {
        return $details
            ->where('condition_status', 'good')
            ->groupBy('product_id')
            ->map(fn ($items) => $items->sum('quantity_received'));
    }

    protected function subtractOldGoodQuantities($oldGoodQuantities): void
    {
        foreach ($oldGoodQuantities as $productId => $quantity) {
            $inventory = Inventory::where('product_id', $productId)->first();

            if (! $inventory) {
                continue;
            }

            $inventory->current_stock = max(0, $inventory->current_stock - $quantity);
            $inventory->save();
        }
    }
}
