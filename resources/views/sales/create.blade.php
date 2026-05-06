@extends('layouts.app')
@section('title', 'New Sale')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-8">
            <h1 class="page-title">New Sales Transaction</h1>
            <p class="page-subtitle">Process customer sales with clear order, pricing, and payment details.</p>
        </div>

        @php
            $initialProducts = old('products', [[
                'product_id' => '',
                'quantity' => 0,
                'unit_price' => 0,
            ]]);
            $customerOptions = $customers->map(fn($customer) => [
                'id' => $customer->id,
                'label' => $customer->full_name,
            ])->values();
            $selectedCustomerLabel = $customers->firstWhere('id', old('customer_id'))?->full_name ?? '';
        @endphp

        @if($errors->any())
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('sales.store') }}" x-data='salesForm(@json($initialProducts))'>
            @csrf

            <div class="card mb-6 p-8">
                <h2 class="mb-5 text-xl font-bold text-slate-900">Customer Information</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="form-label">Customer <span class="text-red-500">*</span></label>
                        <input type="hidden" name="customer_id" id="sales_customer_id" value="{{ old('customer_id') }}">
                        <input
                            type="text"
                            id="sales_customer_lookup"
                            list="sales_customer_lookup_list"
                            value="{{ $selectedCustomerLabel }}"
                            class="form-input"
                            placeholder="Search customer name"
                            autocomplete="off"
                        >
                        <datalist id="sales_customer_lookup_list">
                            @foreach($customerOptions as $option)
                                <option value="{{ $option['label'] }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    <div>
                        <label class="form-label">Sales Date <span class="text-red-500">*</span></label>
                        <input type="date" name="sales_date" value="{{ old('sales_date', date('Y-m-d')) }}" class="form-input" required>
                    </div>
                </div>
            </div>

            <div class="card mb-6 p-8">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900">Product Items</h2>
                    <button type="button" @click="addItem()" class="btn-secondary"><i class="fa-solid fa-plus"></i><span>Add Another Item</span></button>
                </div>

                <template x-for="(item, index) in items" :key="index">
                    <div class="soft-panel mb-4 p-4">
                        <div class="grid grid-cols-1 gap-4 xl:grid-cols-12 xl:items-end">
                            <div class="xl:col-span-5">
                                <label x-show="index === 0" class="form-label">Product</label>
                                <select :name="`products[${index}][product_id]`" x-model="item.product_id" @change="setPrice(index)" class="form-input" required>
                                    <option value="">Select product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}" data-stock="{{ $product->inventory?->current_stock ?? 0 }}">
                                            {{ $product->product_name }} (Stock: {{ $product->inventory?->current_stock ?? 0 }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="xl:col-span-2">
                                <label x-show="index === 0" class="form-label">Quantity</label>
                                <input type="number" :name="`products[${index}][quantity]`" x-model="item.quantity" @input="calcSubtotal(index)" class="form-input" min="1" required>
                            </div>
                            <div class="xl:col-span-2">
                                <label x-show="index === 0" class="form-label">Unit Price</label>
                                <input type="number" :name="`products[${index}][unit_price]`" x-model="item.unit_price" @input="calcSubtotal(index)" class="form-input" min="0" step="0.01" required>
                            </div>
                            <div class="xl:col-span-2">
                                <label x-show="index === 0" class="form-label">Subtotal</label>
                                <input type="text" :value="formatPeso(item.subtotal)" class="form-input bg-slate-100" readonly>
                            </div>
                            <div class="xl:col-span-1 flex items-end">
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-500 transition hover:bg-red-100"><i class="fa-solid fa-trash"></i></button>
                            </div>
                        </div>
                    </div>
                </template>

                <div class="mt-6 flex justify-end border-t border-slate-100 pt-6">
                    <div class="text-right">
                        <div class="text-sm font-medium text-slate-500">Grand Total</div>
                        <div class="text-3xl font-bold text-slate-900" x-text="formatPeso(grandTotal)"></div>
                    </div>
                </div>
            </div>

            <div class="card mb-6 p-8">
                <h2 class="mb-5 text-xl font-bold text-slate-900">Payment Details</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-4">
                    <div>
                        <label class="form-label">Payment Date <span class="text-red-500">*</span></label>
                        <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Payment Method <span class="text-red-500">*</span></label>
                        <select name="payment_method_id" class="form-input" required>
                            <option value="">Select method</option>
                            @foreach($paymentMethods as $paymentMethod)
                                <option value="{{ $paymentMethod->id }}" @selected(old('payment_method_id') == $paymentMethod->id)>{{ $paymentMethod->payment_method_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Payment Status <span class="text-red-500">*</span></label>
                        <select name="payment_status" class="form-input" required>
                            <option value="paid" @selected(old('payment_status') === 'paid')>Paid</option>
                            <option value="partial" @selected(old('payment_status') === 'partial')>Partial</option>
                            <option value="unpaid" @selected(old('payment_status') === 'unpaid')>Unpaid</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Amount Paid (PHP) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount_paid" value="{{ old('amount_paid', 0) }}" class="form-input" min="0" step="0.01" required>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('sales.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-success"><i class="fa-solid fa-floppy-disk"></i><span>Record Sale</span></button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
setupSearchCombobox({
    inputId: 'sales_customer_lookup',
    hiddenId: 'sales_customer_id',
    options: @json($customerOptions),
    requiredMessage: 'Please select a valid customer from the list.'
});

function salesForm(initialItems) {
    const normalized = (initialItems || []).map(item => ({
        product_id: item.product_id ?? '',
        quantity: Number(item.quantity ?? 0),
        unit_price: Number(item.unit_price ?? item.price ?? 0),
        subtotal: Number(item.subtotal ?? ((item.quantity ?? 0) * (item.unit_price ?? item.price ?? 0))),
    }));

    return {
        items: normalized.length ? normalized : [{ product_id: '', quantity: 0, unit_price: 0, subtotal: 0 }],
        get grandTotal() { return this.items.reduce((sum, i) => sum + (parseFloat(i.subtotal) || 0), 0); },
        addItem() { this.items.push({ product_id: '', quantity: 0, unit_price: 0, subtotal: 0 }); },
        removeItem(index) { this.items.splice(index, 1); },
        calcSubtotal(index) { const item = this.items[index]; item.subtotal = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0); },
        setPrice(index) { const select = document.querySelectorAll(`[name="products[${index}][product_id]"]`)[0]; const option = select?.options[select?.selectedIndex]; if (option) { this.items[index].unit_price = parseFloat(option.dataset.price) || 0; this.calcSubtotal(index); } },
        formatPeso(val) { return 'PHP ' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 }); }
    }
}
</script>
@endpush
