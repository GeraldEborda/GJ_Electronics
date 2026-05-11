<div class="mx-auto max-w-4xl">
    <div class="mb-8">
        <h1 class="page-title">{{ $title }}</h1>
        <p class="page-subtitle">{{ $subtitle }}</p>
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

    @php
        $salesTotals = $sales->mapWithKeys(fn($sale) => [$sale->id => (float) $sale->total_amount]);
        $selectedSaleId = old('sales_transaction_id', $payment?->sales_transaction_id);
        $initialAmountPaid = (float) old('amount_paid', $payment?->amount_paid ?? 0);
    @endphp

    <div class="card p-8">
        <form method="POST" action="{{ $action }}" class="space-y-6" x-data='paymentForm(@json($salesTotals), @json((string) $selectedSaleId), @json($initialAmountPaid))'>
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif

            <div>
                <label class="form-label">Sales Transaction</label>
                <select name="sales_transaction_id" x-model="salesTransactionId" class="form-input" required>
                    <option value="">Select sale</option>
                    @foreach($sales as $sale)
                        <option value="{{ $sale->id }}">{{ $sale->sale_code }} - {{ $sale->customer->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="form-label">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', optional($payment?->payment_date)->format('Y-m-d') ?? date('Y-m-d')) }}" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Amount Paid</label>
                    <input type="number" name="amount_paid" x-model.number="amountPaid" min="0" step="0.01" class="form-input" required>
                </div>
            </div>

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method_id" class="form-input" required>
                        <option value="">Select payment method</option>
                        @foreach($paymentMethods as $paymentMethod)
                            <option value="{{ $paymentMethod->id }}" @selected(old('payment_method_id', $payment?->payment_method_id) == $paymentMethod->id)>{{ $paymentMethod->payment_method_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Status</label>
                    <div class="form-input bg-slate-100 font-semibold" x-text="paymentStatusLabel"></div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('payments.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i><span>{{ $method === 'POST' ? 'Add Payment' : 'Save Changes' }}</span></button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function paymentForm(salesTotals, selectedSaleId = '', initialAmountPaid = 0) {
    return {
        salesTransactionId: selectedSaleId || '',
        amountPaid: Number(initialAmountPaid || 0),
        salesTotals: salesTotals || {},
        get saleTotal() { return Number(this.salesTotals[this.salesTransactionId] || 0); },
        get paymentStatusLabel() { return this.salesTransactionId && Number(this.amountPaid || 0) >= this.saleTotal ? 'Paid' : 'Partial'; },
    };
}
</script>
@endpush
