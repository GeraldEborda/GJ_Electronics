@extends('layouts.app')
@section('title', 'Edit Product')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Edit Product</h1>
        <p class="text-slate-500 text-sm mt-0.5">Update the product details below.</p>
    </div>

    <div class="card p-7">
        @if($errors->any())
        <div class="mb-5 px-4 py-3 bg-red-50 border border-red-200 text-red-600 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="form-label">Product Name <span class="text-red-500">*</span></label>
                <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}"
                       class="form-input" required>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Category <span class="text-red-500">*</span></label>
                    <select name="category_id" class="form-input" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->category_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Model Number</label>
                    <input type="text" name="model_number" value="{{ old('model_number', $product->model_number) }}"
                           class="form-input">
                </div>
            </div>

            <div>
                <label class="form-label">Description</label>
                <textarea name="description" rows="3" class="form-input resize-none">{{ old('description', $product->description) }}</textarea>
            </div>

            <div>
                <label class="form-label">Supplier <span class="text-red-500">*</span></label>
                <select name="supplier_id" class="form-input" required>
                    @foreach($suppliers as $sup)
                    <option value="{{ $sup->id }}" {{ old('supplier_id', $product->supplier_id) == $sup->id ? 'selected' : '' }}>
                        {{ $sup->supplier_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Unit Price (₱) <span class="text-red-500">*</span></label>
                    <input type="number" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}"
                           class="form-input" min="0" step="0.01" required>
                </div>
                <div>
                    <label class="form-label">Minimum Stock Level <span class="text-red-500">*</span></label>
                    <input type="number" name="minimum_stock"
                           value="{{ old('minimum_stock', $product->inventory?->minimum_stock ?? 0) }}"
                           class="form-input" min="0" required>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('products.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection