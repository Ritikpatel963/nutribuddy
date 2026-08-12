@extends('layout.layout')
@php
    $title = 'Edit Ingredient';
    $subTitle = 'Ecommerce / Ingredients / Edit';
@endphp

@section('content')
    @include('admin.ecommerce._messages')

    @include('admin.ecommerce.ingredients._form', [
        'formAction' => route('admin.ecommerce.ingredients.update', $ingredient),
        'submitLabel' => 'Update Ingredient',
        'ingredient' => $ingredient,
    ])
@endsection
