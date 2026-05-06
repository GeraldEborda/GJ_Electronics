@extends('layouts.app')
@section('title', 'Edit Payment')

@section('content')
    @include('payments.partials.form', [
        'title' => 'Edit Payment',
        'subtitle' => 'Update the payment details of a sales transaction.',
        'action' => route('payments.update', $payment),
        'method' => 'PUT',
        'payment' => $payment,
        'sales' => $sales,
        'paymentMethods' => $paymentMethods,
    ])
@endsection
