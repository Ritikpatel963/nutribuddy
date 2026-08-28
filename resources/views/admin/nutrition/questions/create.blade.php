@extends('layout.layout')

@php
  $title = 'Add Nutrition Question';
  $subTitle = 'Diet Chart / Nutrition Questions / Add';
@endphp

@section('content')
  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0">Add Question</h5>
    </div>
    <div class="card-body">
        <p>Form to add a question goes here...</p>
        <a href="{{ route('admin.nutrition.questions.index') }}" class="btn btn-secondary">Back</a>
    </div>
  </div>
@endsection
