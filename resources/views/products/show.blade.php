@extends('layouts.app')
@section('title', 'Product Details')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">{{ $product->product_name }}</h1>
            <p class="page-subtitle">Product profile, stock status, and transaction summary.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('products.edit', $product) }}" class="btn-primary"><i class="fa-solid fa-pen-to-square"></i><span>Edit</span></a>
            <a href="{{ route('products.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i><span>Back</span></a>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="card p-7 xl:col-span-2">
            <h2 class="mb-4 text-xl font-bold text-slate-900">Product Information</h2>
            <dl class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                <div><dt class="mb-1 text-slate-500">Category</dt><dd class="font-semibold text-slate-800">{{ $product->category->category_name }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Supplier</dt><dd class="font-semibold text-slate-800">{{ $product->supplier->supplier_name }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Model Number</dt><dd class="font-semibold text-slate-800">{{ $product->model_number ?: 'N/A' }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Unit Price</dt><dd class="font-semibold text-slate-800">&#8369;{{ number_format($product->unit_price, 2) }}</dd></div>
                <div class="md:col-span-2"><dt class="mb-1 text-slate-500">Description</dt><dd class="text-slate-700">{{ $product->description ?: 'N/A' }}</dd></div>
            </dl>
        </div>

        <div class="card p-7">
            <h2 class="mb-4 text-xl font-bold text-slate-900">Inventory Snapshot</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Current Stock</span><span class="font-semibold text-slate-800">{{ $product->inventory?->current_stock ?? 0 }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Minimum Stock</span><span class="font-semibold text-slate-800">{{ $product->inventory?->minimum_stock ?? 0 }}</span></div>
                <div class="flex justify-between"><span class="text-slate-500">Status</span><span class="font-semibold text-slate-800">{{ $product->inventory?->status_label ?? 'Out of Stock' }}</span></div>
            </div>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-5 xl:grid-cols-2">
        <div class="card table-wrap">
            <div class="section-head">
                <h2 class="text-xl font-bold text-slate-900">Stock In History</h2>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Receipt No.</th>
                        <th>Quantity</th>
                        <th>Condition</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->stockInDetails as $detail)
                        <tr>
                            <td>{{ $detail->stockIn->date_received->format('Y-m-d') }}</td>
                            <td>{{ $detail->stockIn->delivery_receipt_no ?: 'N/A' }}</td>
                            <td class="font-semibold text-slate-800">{{ $detail->quantity_received }}</td>
                            <td>{{ ucfirst($detail->condition_status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-16 text-center text-slate-400">No stock in records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card table-wrap">
            <div class="section-head">
                <h2 class="text-xl font-bold text-slate-900">Sales History</h2>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sale ID</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($product->salesDetails as $detail)
                        <tr>
                            <td>{{ $detail->salesTransaction->sales_date->format('Y-m-d') }}</td>
                            <td><a href="{{ route('sales.show', $detail->salesTransaction) }}" class="font-semibold text-slate-800 hover:text-primary">SALE-{{ str_pad($detail->salesTransaction->id, 3, '0', STR_PAD_LEFT) }}</a></td>
                            <td>{{ $detail->quantity }}</td>
                            <td class="font-semibold text-slate-800">&#8369;{{ number_format($detail->subtotal, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-16 text-center text-slate-400">No sales records yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
