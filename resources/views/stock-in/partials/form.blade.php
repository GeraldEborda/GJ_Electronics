@php
    $initialItems = old('products', $stockIn?->details->map(fn($detail) => [
        'product_id' => $detail->product_id,
        'quantity' => $detail->quantity_received,
        'cost_per_unit' => $detail->cost_per_unit,
        'minimum_stock' => $detail->product->inventory?->minimum_stock ?? 0,
        'condition' => $detail->condition_status,
    ])->values()->all() ?? [[
        'product_id' => '',
        'quantity' => 1,
        'cost_per_unit' => 0,
        'minimum_stock' => 0,
        'condition' => 'good',
    ]]);
    $supplierOptions = $suppliers->map(fn($supplier) => [
        'id' => $supplier->id,
        'label' => $supplier->supplier_name,
    ])->values();
    $selectedSupplierId = old('supplier_id', $stockIn?->supplier_id);
    $selectedSupplierLabel = $suppliers->firstWhere('id', $selectedSupplierId)?->supplier_name ?? '';
@endphp

<div class="mx-auto max-w-7xl">
    <div class="mb-8">
        <h1 class="page-title">{{ $title }}</h1>
        <p class="page-subtitle">{{ $subtitle }}</p>
    </div>

    @if($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
            <ul class="space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $action }}" x-data='stockInForm(@json($initialItems))'>
        @csrf
        @if($method !== 'POST')
            @method($method)
        @endif

        <div class="card mb-6 p-8">
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                <div>
                    <label class="form-label">Supplier <span class="text-red-500">*</span></label>
                    <input type="hidden" name="supplier_id" id="stockin_supplier_id" value="{{ $selectedSupplierId }}">
                    <input
                        type="text"
                        id="stockin_supplier_lookup"
                        list="stockin_supplier_lookup_list"
                        value="{{ $selectedSupplierLabel }}"
                        class="form-input"
                        placeholder="Search supplier name"
                        autocomplete="off"
                    >
                    <datalist id="stockin_supplier_lookup_list">
                        @foreach($supplierOptions as $option)
                            <option value="{{ $option['label'] }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div>
                    <label class="form-label">Delivery Receipt No.</label>
                    <input type="text" name="delivery_receipt_no" value="{{ old('delivery_receipt_no', $stockIn?->delivery_receipt_no) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Date Received <span class="text-red-500">*</span></label>
                    <input type="date" name="date_received" value="{{ old('date_received', optional($stockIn?->date_received)->format('Y-m-d') ?? date('Y-m-d')) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Received By (Employee) <span class="text-red-500">*</span></label>
                    <select name="employee_id" class="form-input" required>
                        <option value="">Select employee</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('employee_id', $stockIn?->employee_id) == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-5">
                <label class="form-label">Remarks / Notes</label>
                <textarea name="remarks" rows="3" class="form-input">{{ old('remarks', $stockIn?->remarks) }}</textarea>
            </div>
        </div>

        <div class="card mb-6 p-8">
            <div class="mb-5 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Product Items</h2>
                    <p class="mt-1 text-sm text-slate-500">Use this module to record the actual quantity received for each product and set its stock threshold.</p>
                </div>
                <button type="button" @click="addItem()" class="btn-secondary"><i class="fa-solid fa-plus"></i><span>Add Another Item</span></button>
            </div>
            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="soft-panel p-4">
                        <div class="grid grid-cols-1 gap-4 xl:grid-cols-12 xl:items-end">
                            <div class="xl:col-span-3">
                                <label x-show="index === 0" class="form-label">Product</label>
                                <select :name="`products[${index}][product_id]`" x-model="item.product_id" @change="setDefaults(index)" class="form-input" required>
                                    <option value="">Select product</option>
                                    @foreach($products as $product)
                                        <option
                                            value="{{ $product->id }}"
                                            data-price="{{ $product->unit_price }}"
                                            data-min-stock="{{ $product->inventory?->minimum_stock ?? 0 }}"
                                        >
                                            {{ $product->product_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="xl:col-span-2">
                                <label x-show="index === 0" class="form-label">Quantity</label>
                                <input type="number" :name="`products[${index}][quantity]`" x-model="item.quantity" @input="calcTotal(index)" class="form-input" min="1" required>
                            </div>
                            <div class="xl:col-span-2">
                                <label x-show="index === 0" class="form-label">Unit Cost</label>
                                <input type="number" :name="`products[${index}][cost_per_unit]`" x-model="item.cost_per_unit" @input="calcTotal(index)" class="form-input" min="0" step="0.01" required>
                            </div>
                                <div class="xl:col-span-2">
                                    <label x-show="index === 0" class="form-label">Minimum Stock</label>
                                    <input type="number" :name="`products[${index}][minimum_stock]`" x-model="item.minimum_stock" class="form-input" min="0" required>
                                </div>
                                <div class="xl:col-span-2">
                                    <label x-show="index === 0" class="form-label">Condition</label>
                                    <select :name="`products[${index}][condition]`" x-model="item.condition" class="form-input" required>
                                        <option value="good">Good</option>
                                    <option value="damaged">Damaged</option>
                                    <option value="defective">Defective</option>
                                    <option value="expired">Expired</option>
                                    <option value="returned">Returned</option>
                                </select>
                            </div>
                                <div class="xl:col-span-1">
                                    <label x-show="index === 0" class="form-label">Total</label>
                                    <input type="text" :value="formatPeso(item.total)" class="form-input bg-slate-100" readonly>
                                </div>
                            <div class="xl:col-span-12 flex justify-end">
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="inline-flex items-center gap-2 rounded-2xl bg-red-50 px-4 py-2 text-sm font-semibold text-red-500 transition hover:bg-red-100"><i class="fa-solid fa-trash"></i><span>Remove</span></button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="mt-6 flex justify-end border-t border-slate-100 pt-6">
                <div class="text-right">
                    <div class="text-sm font-medium text-slate-500">Grand Total</div>
                    <div class="text-3xl font-bold text-slate-900" x-text="formatPeso(grandTotal)"></div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('stock-in.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i><span>{{ $submitLabel }}</span></button>
        </div>
    </form>
</div>

@push('scripts')
<script>
setupSearchCombobox({
    inputId: 'stockin_supplier_lookup',
    hiddenId: 'stockin_supplier_id',
    options: @json($supplierOptions),
    requiredMessage: 'Please select a valid supplier from the list.'
});

function stockInForm(initialItems) {
    const normalized = (initialItems || []).map(item => ({
        product_id: item.product_id ?? '',
        quantity: Number(item.quantity ?? 1),
        cost_per_unit: Number(item.cost_per_unit ?? item.cost ?? 0),
        minimum_stock: Number(item.minimum_stock ?? 0),
        condition: item.condition ?? 'good',
        total: Number(item.total ?? ((item.quantity ?? 0) * (item.cost_per_unit ?? item.cost ?? 0))),
    }));

    return {
        items: normalized.length ? normalized : [{ product_id: '', quantity: 1, cost_per_unit: 0, minimum_stock: 0, condition: 'good', total: 0 }],
        get grandTotal() {
            return this.items.reduce((sum, item) => sum + (parseFloat(item.total) || 0), 0);
        },
        addItem() {
            this.items.push({ product_id: '', quantity: 1, cost_per_unit: 0, minimum_stock: 0, condition: 'good', total: 0 });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        calcTotal(index) {
            const item = this.items[index];
            item.total = (parseFloat(item.quantity) || 0) * (parseFloat(item.cost_per_unit) || 0);
        },
        setDefaults(index) {
            const select = document.querySelector(`[name="products[${index}][product_id]"]`);
            const option = select?.options[select.selectedIndex];

            if (!option) {
                return;
            }

            this.items[index].cost_per_unit = parseFloat(option.dataset.price) || 0;
            this.items[index].minimum_stock = parseInt(option.dataset.minStock || 0, 10);
            this.calcTotal(index);
        },
        formatPeso(value) {
            return 'PHP ' + parseFloat(value || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        }
    }
}
</script>
@endpush
