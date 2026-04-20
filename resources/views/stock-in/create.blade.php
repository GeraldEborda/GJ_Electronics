@extends('layouts.app')
@section('title', 'New Stock In')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-8">
            <h1 class="page-title">Record Stock In Transaction</h1>
            <p class="page-subtitle">Capture supplier deliveries and keep incoming inventory structured.</p>
        </div>

        @if($errors->any())
            <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-600">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('stock-in.store') }}" x-data="stockInForm()">
            @csrf

            <div class="card mb-6 p-8">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <label class="form-label">Supplier Name <span class="text-red-500">*</span></label>
                        <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" class="form-input" placeholder="Enter supplier name" required>
                    </div>
                    <div>
                        <label class="form-label">Delivery Receipt No.</label>
                        <input type="text" name="delivery_receipt_no" value="{{ old('delivery_receipt_no') }}" class="form-input" placeholder="Enter delivery receipt number">
                    </div>
                    <div>
                        <label class="form-label">Date Received <span class="text-red-500">*</span></label>
                        <input type="date" name="date_received" value="{{ old('date_received', date('Y-m-d')) }}" class="form-input" required>
                    </div>
                    <div>
                        <label class="form-label">Received By (Employee) <span class="text-red-500">*</span></label>
                        <select name="employee_id" class="form-input" required>
                            <option value="">Select employee</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-5">
                    <label class="form-label">Remarks / Notes</label>
                    <textarea name="remarks" rows="3" class="form-input" placeholder="Additional notes or observations">{{ old('remarks') }}</textarea>
                </div>
            </div>

            <div class="card mb-6 p-8">
                <div class="mb-5 flex items-center justify-between">
                    <h2 class="text-xl font-bold text-slate-900">Product Items</h2>
                    <button type="button" @click="addItem()" class="btn-secondary">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Another Item</span>
                    </button>
                </div>

                <div class="space-y-4">
                    <template x-for="(item, index) in items" :key="index">
                        <div class="soft-panel p-4">
                            <div class="grid grid-cols-1 gap-4 xl:grid-cols-12 xl:items-end">
                                <div class="xl:col-span-4">
                                    <label x-show="index === 0" class="form-label">Product</label>
                                    <select :name="`products[${index}][product_id]`" x-model="item.product_id" @change="setPrice(index)" class="form-input" required>
                                        <option value="">Select product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}">{{ $product->product_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="xl:col-span-2">
                                    <label x-show="index === 0" class="form-label">Quantity</label>
                                    <input type="number" :name="`products[${index}][quantity]`" x-model="item.quantity" @input="calcTotal(index)" class="form-input" min="1" required>
                                </div>
                                <div class="xl:col-span-2">
                                    <label x-show="index === 0" class="form-label">Unit Cost</label>
                                    <input type="number" :name="`products[${index}][cost_per_unit]`" x-model="item.cost" @input="calcTotal(index)" class="form-input" min="0" step="0.01" required>
                                </div>
                                <div class="xl:col-span-2">
                                    <label x-show="index === 0" class="form-label">Total</label>
                                    <input type="text" :value="formatPeso(item.total)" class="form-input bg-slate-100" readonly>
                                </div>
                                <div class="xl:col-span-1">
                                    <label x-show="index === 0" class="form-label">Condition</label>
                                    <select :name="`products[${index}][condition]`" x-model="item.condition" class="form-input" required>
                                        <option value="good">Good</option>
                                        <option value="damaged">Damaged</option>
                                        <option value="defective">Defective</option>
                                        <option value="expired">Expired</option>
                                        <option value="returned">Returned</option>
                                    </select>
                                </div>
                                <div class="xl:col-span-1 flex items-end">
                                    <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50 text-red-500 transition hover:bg-red-100">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
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
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Record Stock In</span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
function stockInForm() {
    return {
        items: [{ product_id: '', quantity: 0, cost: 0, total: 0, condition: 'good' }],
        get grandTotal() {
            return this.items.reduce((sum, i) => sum + (parseFloat(i.total) || 0), 0);
        },
        addItem() {
            this.items.push({ product_id: '', quantity: 0, cost: 0, total: 0, condition: 'good' });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        calcTotal(index) {
            const item = this.items[index];
            item.total = (parseFloat(item.quantity) || 0) * (parseFloat(item.cost) || 0);
        },
        setPrice(index) {
            const select = document.querySelectorAll(`[name="products[${index}][product_id]"]`)[0];
            const option = select?.options[select?.selectedIndex];
            if (option) {
                this.items[index].cost = parseFloat(option.dataset.price) || 0;
                this.calcTotal(index);
            }
        },
        formatPeso(val) {
            return 'PHP ' + parseFloat(val || 0).toLocaleString('en-PH', { minimumFractionDigits: 2 });
        }
    }
}
</script>
@endpush
