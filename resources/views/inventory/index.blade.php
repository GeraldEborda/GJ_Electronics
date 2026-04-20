@extends('layouts.app')
@section('title', 'Inventory')

@section('content')
    <div class="mb-8">
        <h1 class="page-title">Inventory Management</h1>
        <p class="page-subtitle">Monitor stock levels, value, and product health across every category.</p>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card stat-card">
            <div>
                <div class="text-xs font-medium text-slate-500">Total Items</div>
                <div class="text-3xl font-bold text-slate-800">{{ $totalItems }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50">
                <i class="fa-solid fa-cube text-blue-500"></i>
            </div>
        </div>
        <div class="card stat-card">
            <div>
                <div class="text-xs font-medium text-slate-500">Total Value</div>
                <div class="text-3xl font-bold text-slate-800">&#8369;{{ number_format($totalValue, 0) }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50">
                <i class="fa-solid fa-box text-emerald-500"></i>
            </div>
        </div>
        <div class="card stat-card">
            <div>
                <div class="text-xs font-medium text-slate-500">Low Stock</div>
                <div class="text-3xl font-bold text-slate-800">{{ $lowStock }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50">
                <i class="fa-solid fa-circle-exclamation text-amber-500"></i>
            </div>
        </div>
        <div class="card stat-card">
            <div>
                <div class="text-xs font-medium text-slate-500">Out of Stock</div>
                <div class="text-3xl font-bold text-slate-800">{{ $outOfStock }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50">
                <i class="fa-solid fa-circle-exclamation text-red-500"></i>
            </div>
        </div>
    </div>

    <div class="card mb-5 p-5">
        <form method="GET" action="{{ route('inventory.index') }}" class="flex flex-col gap-4 xl:flex-row xl:items-center">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by product name or model..." class="form-input pl-11">
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-semibold text-slate-500">Filter:</span>
                <a href="{{ route('inventory.index') }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ !request('category') ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600' }}">
                    All Categories
                </a>
                @foreach($categories as $cat)
                    <a href="{{ route('inventory.index', ['category' => $cat->category_name]) }}" class="rounded-2xl px-4 py-2 text-sm font-semibold {{ request('category') === $cat->category_name ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600' }}">
                        {{ $cat->category_name }}
                    </a>
                @endforeach
            </div>
        </form>
    </div>

    <div class="card table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Model</th>
                    <th>Current Stock</th>
                    <th>Min Stock</th>
                    <th>Unit Price</th>
                    <th>Supplier</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventories as $inv)
                    @php $status = $inv->status; @endphp
                    <tr>
                        <td class="font-semibold text-slate-800">{{ $inv->product->product_name }}</td>
                        <td class="text-slate-600">{{ $inv->product->category->category_name }}</td>
                        <td class="font-mono text-xs text-slate-500">{{ $inv->product->model_number }}</td>
                        <td class="font-bold text-slate-800">{{ $inv->current_stock }}</td>
                        <td class="text-slate-500">{{ $inv->minimum_stock }}</td>
                        <td class="font-medium text-slate-700">&#8369;{{ number_format($inv->product->unit_price, 0) }}</td>
                        <td class="text-slate-600">{{ $inv->product->supplier->supplier_name }}</td>
                        <td>
                            @if($status === 'in_stock')
                                <span class="badge-green">In Stock</span>
                            @elseif($status === 'low_stock')
                                <span class="badge-yellow">Low Stock</span>
                            @elseif($status === 'critical')
                                <span class="badge-red">Critical</span>
                            @else
                                <span class="badge-red">Out of Stock</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-16 text-center text-slate-400">No inventory records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
