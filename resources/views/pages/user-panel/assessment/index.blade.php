@extends('layouts.user-panel')
@section('title', 'Health Assessment — NutriBuddy Kids')
@section('panel-page-class', 'panel-assessment')

@section('panel-content')
<div class="ud-main">
  <div class="page">

    {{-- PAGE HEADER --}}
    <div class="page-header fade-in d1">
      <div class="page-header-left">
        <h1>🩺 Child Health Assessment</h1>
        <p>Answer every question honestly to get the most accurate health score for your child.</p>
      </div>
    </div>

    {{-- QUIZ FORM --}}
    <form id="quizForm" action="{{ route('user.assessment.store') }}" method="POST" class="quiz-form">
      @csrf

      {{-- Progress Bar --}}
      <div class="quiz-progress-wrap fade-in d2">
        <div class="quiz-progress-header">
          <span class="quiz-progress-label" id="progressLabel">Question 1 of {{ $totalQuestions }}</span>
          <span class="quiz-progress-pct" id="progressPct">0%</span>
        </div>
        <div class="quiz-progress-track">
          <div class="quiz-progress-fill" id="progressFill" style="width:0%"></div>
        </div>
        <div class="quiz-section-tag" id="sectionTag"></div>
      </div>

      {{-- Question Cards --}}
      <div class="quiz-questions-wrap fade-in d3" id="quizWrap">

        @php $qIndex = 0; @endphp
        @foreach($questionsGrouped as $section => $sectionQuestions)
          @foreach($sectionQuestions as $question)
            @php $qIndex++; @endphp
            <div class="quiz-card {{ $qIndex === 1 ? 'active' : '' }}"
                 id="qcard-{{ $qIndex }}"
                 data-index="{{ $qIndex }}"
                 data-section="{{ $section }}"
                 data-qid="{{ $question->id }}">

              <div class="quiz-card-header">
                <span class="quiz-section-badge">{{ $section }}</span>
                <span class="quiz-q-num">Q{{ $qIndex }} / {{ $totalQuestions }}</span>
              </div>

              <p class="quiz-question-text">{{ $question->title }}</p>

              @if($question->description)
                <p class="quiz-question-hint">{{ $question->description }}</p>
              @endif

              <div class="quiz-options-wrap">
                @foreach($question->options as $option)
                  <label class="quiz-option" for="opt-{{ $question->id }}-{{ $option->id }}">
                    <input type="radio"
                           id="opt-{{ $question->id }}-{{ $option->id }}"
                           name="answers[{{ $question->id }}]"
                           value="{{ $option->id }}"
                           class="quiz-radio"
                           data-qindex="{{ $qIndex }}"
                           onchange="onOptionSelect({{ $qIndex }})">
                    <span class="quiz-option-mark"></span>
                    <span class="quiz-option-text">{{ $option->option_text }}</span>
                    <span class="quiz-option-score">+{{ $option->score }} pts</span>
                  </label>
                @endforeach
              </div>

              {{-- Validation Error --}}
              <div class="quiz-validation-msg" id="qerr-{{ $qIndex }}" style="display:none;">
                ⚠️ Please select an answer to continue.
              </div>
            </div>
          @endforeach
        @endforeach

      </div>{{-- /quiz-questions-wrap --}}

      {{-- Navigation Buttons --}}
      <div class="quiz-nav fade-in d4" id="quizNav">
        <button type="button" class="quiz-btn-prev" id="btnPrev" onclick="prevQuestion()" style="display:none;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
          Previous
        </button>

        <div class="quiz-answered-count">
          <span id="answeredCount">0</span> / {{ $totalQuestions }} answered
        </div>

        <button type="button" class="quiz-btn-next" id="btnNext" onclick="nextQuestion()">
          Next
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
        </button>

        <button type="submit" class="quiz-btn-submit" id="btnSubmit" style="display:none;" onclick="confirmSubmit(event)">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
          Submit Assessment
        </button>
      </div>

    </form>

  </div>{{-- /page --}}
</div>{{-- /ud-main --}}

@push('scripts')
<script>
(function () {
  const total      = {{ $totalQuestions }};
  let current      = 1;
  let answered     = {};   // { qIndex: true }

  const fill       = document.getElementById('progressFill');
  const label      = document.getElementById('progressLabel');
  const pct        = document.getElementById('progressPct');
  const secTag     = document.getElementById('sectionTag');
  const btnPrev    = document.getElementById('btnPrev');
  const btnNext    = document.getElementById('btnNext');
  const btnSubmit  = document.getElementById('btnSubmit');
  const ansCount   = document.getElementById('answeredCount');

  function card(i) { return document.getElementById('qcard-' + i); }

  function updateProgress() {
    const p     = Math.round(((current - 1) / total) * 100);
    fill.style.width = p + '%';
    label.textContent = 'Question ' + current + ' of ' + total;
    pct.textContent   = p + '%';
    secTag.textContent = card(current).dataset.section;
    ansCount.textContent = Object.keys(answered).length;
  }

  function showCard(i) {
    document.querySelectorAll('.quiz-card').forEach(c => c.classList.remove('active'));
    card(i).classList.add('active');

    btnPrev.style.display   = i > 1    ? 'flex' : 'none';
    btnNext.style.display   = i < total ? 'flex' : 'none';
    btnSubmit.style.display = i === total ? 'flex' : 'none';

    current = i;
    updateProgress();
  }

  window.onOptionSelect = function (qIndex) {
    answered[qIndex] = true;
    document.getElementById('qerr-' + qIndex).style.display = 'none';
    card(qIndex).querySelectorAll('.quiz-option').forEach(o => o.classList.remove('selected'));
    const checked = card(qIndex).querySelector('input[type=radio]:checked');
    if (checked) checked.closest('.quiz-option').classList.add('selected');
    ansCount.textContent = Object.keys(answered).length;
  };

  window.nextQuestion = function () {
    if (!answered[current]) {
      document.getElementById('qerr-' + current).style.display = 'flex';
      card(current).classList.add('shake');
      setTimeout(() => card(current).classList.remove('shake'), 600);
      return;
    }
    if (current < total) showCard(current + 1);
  };

  window.prevQuestion = function () {
    if (current > 1) showCard(current - 1);
  };

  window.confirmSubmit = function (event) {
    event.preventDefault();
    // Validate all questions have an answer.
    const missing = [];
    for (let i = 1; i <= total; i++) {
      if (!answered[i]) missing.push(i);
    }
    if (missing.length > 0) {
      showCard(missing[0]);
      document.getElementById('qerr-' + missing[0]).style.display = 'flex';
      return;
    }
    btnSubmit.disabled = true;
    btnSubmit.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg> Calculating…';
    document.getElementById('quizForm').submit();
  };

  // Restore selection state on page load (e.g. browser back).
  document.querySelectorAll('.quiz-radio').forEach(radio => {
    if (radio.checked) {
      const qi = parseInt(radio.dataset.qindex);
      answered[qi] = true;
      radio.closest('.quiz-option').classList.add('selected');
    }
  });

  // Init.
  showCard(1);
})();
</script>
@endpush
@endsection
