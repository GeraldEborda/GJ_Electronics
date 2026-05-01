@extends('layouts.app')
@section('title', 'Products')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">Product Management</h1>
            <p class="page-subtitle">Browse, edit, and maintain your active product catalog.</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            <span>Add Product</span>
        </a>
    </div>

    <div class="card mb-6 p-5">
        <form method="GET" action="{{ route('products.index') }}">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products by name or model..." class="form-input pl-11">
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($products as $product)
            @php
                $inv = $product->inventory;
                $status = $inv ? $inv->status : 'out_of_stock';
            @endphp
            <div class="card overflow-hidden">
                <div class="relative flex h-48 items-center justify-center
                    @if($product->category->category_name === 'Fire Safety') bg-gradient-to-br from-orange-50 to-red-100
                    @elseif($product->category->category_name === 'CCTV') bg-gradient-to-br from-slate-100 to-slate-200
                    @else bg-gradient-to-br from-blue-50 to-indigo-100
                    @endif">
                    <i class="text-7xl opacity-30
                        @if($product->category->category_name === 'Fire Safety') fa-solid fa-fire-extinguisher text-orange-600
                        @elseif($product->category->category_name === 'CCTV') fa-solid fa-video text-slate-600
                        @else fa-solid fa-network-wired text-blue-600
                        @endif"></i>
                    <div class="absolute right-4 top-4">
                        @if($status === 'in_stock')
                            <span class="badge-green">In Stock</span>
                        @elseif($status === 'low_stock')
                            <span class="badge-yellow">Low Stock</span>
                        @elseif($status === 'critical')
                            <span class="badge-red">Critical</span>
                        @else
                            <span class="badge-red">Out of Stock</span>
                        @endif
                    </div>
                </div>

                <div class="flex h-[calc(100%-12rem)] flex-col p-6">
                    <div class="text-2xl font-bold text-slate-900">{{ $product->product_name }}</div>
                    <div class="mt-1 font-mono text-sm text-slate-400">{{ $product->model_number }}</div>
                    <p class="mt-4 text-sm leading-6 text-slate-500">{{ $product->description }}</p>

                    <div class="mt-6 space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Category:</span>
                            <span class="font-semibold text-slate-700">{{ $product->category->category_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Stock:</span>
                            <span class="font-semibold text-slate-700">{{ $inv?->current_stock ?? 0 }} units</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Price:</span>
                            <span class="font-bold text-primary">&#8369;{{ number_format($product->unit_price, 0) }}</span>
                        </div>
                    </div>

                    <div class="mt-auto flex items-center gap-2 border-t border-slate-100 pt-5">
                        <a href="{{ route('products.edit', $product) }}" class="btn-secondary flex-1">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <span>Edit</span>
                        </a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Archive this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex h-12 items-center justify-center gap-2 rounded-2xl border border-amber-100 bg-amber-50 px-4 text-amber-700 transition hover:bg-amber-100">
                                <i class="fa-solid fa-box-archive"></i>
                                <span class="text-sm font-semibold">Archive</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-3 py-20 text-center text-slate-400">No products found.</div>
        @endforelse
    </div>
@endsection
