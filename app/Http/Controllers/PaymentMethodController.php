<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index(Request $request)
    {
        $paymentMethods = PaymentMethod::withCount('payments')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->string('search');
                $query->where('payment_method_name', 'like', "%{$search}%");
            })
            ->latest()
            ->get();

        return view('payment-methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        return view('payment-methods.create');
    }

    public function store(Request $request)
    {
        PaymentMethod::create($this->validatePaymentMethod($request));

        return redirect()->route('payment-methods.index')->with('success', 'Payment method added successfully.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        return view('payment-methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $paymentMethod->update($this->validatePaymentMethod($request, $paymentMethod->id));

        return redirect()->route('payment-methods.index')->with('success', 'Payment method updated successfully.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->payments()->exists()) {
            return redirect()->route('payment-methods.index')->with('error', 'Payment method cannot be deleted because it is already used in payment records.');
        }

        $paymentMethod->delete();

        return redirect()->route('payment-methods.index')->with('success', 'Payment method deleted successfully.');
    }

    protected function validatePaymentMethod(Request $request, ?int $ignoreId = null): array
    {
        $uniqueRule = 'unique:payment_methods,payment_method_name';

        if ($ignoreId) {
            $uniqueRule .= ',' . $ignoreId;
        }

        return $request->validate([
            'payment_method_name' => ['required', 'string', 'max:255', $uniqueRule],
        ]);
    }
}
