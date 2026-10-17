@extends('layouts.admin')

@php
  $title = 'Nutrition Questions';
  $subTitle = 'Diet Chart / Nutrition Questions';
@endphp

@section('content')
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Nutrition Questions</h5>
    </div>
    
    <div class="card-body">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif
      
      @if($groupedQuestions->isEmpty())
        <div class="text-center p-4">
          <p class="text-muted mb-3">No nutrition questions found.</p>
          <a href="{{ route('admin.nutrition.questions.create') }}" class="btn btn-outline-primary">Create your first question</a>
        </div>
      @else
        <div class="accordion mt-3" id="questionsAccordion">
          @foreach($groupedQuestions as $modelName => $sections)
            @php $modelId = Str::slug($modelName); @endphp
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading-{{ $modelId }}">
                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $modelId }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse-{{ $modelId }}">
                  <strong>{{ $modelName }}</strong>
                </button>
              </h2>
              <div id="collapse-{{ $modelId }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" aria-labelledby="heading-{{ $modelId }}" data-bs-parent="#questionsAccordion">
                <div class="accordion-body">
                  
                  @foreach($sections as $sectionName => $questions)
                    <div class="card mb-3 shadow-none border">
                      <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">{{ $sectionName }}</h6>
                      </div>
                      <div class="card-body p-0">
                        <div class="table-responsive">
                          <table class="table table-hover mb-0">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Scale/Options</th>
                                <th>Condition</th>
                                <th>Actions</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($questions as $question)
                                <tr>
                                  <td>{{ $question->sort_order }}</td>
                                  <td>
                                    <strong>{{ $question->question }}</strong><br>
                                    <small class="text-muted" title="Clinical Reason">{{ Str::limit($question->clinical_reason, 40) }}</small>
                                  </td>
                                  <td><span class="badge bg-secondary">{{ $question->answer_type }}</span></td>
                                  <td>{{ Str::limit($question->options_scale, 30) }}</td>
                                  <td><span class="badge bg-info">{{ $question->only_ask_if }}</span></td>
                                  <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.nutrition.questions.edit', $question->id) }}" class="btn btn-sm btn-info text-white"><i class="bx bx-edit-alt"></i> Edit</a>
                                        <form action="{{ route('admin.nutrition.questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Delete this question?');">
                                          @csrf @method('DELETE')
                                          <button type="submit" class="btn btn-sm btn-danger"><i class="bx bx-trash"></i> Delete</button>
                                        </form>
                                    </div>
                                  </td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  @endforeach

                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>
@endsection
