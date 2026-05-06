@extends('layouts.app')
@section('title', 'Edit Customer')

@section('content')
    @include('customers.partials.form', [
        'title' => 'Edit Customer',
        'subtitle' => 'Update customer information used in sales transactions.',
        'action' => route('customers.update', $customer),
        'method' => 'PUT',
        'customer' => $customer,
    ])
@endsection
