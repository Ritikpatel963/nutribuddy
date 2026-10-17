@extends('layouts.admin')

@php
  $title = 'Edit Nutrition Question';
  $subTitle = 'Diet Chart / Nutrition Questions / Edit';
@endphp

@section('content')
  <div class="card">
    <div class="card-header">
      <h5 class="card-title mb-0">Edit Question</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.nutrition.questions.update', $question->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-8">
                    <label class="form-label">Question <span class="text-danger">*</span></label>
                    <input type="text" name="question" class="form-control" value="{{ old('question', $question->question) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sort Order <span class="text-danger">*</span></label>
                    <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', $question->sort_order) }}" required>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update Question</button>
                <a href="{{ route('admin.nutrition.questions.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
  </div>
@endsection
