@extends('layouts.app')
@section('title', 'Payments')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">Payments</h1>
            <p class="page-subtitle">Review and update payment records that were initially captured during sales transactions.</p>
        </div>
    </div>

    <div class="card table-wrap">
        <table class="data-table">
            <thead><tr><th>Payment ID</th><th>Sale</th><th>Customer</th><th>Date</th><th>Method</th><th>Amount</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td class="font-semibold text-slate-800">PAY-{{ str_pad($payment->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td><a href="{{ route('sales.show', $payment->salesTransaction) }}" class="font-semibold text-primary hover:underline">{{ $payment->salesTransaction->sale_code }}</a></td>
                        <td>{{ $payment->salesTransaction->customer->full_name }}</td>
                        <td>{{ optional($payment->payment_date)->format('Y-m-d') }}</td>
                        <td>{{ $payment->paymentMethod?->payment_method_name ?: 'N/A' }}</td>
                        <td class="font-semibold text-emerald-600">&#8369;{{ number_format($payment->amount_paid, 2) }}</td>
                        <td>
                            @if($payment->status === 'paid') <span class="badge-green">Paid</span>
                            @else <span class="badge-yellow">Partial</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('payments.show', $payment) }}" class="btn-secondary !min-h-0 !px-4 !py-2">View</a>
                                <a href="{{ route('payments.edit', $payment) }}" class="btn-secondary !min-h-0 !px-4 !py-2">Edit</a>
                                <form method="POST" action="{{ route('payments.destroy', $payment) }}" onsubmit="return confirm('Archive this payment?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-2 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100"><i class="fa-solid fa-box-archive"></i><span>Archive</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="py-16 text-center text-slate-400">No payments found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
