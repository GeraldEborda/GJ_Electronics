@extends('layouts.app')
@section('title', 'New Stock In')

@section('content')
    @include('stock-in.partials.form', [
        'title' => 'Record Stock In Transaction',
        'subtitle' => 'Capture supplier deliveries and keep incoming inventory structured.',
        'action' => route('stock-in.store'),
        'method' => 'POST',
        'submitLabel' => 'Record Stock In',
        'stockIn' => null,
    ])
@endsection
