<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\SalesDetail;
use App\Models\SalesTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SalesController extends Controller
{
    public function index()
    {
        $sales = SalesTransaction::with(['customer', 'employee', 'payment.paymentMethod', 'details.product'])
            ->orderByDesc('sales_date')
            ->orderByDesc('id')
            ->get();

        $totalSales = $sales->where('status', '!=', 'cancelled')->sum('total_amount');
        $totalTransactions = $sales->where('status', '!=', 'cancelled')->count();
        $avgTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        $topProducts = SalesDetail::with('product')
            ->whereHas('salesTransaction', fn ($query) => $query->where('status', '!=', 'cancelled'))
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(3)
            ->get();

        return view('sales.index', compact('sales', 'totalSales', 'totalTransactions', 'avgTransaction', 'topProducts'));
    }

    public function create()
    {
        $customers = Customer::active()->orderBy('first_name')->orderBy('last_name')->get();
        $products = Product::active()->with('inventory')->get();
        $paymentMethods = PaymentMethod::orderBy('payment_method_name')->get();

        return view('sales.create', compact('customers', 'products', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateSale($request);
        $requestedQuantities = $this->aggregateRequestedQuantities($validated['products']);

        foreach ($requestedQuantities as $productId => $quantity) {
            $availableStock = Inventory::where('product_id', $productId)->value('current_stock') ?? 0;

            if ($quantity > $availableStock) {
                throw ValidationException::withMessages([
                    'products' => 'Requested quantity exceeds available stock for one or more products.',
                ]);
            }
        }

        DB::transaction(function () use ($validated) {
            $sale = SalesTransaction::create([
                'customer_id' => $validated['customer_id'],
                'employee_id' => $this->resolveSalesEmployee()->id,
                'sales_date' => $validated['sales_date'],
                'total_amount' => $this->calculateTotal($validated['products']),
                'status' => 'pending',
            ]);

            $this->syncSaleDetails($sale, collect(), collect($validated['products']));
            $this->upsertPayment($sale, $validated);
            $sale->refreshStatusFromPayment();
        });

        return redirect()->route('sales.index')->with('success', 'Sale recorded successfully.');
    }

    public function show(SalesTransaction $sale)
    {
        $sale->load(['customer', 'employee', 'payment.paymentMethod', 'details.product.category']);

        return view('sales.show', compact('sale'));
    }

    public function edit(SalesTransaction $sale)
    {
        if ($sale->status === 'cancelled') {
            return redirect()->route('sales.show', $sale)->with('error', 'Cancelled sales cannot be edited.');
        }

        $sale->load(['details', 'payment']);
        $customers = Customer::active()->orderBy('first_name')->orderBy('last_name')->get();
        $products = Product::active()->with('inventory')->get();
        $paymentMethods = PaymentMethod::orderBy('payment_method_name')->get();

        return view('sales.edit', compact('sale', 'customers', 'products', 'paymentMethods'));
    }

    public function update(Request $request, SalesTransaction $sale)
    {
        if ($sale->status === 'cancelled') {
            return redirect()->route('sales.show', $sale)->with('error', 'Cancelled sales cannot be edited.');
        }

        $validated = $this->validateSale($request);
        $oldQuantities = $sale->details->groupBy('product_id')->map(fn ($group) => $group->sum('quantity'));
        $requestedQuantities = $this->aggregateRequestedQuantities($validated['products']);

        foreach ($requestedQuantities as $productId => $quantity) {
            $availableStock = (Inventory::where('product_id', $productId)->value('current_stock') ?? 0) + ($oldQuantities[$productId] ?? 0);

            if ($quantity > $availableStock) {
                throw ValidationException::withMessages([
                    'products' => 'Requested quantity exceeds available stock for one or more products.',
                ]);
            }
        }

        DB::transaction(function () use ($validated, $sale) {
            $oldDetails = $sale->details()->get();

            $sale->update([
                'customer_id' => $validated['customer_id'],
                'sales_date' => $validated['sales_date'],
                'total_amount' => $this->calculateTotal($validated['products']),
            ]);

            $this->syncSaleDetails($sale, $oldDetails, collect($validated['products']));
            $this->upsertPayment($sale, $validated);
            $sale->refreshStatusFromPayment();
        });

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy(SalesTransaction $sale)
    {
        DB::transaction(function () use ($sale) {
            if ($sale->status === 'cancelled') {
                return;
            }

            foreach ($sale->details as $detail) {
                Inventory::where('product_id', $detail->product_id)->increment('current_stock', $detail->quantity);
            }

            $sale->payment()?->update(['is_archived' => true]);
            $sale->update(['status' => 'cancelled']);
        });

        return redirect()->route('sales.index')->with('success', 'Sale cancelled successfully.');
    }

    protected function validateSale(Request $request): array
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'sales_date' => ['required', 'date'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_date' => ['required', 'date'],
            'products' => ['required', 'array', 'min:1'],
            'products.*.product_id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'integer', 'min:1'],
            'products.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $this->validatePaymentAmount(
            (float) $validated['amount_paid'],
            $this->calculateTotal($validated['products'])
        );

        return $validated;
    }

    protected function calculateTotal(array $products): float
    {
        return (float) collect($products)->sum(fn ($product) => $product['quantity'] * $product['unit_price']);
    }

    protected function validatePaymentAmount(float $amountPaid, float $totalAmount): void
    {
        if ($amountPaid > $totalAmount) {
            throw ValidationException::withMessages([
                'amount_paid' => 'Amount paid cannot exceed the sale total.',
            ]);
        }
    }

    protected function aggregateRequestedQuantities(array $products): Collection
    {
        return collect($products)
            ->groupBy('product_id')
            ->map(fn ($group) => $group->sum('quantity'));
    }

    protected function syncSaleDetails(SalesTransaction $sale, Collection $oldDetails, Collection $newDetails): void
    {
        foreach ($oldDetails as $detail) {
            Inventory::where('product_id', $detail->product_id)->increment('current_stock', $detail->quantity);
        }

        $sale->details()->delete();

        foreach ($newDetails as $item) {
            $subtotal = $item['quantity'] * $item['unit_price'];

            SalesDetail::create([
                'sales_transaction_id' => $sale->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $subtotal,
            ]);
        }
    }

    protected function upsertPayment(SalesTransaction $sale, array $validated): void
    {
        $sale->payment()->updateOrCreate(
            ['sales_transaction_id' => $sale->id],
            [
                'payment_date' => $validated['payment_date'],
                'amount_paid' => $validated['amount_paid'],
                'payment_method_id' => $validated['payment_method_id'],
                'status' => Payment::statusForAmount((float) $validated['amount_paid'], (float) $sale->total_amount),
            ]
        );
    }

    protected function resolveSalesEmployee(): Employee
    {
        $currentUserName = trim((string) auth()->user()?->name);

        return Employee::query()
            ->when($currentUserName !== '', function ($query) use ($currentUserName) {
                $query->orderByRaw(
                    "CASE WHEN CONCAT(first_name, ' ', last_name) = ? THEN 0 WHEN first_name = ? THEN 1 ELSE 2 END",
                    [$currentUserName, $currentUserName]
                );
            })
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->firstOrFail();
    }
}
