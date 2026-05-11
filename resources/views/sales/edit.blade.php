@extends('layouts.app')
@section('title', 'Edit Sale')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-8">
            <h1 class="page-title">Edit Sales Transaction</h1>
            <p class="page-subtitle">Update customer, item, and payment information for this sale.</p>
        </div>

        @php
            $initialProducts = old('products', $sale->details->map(fn($detail) => [
                'product_id' => $detail->product_id,
                'quantity' => $detail->quantity,
                'unit_price' => $detail->unit_price,
                'subtotal' => $detail->subtotal,
            ])->values()->all());
            $customerOptions = $customers->map(fn($customer) => [
                'id' => $customer->id,
                'label' => $customer->full_name,
            ])->values();
            $selectedCustomerId = old('customer_id', $sale->customer_id);
            $selectedCustomerLabel = $customers->firstWhere('id', $selectedCustomerId)?->full_name ?? '';
        @endphp

        @if($errors->any())
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('sales.update', $sale) }}" x-data='salesForm(@json($initialProducts), @json((float) old('amount_paid', $sale?->payment?->amount_paid ?? 0)))'>
            @csrf
            @method('PUT')

            <div class="card mb-6 p-8">
                <h2 class="mb-5 text-xl font-bold text-slate-900">Customer Information</h2>
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="form-label">Customer <span class="text-red-500">*</span></label>
                        <input type="hidden" name="customer_id" id="sales_customer_id" value="{{ $selectedCustomerId }}">
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
                        <input type="date" name="sales_date" value="{{ old('sales_date', $sale->sales_date->format('Y-m-d')) }}" class="form-input" required>
                    </div>
                </div>
            </div>

            @include('sales.partials.items-and-payment', ['paymentMethods' => $paymentMethods, 'sale' => $sale])

            <div class="flex justify-end gap-3">
                <a href="{{ route('sales.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-success"><i class="fa-solid fa-floppy-disk"></i><span>Save Changes</span></button>
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

function salesForm(initialItems, initialAmountPaid = 0) {
    const normalized = (initialItems || []).map(item => ({
        product_id: item.product_id ?? '',
        quantity: Number(item.quantity ?? 0),
        unit_price: Number(item.unit_price ?? 0),
        subtotal: Number(item.subtotal ?? ((item.quantity ?? 0) * (item.unit_price ?? 0))),
    }));
    return {
        items: normalized.length ? normalized : [{ product_id: '', quantity: 0, unit_price: 0, subtotal: 0 }],
        amountPaid: Number(initialAmountPaid || 0),
        get grandTotal() { return this.items.reduce((sum, i) => sum + (parseFloat(i.subtotal) || 0), 0); },
        get paymentStatusLabel() { return this.grandTotal > 0 && Number(this.amountPaid || 0) >= this.grandTotal ? 'Paid' : 'Partial'; },
        addItem() { this.items.push({ product_id: '', quantity: 0, unit_price: 0, subtotal: 0 }); },
        removeItem(index) { this.items.splice(index, 1); },
        calcSubtotal(index) { const item = this.items[index]; item.subtotal = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0); },
        setPrice(index) { const select = document.querySelectorAll(`[name="products[${index}][product_id]"]`)[0]; const option = select?.options[select?.selectedIndex]; if (option) { this.items[index].unit_price = parseFloat(option.dataset.price) || 0; this.calcSubtotal(index); } },
        formatPeso(val) { return 'PHP ' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 }); }
    }
}
</script>
@endpush
