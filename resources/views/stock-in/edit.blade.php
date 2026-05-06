@extends('layouts.app')
@section('title', 'Edit Stock In')

@section('content')
    @include('stock-in.partials.form', [
        'title' => 'Edit Stock In Transaction',
        'subtitle' => 'Update the delivery details and item quantities while keeping inventory in sync.',
        'action' => route('stock-in.update', $stockIn),
        'method' => 'PUT',
        'submitLabel' => 'Save Changes',
        'stockIn' => $stockIn,
    ])
@endsection
