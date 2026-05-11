@extends('layouts.app')
@section('title', 'Sales')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">Sales Management</h1>
            <p class="page-subtitle">Track transactions, payment status, and top-performing products.</p>
        </div>
        <a href="{{ route('sales.create') }}" class="btn-success">
            <i class="fa-solid fa-plus"></i>
            <span>New Sale</span>
        </a>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="card stat-card">
            <div>
                <div class="mb-1 text-xs font-medium text-slate-500">Total Sales</div>
                <div class="text-3xl font-bold text-slate-800">&#8369;{{ number_format($totalSales, 0) }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50">
                <i class="fa-solid fa-cart-shopping text-xl text-emerald-500"></i>
            </div>
        </div>
        <div class="card stat-card">
            <div>
                <div class="mb-1 text-xs font-medium text-slate-500">Transactions</div>
                <div class="text-3xl font-bold text-slate-800">{{ $totalTransactions }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50">
                <i class="fa-regular fa-calendar text-xl text-blue-500"></i>
            </div>
        </div>
        <div class="card stat-card">
            <div>
                <div class="mb-1 text-xs font-medium text-slate-500">Average Transaction</div>
                <div class="text-3xl font-bold text-slate-800">&#8369;{{ number_format($avgTransaction, 0) }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50">
                <i class="fa-solid fa-chart-line text-xl text-violet-500"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-5">
        <div class="card overflow-hidden xl:col-span-3">
            <div class="section-head">
                <h2 class="text-xl font-bold text-slate-900">Recent Sales Transactions</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($sales as $sale)
                    <div class="flex items-center gap-4 px-6 py-5 hover:bg-slate-50/70">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600">
                            <i class="fa-solid fa-cart-shopping text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-base font-bold text-slate-800">{{ $sale->sale_code }}</span>
                                @if($sale->status === 'cancelled')
                                    <span class="badge-red">Cancelled</span>
                                @elseif($sale->payment)
                                    @if($sale->payment->status === 'paid')
                                        <span class="badge-green">Paid</span>
                                    @else
                                        <span class="badge-yellow">Partial</span>
                                    @endif
                                @endif
                            </div>
                            <div class="mt-1 text-sm font-medium text-slate-600">{{ $sale->customer->full_name }}</div>
                            <div class="mt-1 flex flex-wrap items-center gap-3 text-xs text-slate-400">
                                <span><i class="fa-regular fa-calendar mr-1"></i>{{ $sale->sales_date->format('Y-m-d') }}</span>
                                <span><i class="fa-regular fa-user mr-1"></i>{{ $sale->employee->full_name }}</span>
                                @if($sale->payment)
                                    <span><i class="fa-solid fa-credit-card mr-1"></i>{{ $sale->payment->paymentMethod?->payment_method_name ?: 'N/A' }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-emerald-600">&#8369;{{ number_format($sale->total_amount, 0) }}</div>
                            <div class="mt-1 flex items-center justify-end gap-3">
                                <a href="{{ route('sales.show', $sale) }}" class="inline-block text-xs font-semibold text-primary hover:underline">View</a>
                                @if($sale->status !== 'cancelled')
                                    <a href="{{ route('sales.edit', $sale) }}" class="inline-block text-xs font-semibold text-slate-500 hover:underline">Edit</a>
                                    <form method="POST" action="{{ route('sales.destroy', $sale) }}" onsubmit="return confirm('Cancel this sale and return the sold items to inventory?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-block text-xs font-semibold text-red-500 hover:underline">Cancel</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center text-slate-400">No sales transactions yet.</div>
                @endforelse
            </div>
        </div>

        <div class="card p-7 xl:col-span-2">
            <h2 class="mb-5 text-xl font-bold text-slate-900">Top Selling Products</h2>
            <div class="space-y-5">
                @forelse($topProducts as $i => $tp)
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-50 font-bold text-blue-600">{{ $i + 1 }}</div>
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-base font-bold text-slate-800">{{ $tp->product->product_name }}</div>
                            <div class="text-sm text-slate-500">{{ $tp->total_qty }} units sold</div>
                            @php
                                $maxQty = $topProducts->max('total_qty');
                                $pct = $maxQty > 0 ? round(($tp->total_qty / $maxQty) * 100) : 0;
                            @endphp
                            <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">
                                <div class="h-2 rounded-full bg-primary" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        <div class="text-sm font-bold text-slate-700">&#8369;{{ number_format($tp->total_revenue, 0) }}</div>
                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-400">No sales data yet.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
