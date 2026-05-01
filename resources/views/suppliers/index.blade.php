@extends('layouts.app')
@section('title', 'Suppliers')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">Supplier Management</h1>
            <p class="page-subtitle">Manage supplier records used by products and stock in transactions.</p>
        </div>
        <a href="{{ route('suppliers.create') }}" class="btn-primary">
            <i class="fa-solid fa-plus"></i>
            <span>Add Supplier</span>
        </a>
    </div>

    <div class="card mb-6 p-5">
        <form method="GET" action="{{ route('suppliers.index') }}">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search supplier, contact person, or contact info..." class="form-input pl-11">
            </div>
        </form>
    </div>

    <div class="card table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Supplier</th>
                    <th>Contact Person</th>
                    <th>Contact Info</th>
                    <th>Address</th>
                    <th>Products</th>
                    <th>Stock In Records</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $supplier)
                    <tr>
                        <td class="font-semibold text-slate-800">{{ $supplier->supplier_name }}</td>
                        <td>{{ $supplier->contact_person ?: 'N/A' }}</td>
                        <td>{{ $supplier->contact_info ?: 'N/A' }}</td>
                        <td>{{ $supplier->address ?: 'N/A' }}</td>
                        <td>{{ $supplier->products_count }}</td>
                        <td>{{ $supplier->stock_ins_count }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn-secondary !min-h-0 !px-4 !py-2">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                    <span>Edit</span>
                                </a>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Archive this supplier?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-2 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100">
                                        <i class="fa-solid fa-box-archive"></i>
                                        <span>Archive</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-16 text-center text-slate-400">No suppliers found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
