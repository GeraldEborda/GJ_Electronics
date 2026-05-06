@extends('layouts.app')
@section('title', 'Supplier Details')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">{{ $supplier->supplier_name }}</h1>
            <p class="page-subtitle">Supplier profile plus the products and stock in records connected to it.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn-primary"><i class="fa-solid fa-pen-to-square"></i><span>Edit</span></a>
            <a href="{{ route('suppliers.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i><span>Back</span></a>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="card p-7 lg:col-span-2">
            <h2 class="mb-4 text-xl font-bold text-slate-900">Supplier Information</h2>
            <dl class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                <div><dt class="mb-1 text-slate-500">Supplier Name</dt><dd class="font-semibold text-slate-800">{{ $supplier->supplier_name }}</dd></div>
                <div><dt class="mb-1 text-slate-500">First Name</dt><dd class="font-semibold text-slate-800">{{ $supplier->first_name ?: 'N/A' }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Last Name</dt><dd class="font-semibold text-slate-800">{{ $supplier->last_name ?: 'N/A' }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Contact Info</dt><dd class="font-semibold text-slate-800">{{ $supplier->contact_info ?: 'N/A' }}</dd></div>
                <div class="md:col-span-2"><dt class="mb-1 text-slate-500">Address</dt><dd class="text-slate-700">{{ $supplier->address ?: 'N/A' }}</dd></div>
            </dl>
        </div>

        <div class="grid gap-5">
            <div class="card flex flex-col items-center justify-center p-7 text-center">
                <div class="text-sm text-slate-500">Linked Products</div>
                <div class="mt-2 text-4xl font-bold text-slate-900">{{ $supplier->products->count() }}</div>
            </div>
            <div class="card flex flex-col items-center justify-center p-7 text-center">
                <div class="text-sm text-slate-500">Stock In Records</div>
                <div class="mt-2 text-4xl font-bold text-slate-900">{{ $supplier->stockIns->count() }}</div>
            </div>
        </div>
    </div>

    <div class="mb-6 card table-wrap">
        <div class="section-head">
            <h2 class="text-xl font-bold text-slate-900">Products</h2>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Unit Price</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplier->products as $product)
                    <tr>
                        <td class="font-semibold text-slate-800"><a href="{{ route('products.show', $product) }}" class="hover:text-primary">{{ $product->product_name }}</a></td>
                        <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                        <td>{{ $product->inventory?->current_stock ?? 0 }}</td>
                        <td class="font-semibold text-slate-800">&#8369;{{ number_format($product->unit_price, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-16 text-center text-slate-400">No products linked yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card table-wrap">
        <div class="section-head">
            <h2 class="text-xl font-bold text-slate-900">Stock In Transactions</h2>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Receipt No.</th>
                    <th>Date</th>
                    <th>Received By</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supplier->stockIns as $stockIn)
                    <tr>
                        <td class="font-semibold text-slate-800"><a href="{{ route('stock-in.show', $stockIn) }}" class="hover:text-primary">{{ $stockIn->delivery_receipt_no ?: $stockIn->stock_in_code }}</a></td>
                        <td>{{ $stockIn->date_received->format('Y-m-d') }}</td>
                        <td>{{ $stockIn->employee->full_name }}</td>
                        <td>{{ $stockIn->details()->count() }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-16 text-center text-slate-400">No stock in records yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
