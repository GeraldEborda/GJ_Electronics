<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::active()
            ->withCount(['products', 'stockIns'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');

                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('supplier_name', 'like', "%{$search}%")
                        ->orWhere('contact_person', 'like', "%{$search}%")
                        ->orWhere('contact_info', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateSupplier($request);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier added successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $this->validateSupplier($request);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->update(['is_archived' => true]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier archived successfully.');
    }

    protected function validateSupplier(Request $request): array
    {
        return $request->validate([
            'supplier_name' => ['required', 'string', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:255'],
            'contact_info' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
        ]);
    }
}
