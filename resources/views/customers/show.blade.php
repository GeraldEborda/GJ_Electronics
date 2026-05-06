@extends('layouts.app')
@section('title', 'Customer Details')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">{{ $customer->full_name }}</h1>
            <p class="page-subtitle">Customer profile and related sales transactions.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('customers.edit', $customer) }}" class="btn-primary"><i class="fa-solid fa-pen-to-square"></i><span>Edit</span></a>
            <a href="{{ route('customers.index') }}" class="btn-secondary"><i class="fa-solid fa-arrow-left"></i><span>Back</span></a>
        </div>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-5 lg:grid-cols-3">
        <div class="card p-7 lg:col-span-2">
            <h2 class="mb-4 text-xl font-bold text-slate-900">Customer Information</h2>
            <dl class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                <div><dt class="mb-1 text-slate-500">First Name</dt><dd class="font-semibold text-slate-800">{{ $customer->first_name }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Last Name</dt><dd class="font-semibold text-slate-800">{{ $customer->last_name ?: 'N/A' }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Contact Info</dt><dd class="font-semibold text-slate-800">{{ $customer->contact_info ?: 'N/A' }}</dd></div>
                <div><dt class="mb-1 text-slate-500">Address</dt><dd class="font-semibold text-slate-800">{{ $customer->address ?: 'N/A' }}</dd></div>
            </dl>
        </div>

        <div class="card flex flex-col items-center justify-center p-7 text-center">
            <div class="text-sm text-slate-500">Total Sales Records</div>
            <div class="mt-2 text-4xl font-bold text-slate-900">{{ $customer->salesTransactions->count() }}</div>
        </div>
    </div>

    <div class="card table-wrap">
        <div class="section-head">
            <h2 class="text-xl font-bold text-slate-900">Sales Transactions</h2>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Date</th>
                    <th>Sales Person</th>
                    <th>Total Amount</th>
                    <th>Payment Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customer->salesTransactions as $sale)
                    <tr>
                        <td class="font-semibold text-slate-800"><a href="{{ route('sales.show', $sale) }}" class="hover:text-primary">SALE-{{ str_pad($sale->id, 3, '0', STR_PAD_LEFT) }}</a></td>
                        <td>{{ $sale->sales_date->format('Y-m-d') }}</td>
                        <td>{{ $sale->employee->full_name }}</td>
                        <td class="font-semibold text-slate-800">&#8369;{{ number_format($sale->total_amount, 2) }}</td>
                        <td>{{ ucfirst($sale->payment?->status ?? $sale->status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-16 text-center text-slate-400">No sales transactions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
