@extends('layouts.app')
@section('title', 'Edit Product')

@section('content')
    <div class="mx-auto max-w-4xl">
        @php
            $supplierOptions = $suppliers->map(fn($supplier) => [
                'id' => $supplier->id,
                'label' => $supplier->supplier_name,
            ])->values();
            $selectedSupplierId = old('supplier_id', $product->supplier_id);
            $selectedSupplierLabel = $suppliers->firstWhere('id', $selectedSupplierId)?->supplier_name ?? '';
        @endphp

        <div class="mb-8">
            <h1 class="text-5xl font-black text-slate-900">Edit Product</h1>
            <p class="mt-2 text-xl text-slate-500">Update the product details used across inventory, stock in, and sales.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card p-8">
            <form method="POST" action="{{ route('products.update', $product) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label class="form-label">Product Name</label>
                    <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}" class="form-input" required>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-input" required>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Model</label>
                        <input type="text" name="model_number" value="{{ old('model_number', $product->model_number) }}" class="form-input">
                    </div>
                </div>

                <div>
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-input">{{ old('description', $product->description) }}</textarea>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="form-label">Supplier</label>
                        <input type="hidden" name="supplier_id" id="product_supplier_id" value="{{ $selectedSupplierId }}">
                        <input
                            type="text"
                            id="product_supplier_lookup"
                            list="product_supplier_lookup_list"
                            value="{{ $selectedSupplierLabel }}"
                            class="form-input"
                            placeholder="Search supplier name"
                            autocomplete="off"
                        >
                        <datalist id="product_supplier_lookup_list">
                            @foreach($supplierOptions as $option)
                                <option value="{{ $option['label'] }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="form-label">Unit Price (&#8369;)</label>
                        <input type="number" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" class="form-input" min="0" step="0.01" required>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="form-label">Minimum Stock Level</label>
                        <input type="number" name="minimum_stock" value="{{ old('minimum_stock', $product->inventory?->minimum_stock ?? 0) }}" class="form-input" min="0" required>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('products.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
setupSearchCombobox({
    inputId: 'product_supplier_lookup',
    hiddenId: 'product_supplier_id',
    options: @json($supplierOptions),
    requiredMessage: 'Please select a valid supplier from the list.'
});
</script>
@endpush
