@extends('layouts.app')
@section('title', 'Stock In')

@section('content')
    <div class="mb-8 flex items-start justify-between">
        <div>
            <h1 class="text-5xl font-black text-slate-900">Stock In Management</h1>
            <p class="mt-2 text-xl text-slate-500">Record incoming inventory from suppliers.</p>
        </div>
        <a href="{{ route('stock-in.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            <span>New Stock In</span>
        </a>
    </div>

    <div class="card overflow-hidden">
        <div class="border-b border-slate-200 px-7 py-5">
            <h2 class="text-2xl font-bold text-slate-900">Recent Stock In Transactions</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-500">
                    <tr>
                        <th class="px-7 py-4 font-semibold">Stock In ID</th>
                        <th class="px-4 py-4 font-semibold">DR No.</th>
                        <th class="px-4 py-4 font-semibold">Date</th>
                        <th class="px-4 py-4 font-semibold">Supplier</th>
                        <th class="px-4 py-4 font-semibold">Received By</th>
                        <th class="px-4 py-4 font-semibold">Items</th>
                        <th class="px-4 py-4 font-semibold">Total Amount</th>
                        <th class="px-4 py-4 font-semibold">Status</th>
                        <th class="px-4 py-4 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stockIns as $stockIn)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-7 py-5">
                                <a href="{{ route('stock-in.show', $stockIn) }}" class="font-bold text-slate-800 hover:text-primary">
                                    {{ $stockIn->stock_in_code }}
                                </a>
                            </td>
                            <td class="px-4 py-5 text-slate-500">{{ $stockIn->delivery_receipt_no ?? 'N/A' }}</td>
                            <td class="px-4 py-5 text-slate-700">{{ $stockIn->date_received->format('Y-m-d') }}</td>
                            <td class="px-4 py-5 text-slate-700">{{ $stockIn->supplier->supplier_name }}</td>
                            <td class="px-4 py-5 text-slate-700">{{ $stockIn->employee->full_name }}</td>
                            <td class="px-4 py-5 text-slate-700">{{ $stockIn->details->count() }} items</td>
                            <td class="px-4 py-5 font-bold text-slate-900">&#8369;{{ number_format($stockIn->total_amount, 0) }}</td>
                            <td class="px-4 py-5"><span class="badge-green">Completed</span></td>
                            <td class="px-4 py-5">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('stock-in.show', $stockIn) }}" class="btn-secondary !min-h-0 !px-4 !py-2">View</a>
                                    <a href="{{ route('stock-in.edit', $stockIn) }}" class="btn-secondary !min-h-0 !px-4 !py-2">Edit</a>
                                    <form method="POST" action="{{ route('stock-in.destroy', $stockIn) }}" onsubmit="return confirm('Archive this stock in transaction?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-2xl border border-amber-100 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100">Archive</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-7 py-14 text-center text-slate-400">No stock in transactions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
