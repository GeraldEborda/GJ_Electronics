@extends('layouts.app')
@section('title', 'Edit Payment Method')

@section('content')
    @include('payment-methods.partials.form', [
        'title' => 'Edit Payment Method',
        'subtitle' => 'Update an available payment option.',
        'action' => route('payment-methods.update', $paymentMethod),
        'method' => 'PUT',
        'paymentMethod' => $paymentMethod,
    ])
@endsection
