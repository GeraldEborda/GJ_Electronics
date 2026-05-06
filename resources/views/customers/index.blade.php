@extends('layouts.app')
@section('title', 'Customers')

@section('content')
    <div class="mb-8 flex items-start justify-between gap-4">
        <div>
            <h1 class="page-title">Customer Management</h1>
            <p class="page-subtitle">Manage customer records used in sales transactions.</p>
        </div>
        <a href="{{ route('customers.create') }}" class="btn-primary"><i class="fa-solid fa-plus"></i><span>Add Customer</span></a>
    </div>

    <div class="card mb-6 p-5">
        <form method="GET" action="{{ route('customers.index') }}">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer name, contact, or address..." class="form-input pl-11">
            </div>
        </form>
    </div>

    <div class="card table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Contact Info</th>
                    <th>Address</th>
                    <th>Sales Records</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td class="font-semibold text-slate-800">{{ $customer->first_name }}</td>
                        <td>{{ $customer->last_name ?: 'N/A' }}</td>
                        <td>{{ $customer->contact_info ?: 'N/A' }}</td>
                        <td>{{ $customer->address ?: 'N/A' }}</td>
                        <td>{{ $customer->sales_transactions_count }}</td>
                        <td>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('customers.show', $customer) }}" class="btn-secondary !min-h-0 !px-4 !py-2"><i class="fa-solid fa-eye"></i><span>View</span></a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn-secondary !min-h-0 !px-4 !py-2"><i class="fa-solid fa-pen-to-square"></i><span>Edit</span></a>
                                <form method="POST" action="{{ route('customers.destroy', $customer) }}" onsubmit="return confirm('Archive this customer?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="flex items-center gap-2 rounded-2xl border border-amber-100 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 transition hover:bg-amber-100"><i class="fa-solid fa-box-archive"></i><span>Archive</span></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="py-16 text-center text-slate-400">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
