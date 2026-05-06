<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::active()
            ->with(['category', 'supplier', 'inventory'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('product_name', 'like', "%{$search}%")
                        ->orWhere('model_number', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::orderBy('category_name')->get();
        $suppliers = Supplier::active()->orderBy('supplier_name')->get();

        return view('products.create', compact('categories', 'suppliers'));
    }

    public function show(Product $product)
    {
        $product->load([
            'category',
            'supplier',
            'inventory',
            'stockInDetails.stockIn',
            'salesDetails.salesTransaction',
        ]);

        return view('products.show', compact('product'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProduct($request);

        DB::transaction(function () use ($validated) {
            $product = Product::create([
                'supplier_id' => $validated['supplier_id'],
                'category_id' => $validated['category_id'],
                'model_number' => $validated['model_number'] ?? null,
                'product_name' => $validated['product_name'],
                'description' => $validated['description'] ?? null,
                'unit_price' => $validated['unit_price'],
            ]);

            Inventory::create([
                'product_id' => $product->id,
                'current_stock' => 0,
                'minimum_stock' => $validated['minimum_stock'],
            ]);
        });

        return redirect()->route('products.index')->with('success', 'Product added successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('category_name')->get();
        $suppliers = Supplier::active()->orderBy('supplier_name')->get();

        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $this->validateProduct($request);

        DB::transaction(function () use ($product, $validated) {
            $product->update([
                'supplier_id' => $validated['supplier_id'],
                'category_id' => $validated['category_id'],
                'model_number' => $validated['model_number'] ?? null,
                'product_name' => $validated['product_name'],
                'description' => $validated['description'] ?? null,
                'unit_price' => $validated['unit_price'],
            ]);

            $product->inventory()->updateOrCreate(
                ['product_id' => $product->id],
                ['minimum_stock' => $validated['minimum_stock']]
            );
        });

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_archived' => true]);

        return redirect()->route('products.index')->with('success', 'Product archived successfully.');
    }

    protected function validateProduct(Request $request): array
    {
        return $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'model_number' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ]);
    }
}
