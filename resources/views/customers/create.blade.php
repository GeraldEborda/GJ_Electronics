@extends('layouts.app')
@section('title', 'Add Customer')

@section('content')
    @include('customers.partials.form', [
        'title' => 'Add Customer',
        'subtitle' => 'Create a customer record for sales transactions.',
        'action' => route('customers.store'),
        'method' => 'POST',
        'customer' => null,
    ])
@endsection
