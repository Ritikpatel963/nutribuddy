@extends('layout.layout')

@php
  $title = 'Nutrition Questions';
  $subTitle = 'Diet Chart / Nutrition Questions';
@endphp

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Nutrition Questions</h5>
      <a href="{{ route('admin.nutrition.questions.create') }}" class="btn btn-primary">+ Add Question</a>
    </div>
    
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      
      @if($questions->isEmpty())
        <div class="text-center p-4">
          <p class="text-muted mb-3">No nutrition questions found.</p>
          <a href="{{ route('admin.nutrition.questions.create') }}" class="btn btn-outline-primary">Create your first question</a>
        </div>
      @else
        <div class="table-responsive">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th>ID</th>
                <th>Model</th>
                <th>Question</th>
                <th>Type</th>
                <th>Order</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($questions as $question)
                <tr>
                  <td>{{ $question->id }}</td>
                  <td><span class="badge bg-secondary">{{ $question->model }}</span></td>
                  <td>{{ Str::limit($question->question, 50) }}</td>
                  <td>{{ $question->answer_type }}</td>
                  <td>{{ $question->sort_order }}</td>
                  <td>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.nutrition.questions.edit', $question->id) }}" class="btn btn-sm btn-info text-white">Edit</a>
                        <form action="{{ route('admin.nutrition.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Delete this question?');">
                          @csrf @method('DELETE')
                          <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
@endsection
