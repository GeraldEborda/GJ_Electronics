@extends('layouts.app')
@section('title', 'Add Supplier')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-8">
            <h1 class="page-title">Add Supplier</h1>
            <p class="page-subtitle">Create a supplier record for product sourcing and stock in transactions.</p>
        </div>

        @if($errors->any())
            <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card p-8">
            <form method="POST" action="{{ route('suppliers.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="form-label">Supplier Name</label>
                    <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" class="form-input" placeholder="" required>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="form-label">Contact Person</label>
                        <input type="text" name="contact_person" value="{{ old('contact_person') }}" class="form-input" placeholder="">
                    </div>
                    <div>
                        <label class="form-label">Contact Info</label>
                        <input type="text" name="contact_info" value="{{ old('contact_info') }}" class="form-input" placeholder="">
                    </div>
                </div>

                <div>
                    <label class="form-label">Address</label>
                    <textarea name="address" rows="4" class="form-input" placeholder="">{{ old('address') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('suppliers.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-plus"></i>
                        <span>Add Supplier</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
