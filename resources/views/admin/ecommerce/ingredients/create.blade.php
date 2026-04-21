@extends('layout.layout')
@php
    $title = 'Create Ingredient';
    $subTitle = 'Ecommerce / Ingredients / Create';
@endphp

@section('content')
    @include('admin.ecommerce._messages')

    @include('admin.ecommerce.ingredients._form', [
        'formAction' => route('admin.ecommerce.ingredients.store'),
        'submitLabel' => 'Create Ingredient',
    ])
@endsection
