<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\SalesTransaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::active()
            ->with(['salesTransaction.customer', 'paymentMethod'])
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $sales = SalesTransaction::doesntHave('payment')
            ->with('customer')
            ->orderByDesc('sales_date')
            ->orderByDesc('id')
            ->get();
        $paymentMethods = PaymentMethod::orderBy('payment_method_name')->get();

        return view('payments.create', compact('sales', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayment($request);

        $payment = Payment::create($validated);
        $payment->salesTransaction->refreshStatusFromPayment();

        return redirect()->route('payments.index')->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['salesTransaction.customer', 'salesTransaction.details.product', 'paymentMethod']);

        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $sales = SalesTransaction::with('customer')
            ->where(function ($query) use ($payment) {
                $query->doesntHave('payment')->orWhere('id', $payment->sales_transaction_id);
            })
            ->orderByDesc('sales_date')
            ->orderByDesc('id')
            ->get();
        $paymentMethods = PaymentMethod::orderBy('payment_method_name')->get();

        return view('payments.edit', compact('payment', 'sales', 'paymentMethods'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $this->validatePayment($request, $payment->id);

        $payment->update($validated);
        $payment->salesTransaction->refreshStatusFromPayment();

        return redirect()->route('payments.index')->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->update(['is_archived' => true]);
        $payment->salesTransaction->refreshStatusFromPayment();

        return redirect()->route('payments.index')->with('success', 'Payment archived successfully.');
    }

    protected function validatePayment(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'sales_transaction_id' => [
                'required',
                'exists:sales_transactions,id',
                Rule::unique('payments', 'sales_transaction_id')
                    ->where(fn ($query) => $query->where('is_archived', false))
                    ->ignore($ignoreId),
            ],
            'payment_date' => ['required', 'date'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
        ]);

        $totalAmount = (float) SalesTransaction::whereKey($validated['sales_transaction_id'])->value('total_amount');

        $this->validatePaymentAmount(
            (float) $validated['amount_paid'],
            $totalAmount
        );

        $validated['status'] = Payment::statusForAmount((float) $validated['amount_paid'], $totalAmount);

        return $validated;
    }

    protected function validatePaymentAmount(float $amountPaid, float $totalAmount): void
    {
        if ($amountPaid > $totalAmount) {
            throw ValidationException::withMessages([
                'amount_paid' => 'Amount paid cannot exceed the sale total.',
            ]);
        }
    }
}
