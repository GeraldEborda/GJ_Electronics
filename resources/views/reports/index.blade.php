@extends('layouts.app')
@section('title', 'Reports')

@section('content')
    <div class="mb-8">
        <h1 class="page-title">Reports & Analytics</h1>
        <p class="page-subtitle">Review revenue, monthly performance, top products, and current stock value.</p>
    </div>

    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card stat-card">
            <div>
                <div class="text-xs font-medium text-slate-500">Total Revenue</div>
                <div class="text-3xl font-bold text-slate-800">&#8369;{{ number_format($totalRevenue, 0) }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50">
                <i class="fa-solid fa-peso-sign text-emerald-600"></i>
            </div>
        </div>
        <div class="card stat-card">
            <div>
                <div class="text-xs font-medium text-slate-500">Total Transactions</div>
                <div class="text-3xl font-bold text-slate-800">{{ $totalTransactions }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50">
                <i class="fa-solid fa-cart-shopping text-blue-500"></i>
            </div>
        </div>
        <div class="card stat-card">
            <div>
                <div class="text-xs font-medium text-slate-500">Total Products</div>
                <div class="text-3xl font-bold text-slate-800">{{ $totalProducts }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50">
                <i class="fa-solid fa-cube text-violet-500"></i>
            </div>
        </div>
        <div class="card stat-card">
            <div>
                <div class="text-xs font-medium text-slate-500">Avg. Transaction</div>
                <div class="text-3xl font-bold text-slate-800">&#8369;{{ number_format($avgTransaction, 0) }}</div>
            </div>
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50">
                <i class="fa-solid fa-arrow-trend-up text-amber-500"></i>
            </div>
        </div>
    </div>

    <div x-data="{ tab: 'sales' }" class="space-y-5">
        <div class="card inline-flex gap-1 p-2">
            <button @click="tab = 'sales'" :class="tab === 'sales' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'" class="rounded-2xl px-6 py-3 text-sm font-semibold transition">Sales Report</button>
            <button @click="tab = 'inventory'" :class="tab === 'inventory' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'" class="rounded-2xl px-6 py-3 text-sm font-semibold transition">Inventory Report</button>
        </div>

        <div x-show="tab === 'sales'" x-transition class="space-y-5">
            <div class="card table-wrap">
                <div class="section-head">
                    <h2 class="text-xl font-bold text-slate-900">Monthly Sales Summary</h2>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Sales</th>
                            <th>Transactions</th>
                            <th>Avg. per Transaction</th>
                            <th>Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlySales as $i => $month)
                            @php
                                $prevMonth = $i > 0 ? $monthlySales[$i - 1] : null;
                                $trend = $prevMonth ? ($month->total_sales > $prevMonth->total_sales ? 'up' : ($month->total_sales < $prevMonth->total_sales ? 'down' : 'flat')) : null;
                                $monthName = \Carbon\Carbon::createFromDate($month->year, $month->month, 1)->format('F Y');
                            @endphp
                            <tr>
                                <td class="font-medium text-slate-700"><i class="fa-regular fa-calendar mr-2 text-slate-400"></i>{{ $monthName }}</td>
                                <td class="font-bold text-emerald-600">&#8369;{{ number_format($month->total_sales, 0) }}</td>
                                <td>{{ $month->transactions }}</td>
                                <td>&#8369;{{ number_format($month->avg_per_transaction, 0) }}</td>
                                <td>
                                    @if($trend === 'up')
                                        <span class="text-xs font-semibold text-emerald-600"><i class="fa-solid fa-arrow-up mr-1"></i>Up</span>
                                    @elseif($trend === 'down')
                                        <span class="text-xs font-semibold text-red-500"><i class="fa-solid fa-arrow-down mr-1"></i>Down</span>
                                    @else
                                        <span class="text-xs text-slate-400">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center text-slate-400">No sales data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card table-wrap">
                <div class="section-head">
                    <h2 class="text-xl font-bold text-slate-900">Top Selling Products</h2>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Units Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topProducts as $i => $tp)
                            <tr>
                                <td><div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-50 font-bold text-blue-600">{{ $i + 1 }}</div></td>
                                <td class="font-semibold text-slate-800">{{ $tp->product->product_name }}</td>
                                <td>{{ $tp->total_qty }} units</td>
                                <td class="font-bold text-emerald-600">&#8369;{{ number_format($tp->total_revenue, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-slate-400">No data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div x-show="tab === 'inventory'" x-transition>
            <div class="card table-wrap">
                <div class="section-head">
                    <h2 class="text-xl font-bold text-slate-900">Current Inventory Status</h2>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Supplier</th>
                            <th>Current Stock</th>
                            <th>Min Stock</th>
                            <th>Unit Price</th>
                            <th>Stock Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventories as $inv)
                            <tr>
                                <td class="font-semibold text-slate-800">{{ $inv->product->product_name }}</td>
                                <td>{{ $inv->product->category->category_name }}</td>
                                <td>{{ $inv->product->supplier->supplier_name }}</td>
                                <td class="font-bold text-slate-800">{{ $inv->current_stock }}</td>
                                <td>{{ $inv->minimum_stock }}</td>
                                <td>&#8369;{{ number_format($inv->product->unit_price, 0) }}</td>
                                <td class="font-semibold text-slate-700">&#8369;{{ number_format($inv->current_stock * $inv->product->unit_price, 0) }}</td>
                                <td>
                                    @if($inv->status === 'in_stock')
                                        <span class="badge-green">In Stock</span>
                                    @elseif($inv->status === 'low_stock')
                                        <span class="badge-yellow">Low Stock</span>
                                    @elseif($inv->status === 'critical')
                                        <span class="badge-red">Critical</span>
                                    @else
                                        <span class="badge-red">Out of Stock</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
