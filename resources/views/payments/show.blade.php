@extends('layouts.app')
@section('title', 'Payment Details')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">PAY-{{ str_pad($payment->id, 3, '0', STR_PAD_LEFT) }}</h1>
            <p class="page-subtitle">Detailed view of the payment record.</p>
        </div>
        <a href="{{ route('payments.index') }}" class="btn-secondary">Back</a>
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="card p-7 xl:col-span-2">
            <h2 class="mb-4 text-xl font-bold text-slate-900">Payment Information</h2>
            <dl class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                <div><dt class="mb-1 text-slate-500">Sale</dt><dd class="font-semibold text-slate-800">{{ $payment->salesTransaction->sale_code }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Customer</dt><dd class="font-semibold text-slate-800">{{ $payment->salesTransaction->customer->full_name }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Payment Date</dt><dd class="font-semibold text-slate-800">{{ optional($payment->payment_date)->format('F j, Y') }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Method</dt><dd class="font-semibold text-slate-800">{{ $payment->paymentMethod?->payment_method_name }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Status</dt><dd class="font-semibold text-slate-800">{{ ucfirst($payment->status) }}</dd></div>
            </dl>
        </div>
        <div class="card flex flex-col items-center justify-center p-7 text-center">
            <div class="text-sm text-slate-500">Amount Paid</div>
            <div class="mt-2 text-4xl font-bold text-slate-900">&#8369;{{ number_format($payment->amount_paid, 2) }}</div>
        </div>
    </div>
@endsection
