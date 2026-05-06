@extends('layouts.app')
@section('title', 'Add Payment Method')

@section('content')
    @include('payment-methods.partials.form', [
        'title' => 'Add Payment Method',
        'subtitle' => 'Create a new payment option for the system.',
        'action' => route('payment-methods.store'),
        'method' => 'POST',
        'paymentMethod' => null,
    ])
@endsection
