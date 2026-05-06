@extends('layouts.app')
@section('title', 'Add Payment')

@section('content')
    @include('payments.partials.form', [
        'title' => 'Add Payment',
        'subtitle' => 'Record a payment for an existing sales transaction.',
        'action' => route('payments.store'),
        'method' => 'POST',
        'payment' => null,
        'sales' => $sales,
        'paymentMethods' => $paymentMethods,
    ])
@endsection
