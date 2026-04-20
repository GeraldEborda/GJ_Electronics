@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">A quick view of products, sales performance, and inventory alerts.</p>
    </div>

    <div class="mb-7 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card stat-card">
            <div>
                <div class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-500">Total Products</div>
                <div class="text-3xl font-bold text-slate-800">{{ $totalProducts }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50">
                <i class="fa-solid fa-cube text-xl text-blue-500"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div>
                <div class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-500">Low Stock Items</div>
                <div class="text-3xl font-bold text-slate-800">{{ $lowStockItems }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50">
                <i class="fa-solid fa-triangle-exclamation text-xl text-amber-500"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div>
                <div class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-500">Total Sales (This Month)</div>
                <div class="text-2xl font-bold text-slate-800">&#8369;{{ number_format($totalSalesThisMonth, 0) }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50">
                <i class="fa-solid fa-peso-sign text-xl text-emerald-500"></i>
            </div>
        </div>

        <div class="card stat-card">
            <div>
                <div class="mb-1 text-xs font-medium uppercase tracking-wide text-slate-500">Transactions (Weekly)</div>
                <div class="text-3xl font-bold text-slate-800">{{ $weeklyTransactions }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50">
                <i class="fa-solid fa-cart-shopping text-xl text-violet-500"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 xl:grid-cols-5">
        <div class="card p-7 xl:col-span-3">
            <div class="mb-5 flex items-center justify-between">
                <h2 class="text-xl font-bold text-slate-900">Recent Activity</h2>
                <span class="text-sm text-slate-400">Latest sales</span>
            </div>

            <div class="space-y-4">
                @forelse($recentSales as $sale)
                    <div class="flex items-start gap-4 rounded-2xl border border-slate-100 bg-slate-50/60 px-4 py-4">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $sale->status === 'completed' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600' }}">
                            <i class="fa-solid fa-cart-shopping text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="text-base font-bold text-slate-800">
                                {{ $sale->details->first()?->product?->product_name ?? 'Sale' }}
                                @if($sale->details->count() > 1)
                                    <span class="ml-1 text-xs font-medium text-slate-400">+{{ $sale->details->count() - 1 }} more</span>
                                @endif
                            </div>
                            <div class="mt-1 text-sm text-slate-500">
                                Quantity: {{ $sale->details->sum('quantity') }} • {{ $sale->customer->full_name }}
                            </div>
                            <div class="mt-1 text-xs text-slate-400">{{ $sale->sales_date->format('Y-m-d') }}</div>
                        </div>
                        <div class="text-right text-sm font-bold text-emerald-600">&#8369;{{ number_format($sale->total_amount, 0) }}</div>
                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-400">No recent activity</div>
                @endforelse
            </div>
        </div>

        <div class="card p-7 xl:col-span-2">
            <h2 class="mb-5 flex items-center gap-2 text-xl font-bold text-slate-900">
                <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                <span>Low Stock Alert</span>
            </h2>

            <div class="space-y-5">
                @forelse($lowStockAlerts as $alert)
                    @php
                        $pct = $alert->minimum_stock > 0 ? min(100, round(($alert->current_stock / $alert->minimum_stock) * 100)) : 0;
                        $isCritical = $alert->current_stock <= 5;
                    @endphp
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/60 p-4">
                        <div class="mb-2 flex items-center justify-between">
                            <span class="text-base font-bold text-slate-800">{{ $alert->product->product_name }}</span>
                            @if($isCritical)
                                <span class="badge-red">Critical</span>
                            @else
                                <span class="badge-yellow">Low Stock</span>
                            @endif
                        </div>
                        <div class="mb-3 text-sm text-slate-500">
                            Current: <span class="font-semibold text-slate-700">{{ $alert->current_stock }}</span> |
                            Min: <span class="font-semibold text-slate-700">{{ $alert->minimum_stock }}</span>
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full bg-slate-200">
                            <div class="h-2.5 rounded-full {{ $isCritical ? 'bg-red-500' : 'bg-amber-400' }}" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center text-sm text-slate-400">All items are well-stocked.</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
