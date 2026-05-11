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
            <input type="date" name="payment_date" value="{{ old('payment_date', optional($sale?->payment?->payment_date)->format('Y-m-d') ?? date('Y-m-d')) }}" class="form-input" required>
        </div>
        <div>
            <label class="form-label">Payment Method <span class="text-red-500">*</span></label>
            <select name="payment_method_id" class="form-input" required>
                <option value="">Select method</option>
                @foreach($paymentMethods as $paymentMethod)
                    <option value="{{ $paymentMethod->id }}" @selected(old('payment_method_id', $sale?->payment?->payment_method_id) == $paymentMethod->id)>{{ $paymentMethod->payment_method_name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Payment Status</label>
            <div class="form-input bg-slate-100 font-semibold" x-text="paymentStatusLabel"></div>
        </div>
        <div>
            <label class="form-label">Amount Paid (PHP) <span class="text-red-500">*</span></label>
            <input type="number" name="amount_paid" x-model.number="amountPaid" class="form-input" min="0" step="0.01" required>
        </div>
    </div>
</div>
