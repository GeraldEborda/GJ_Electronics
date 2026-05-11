@extends('layouts.app')
@section('title', 'Sale Details')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">{{ $sale->sale_code }}</h1>
            <p class="page-subtitle">Detailed view of this sales transaction.</p>
        </div>
        <a href="{{ route('sales.index') }}" class="btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Back</span>
        </a>
    </div>

    <div class="mb-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="card p-7 xl:col-span-2">
            <h2 class="mb-4 text-xl font-bold text-slate-900">Transaction Info</h2>
            <dl class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                <div>
                    <dt class="mb-1 text-slate-500">Customer</dt>
                    <dd class="font-semibold text-slate-800">{{ $sale->customer->full_name }}</dd>
                </div>
                <div>
                    <dt class="mb-1 text-slate-500">Sales Person</dt>
                    <dd class="font-semibold text-slate-800">{{ $sale->employee->full_name }}</dd>
                </div>
                <div>
                    <dt class="mb-1 text-slate-500">Sales Date</dt>
                    <dd class="font-semibold text-slate-800">{{ $sale->sales_date->format('F j, Y g:i A') }}</dd>
                </div>
                <div>
                    <dt class="mb-1 text-slate-500">Status</dt>
                    <dd>
                        @if($sale->status === 'completed')
                            <span class="badge-green">Completed</span>
                        @elseif($sale->status === 'pending')
                            <span class="badge-yellow">Pending</span>
                        @else
                            <span class="badge-red">Cancelled</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        <div class="card p-7">
            <div class="mb-4 text-center">
                <div class="text-sm text-slate-500">Total Amount</div>
                <div class="text-4xl font-bold text-slate-900">&#8369;{{ number_format($sale->total_amount, 2) }}</div>
            </div>

            @if($sale->payment)
                <div class="soft-panel space-y-3 p-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Method</span>
                        <span class="font-semibold text-slate-700">{{ $sale->payment->paymentMethod?->payment_method_name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Amount Paid</span>
                        <span class="font-semibold text-emerald-600">&#8369;{{ number_format($sale->payment->amount_paid, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Balance</span>
                        <span class="font-semibold text-slate-700">&#8369;{{ number_format($sale->total_amount - $sale->payment->amount_paid, 2) }}</span>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    @if($sale->payment->status === 'paid')
                        <span class="badge-green">Paid</span>
                    @else
                        <span class="badge-yellow">Partial Payment</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <div class="card table-wrap">
        <div class="section-head">
            <h2 class="text-xl font-bold text-slate-900">Product Items</h2>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->details as $detail)
                    <tr>
                        <td class="font-semibold text-slate-800">{{ $detail->product->product_name }}</td>
                        <td>{{ $detail->product->category->category_name }}</td>
                        <td class="font-bold">{{ $detail->quantity }}</td>
                        <td>&#8369;{{ number_format($detail->unit_price, 2) }}</td>
                        <td class="font-semibold text-emerald-600">&#8369;{{ number_format($detail->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right font-semibold text-slate-700">Total</td>
                    <td class="font-bold text-slate-900">&#8369;{{ number_format($sale->total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
