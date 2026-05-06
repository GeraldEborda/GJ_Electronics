@extends('layouts.app')
@section('title', 'Stock In Details')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">{{ $stockIn->stock_in_code }}</h1>
            <p class="page-subtitle">Detailed view of this stock in transaction.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('stock-in.edit', $stockIn) }}" class="btn-primary">
                <i class="fa-solid fa-pen-to-square"></i>
                <span>Edit</span>
            </a>
            <a href="{{ route('stock-in.index') }}" class="btn-secondary">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>
    </div>

    <div class="mb-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="card p-7 xl:col-span-2">
            <h2 class="mb-4 text-xl font-bold text-slate-900">Transaction Info</h2>
            <dl class="grid grid-cols-1 gap-4 text-sm md:grid-cols-2">
                <div>
                    <dt class="mb-1 text-slate-500">Supplier</dt>
                    <dd class="font-semibold text-slate-800">{{ $stockIn->supplier->supplier_name }}</dd>
                </div>
                <div>
                    <dt class="mb-1 text-slate-500">Delivery Receipt No.</dt>
                    <dd class="font-semibold text-slate-800">{{ $stockIn->delivery_receipt_no ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="mb-1 text-slate-500">Date Received</dt>
                    <dd class="font-semibold text-slate-800">{{ $stockIn->date_received->format('F j, Y') }}</dd>
                </div>
                <div>
                    <dt class="mb-1 text-slate-500">Received By</dt>
                    <dd class="font-semibold text-slate-800">{{ $stockIn->employee->full_name }}</dd>
                </div>
                @if($stockIn->remarks)
                    <div class="md:col-span-2">
                        <dt class="mb-1 text-slate-500">Remarks</dt>
                        <dd class="text-slate-700">{{ $stockIn->remarks }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="card flex flex-col items-center justify-center p-7 text-center">
            <div class="text-sm text-slate-500">Total Amount</div>
            <div class="mt-2 text-4xl font-bold text-slate-900">&#8369;{{ number_format($stockIn->total_amount, 2) }}</div>
            <div class="mt-4"><span class="badge-green">Completed</span></div>
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
                    <th>Qty Received</th>
                    <th>Cost/Unit</th>
                    <th>Total</th>
                    <th>Condition</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockIn->details as $detail)
                    <tr>
                        <td class="font-semibold text-slate-800">{{ $detail->product->product_name }}</td>
                        <td>{{ $detail->product->category->category_name }}</td>
                        <td class="font-bold text-slate-800">{{ $detail->quantity_received }}</td>
                        <td>&#8369;{{ number_format($detail->cost_per_unit, 2) }}</td>
                        <td class="font-semibold text-slate-800">&#8369;{{ number_format($detail->total_amount, 2) }}</td>
                        <td>
                            @if($detail->condition_status === 'good')
                                <span class="badge-green">Good</span>
                            @elseif($detail->condition_status === 'damaged')
                                <span class="badge-red">Damaged</span>
                            @else
                                <span class="badge-yellow">{{ ucfirst($detail->condition_status) }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
