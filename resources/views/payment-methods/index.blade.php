@extends('layouts.app')
@section('title', 'Payment Methods')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">Payment Methods</h1>
            <p class="page-subtitle">Manage available payment options for sales and payments.</p>
        </div>
        <a href="{{ route('payment-methods.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i><span>Add Method</span></a>
    </div>

    <div class="card mb-6 p-5">
        <form method="GET" action="{{ route('payment-methods.index') }}">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search payment methods..." class="form-input pl-11">
            </div>
        </form>
    </div>

    <div class="card table-wrap">
        <table class="data-table">
            <thead><tr><th>Payment Method</th><th>Used In Payments</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($paymentMethods as $paymentMethod)
                    <tr>
                        <td class="font-semibold text-slate-800">{{ $paymentMethod->payment_method_name }}</td>
                        <td>{{ $paymentMethod->payments_count }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('payment-methods.edit', $paymentMethod) }}" class="btn-secondary !min-h-0 !px-4 !py-2"><i class="fa-solid fa-pen-to-square"></i><span>Edit</span></a>
                                <form method="POST" action="{{ route('payment-methods.destroy', $paymentMethod) }}" onsubmit="return confirm('Delete this payment method?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-2 rounded-2xl border border-red-100 bg-red-50 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-100"><i class="fa-solid fa-trash"></i><span>Delete</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="py-16 text-center text-slate-400">No payment methods found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
