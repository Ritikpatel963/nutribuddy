@extends('layouts.main')
@section('title', 'NutriBuddy — Personalize Your Plan')

@section('content')
<link rel="stylesheet" href="{{ asset('css/questionnaire.css') }}">

<div class="qz-wrap">

    {{-- Hero --}}
    <div class="qz-hero">
        <div class="qz-hero-badge">{{ $plan->name }} Plan</div>
        <h1>Build a <span>personalized</span> diet chart</h1>
        <p>Answer a few questions about your child and get a nutrition plan shaped around their age, goals, and daily routine.</p>
        <div class="qz-mini-stats">
            <div class="qz-mini-stat"><strong>{{ count($questions) }}</strong><span>guided steps</span></div>
            <div class="qz-mini-stat"><strong>2</strong><span>free preview days</span></div>
            <div class="qz-mini-stat"><strong>7</strong><span>day full plan</span></div>
        </div>
    </div>

    {{-- Shell --}}
    <div class="qz-shell">

        {{-- Side Panel --}}
        <aside class="qz-side-panel">
            <h3>What you get</h3>
            <p>Designed for busy parents, with clean steps and output that is easy to follow at home.</p>
            <div>
                <div class="qz-check-item"><span class="qz-tick">✓</span><span>Meal ideas based on diet preference and goals</span></div>
                <div class="qz-check-item"><span class="qz-tick">✓</span><span>Daily nutrition targets for calories, protein and micronutrients</span></div>
                <div class="qz-check-item"><span class="qz-tick">✓</span><span>Parent tips and a matching NutriBuddy product suggestion</span></div>
            </div>
            <div class="qz-side-note">Tip: add height and weight if you know them. The plan still works without them, but the summary becomes more accurate.</div>
        </aside>

        {{-- Main Card --}}
        <div class="qz-main">

            {{-- Stepper --}}
            <div class="qz-stepper">
                @php $stepCount = count($questions); $si = 1; @endphp
                @foreach($questions as $sectionName => $sectionQuestions)
                    <div class="qz-sp-step {{ $si === 1 ? 'active' : '' }}" id="qzSp{{ $si }}">
                        <div class="qz-sp-ball">{{ $si }}</div>
                        <div class="qz-sp-label">{{ Str::words($sectionName, 2, '') }}</div>
                    </div>
                    @if($si < $stepCount)
                        <div class="qz-sp-line"><div class="qz-sp-line-fill" id="qzLine{{ $si }}"></div></div>
                    @endif
                    @php $si++; @endphp
                @endforeach
            </div>

            {{-- CSRF hidden form --}}
            <form id="questionnaireForm" style="display:none;">
                @csrf
                <input type="hidden" name="plan_slug" value="{{ $plan->slug }}">
                <input type="hidden" name="cycle" value="{{ $cycle }}">
                @if($child)<input type="hidden" name="child_id" value="{{ $child->id }}">@endif
            </form>

            {{-- Step Panels --}}
            @php
                $step = 1;
                $stepEmojis = ['👶','🎯','🥗','💡','📊','🌟','🧩','📋'];
                $ageEmojis  = ['👶','🧒','🧑','🧑','🧑'];
                $ageLabels  = ['Toddler','Pre-School','Primary','Middle','Teen'];
                $genderEmojis = ['👦','👧','🧒'];
            @endphp

            @foreach($questions as $sectionName => $sectionQuestions)
            <div class="qz-step-panel {{ $step === 1 ? 'active' : '' }}" id="qzPanel{{ $step }}">

                <div class="qz-step-head">
                    <span class="qz-step-emoji">{{ $stepEmojis[($step-1) % count($stepEmojis)] }}</span>
                    <div class="qz-section-badge">Section {{ $step }} of {{ $stepCount }}</div>
                    <h3>{{ $sectionName }}</h3>
                    <p>Please fill in the details below to personalize your child's plan.</p>
                </div>

                @foreach($sectionQuestions as $q)
                @php
                    $ql = strtolower($q->question);
                    $isHeight  = str_contains($ql, 'height');
                    $isWeight  = str_contains($ql, 'weight');
                    $isName    = str_contains($ql, 'name');
                    $isAgeQ    = str_contains($ql, 'age') && $q->answer_type === 'Choice';
                    $isGenderQ = str_contains($ql, 'gender') || str_contains($ql, 'sex');
                    $isMulti   = $q->answer_type === 'Multi';
                @endphp

                <div class="qz-question-block" data-qid="{{ $q->id }}">

                    @if($q->answer_type === 'Text' || $q->answer_type === 'Date' || $q->answer_type === 'Number')

                        @if($isHeight || $isWeight)
                            <label class="qz-field-label">
                                {{ $isHeight ? '📏' : '⚖️' }} {{ $q->question }}
                                <span style="font-size:.72rem;color:#aaa;font-weight:500;margin-left:4px">(Optional)</span>
                            </label>
                            
                            <input type="text"
                                   class="qz-text-input qz-form-field"
                                   data-qid="{{ $q->id }}"
                                   placeholder="{{ $isHeight ? 'e.g. 115 cm or 4 ft 2 in' : 'e.g. 22 kg or 48 lbs' }}">
                        @else
                            @php
                                $qLower = strtolower($q->question);
                                $isH = str_contains($qLower, 'height');
                                $isW = str_contains($qLower, 'weight');
                            @endphp
                            <label class="qz-field-label">
                                @if($isName)👦 @endif{{ $q->question }}
                                <span style="color:#e84040">*</span>
                            </label>
                            <input type="{{ $q->answer_type === 'Date' ? 'date' : 'text' }}"
                                   class="qz-text-input qz-form-field"
                                   data-qid="{{ $q->id }}"
                                   placeholder="{{ $isName ? 'e.g. Aarav Sharma' : ($isH ? 'e.g. 115 cm or 4 ft 2 in' : ($isW ? 'e.g. 22 kg or 48 lbs' : '')) }}"
                                   required>
                        @endif

                    @elseif($q->answer_type === 'Choice' || $q->answer_type === 'Multi')
                        @php $opts = array_map('trim', explode('/', $q->options_scale)); @endphp

                        @if($isAgeQ)
                            <div class="qz-sub-heading">Age Group</div>
                            <div class="qz-age-grid" id="ageGrid_{{ $q->id }}">
                                @foreach($opts as $idx => $opt)
                                <div class="qz-age-card" data-val="{{ $opt }}"
                                     onclick="qzSelectCard(this,'ageGrid_{{ $q->id }}','{{ $q->id }}',false)">
                                    <span class="qz-age-emoji">{{ $ageEmojis[$idx] ?? '🧒' }}</span>
                                    <span class="qz-age-range">{{ $opt }}</span>
                                    <span class="qz-age-label">{{ $ageLabels[$idx] ?? '' }}</span>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" class="qz-form-field" data-qid="{{ $q->id }}" id="hidAge_{{ $q->id }}" required>

                        @elseif($isGenderQ)
                            <div class="qz-sub-heading">Gender</div>
                            <div class="qz-gender-row" id="genderGrid_{{ $q->id }}">
                                @foreach($opts as $idx => $opt)
                                <div class="qz-gender-card" data-val="{{ $opt }}"
                                     onclick="qzSelectCard(this,'genderGrid_{{ $q->id }}','{{ $q->id }}',false)">
                                    <span class="qz-gender-emoji">{{ $genderEmojis[$idx] ?? '🧒' }}</span>
                                    <div class="qz-gender-name">{{ $opt }}</div>
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" class="qz-form-field" data-qid="{{ $q->id }}" id="hidGender_{{ $q->id }}" required>

                        @else
                            <label class="qz-field-label">
                                {{ $q->question }} <span style="color:#e84040">*</span>
                                @if($isMulti)<span style="font-size:.7rem;color:#aaa;font-weight:500;margin-left:4px">(Select all that apply)</span>@endif
                            </label>
                            <div class="qz-choice-row" id="choiceRow_{{ $q->id }}">
                                @foreach($opts as $opt)
                                <div class="qz-choice-chip"
                                     data-val="{{ $opt }}"
                                     onclick="qzSelectChip(this,'choiceRow_{{ $q->id }}','{{ $q->id }}',{{ $isMulti ? 'true' : 'false' }})">
                                    {{ $opt }}
                                </div>
                                @endforeach
                            </div>
                            <input type="hidden" class="qz-form-field" data-qid="{{ $q->id }}" id="hidChip_{{ $q->id }}" required>
                        @endif

                    @else
                        @php
                            $qLowerStr = strtolower($q->question);
                            $isH2 = str_contains($qLowerStr, 'height');
                            $isW2 = str_contains($qLowerStr, 'weight');
                        @endphp
                        <label class="qz-field-label">{{ $q->question }} <span style="color:#e84040">*</span></label>
                        <input type="text" class="qz-text-input qz-form-field" data-qid="{{ $q->id }}" 
                               placeholder="{{ $isH2 ? 'e.g. 115 cm or 4 ft 2 in' : ($isW2 ? 'e.g. 22 kg or 48 lbs' : '') }}" 
                               required>
                    @endif

                    <div class="qz-error" id="qzErr_{{ $q->id }}">&#9888;&#xFE0F; This field is required.</div>
                </div>
                @endforeach

                {{-- Navigation --}}
                <div class="qz-step-nav">
                    @if($step > 1)
                        <button class="qz-btn-back" type="button" onclick="qzGo({{ $step }}, {{ $step - 1 }})">&#8592; Back</button>
                    @else
                        <div></div>
                    @endif

                    @if($step < $stepCount)
                        <button class="qz-btn-next" type="button" onclick="qzGo({{ $step }}, {{ $step + 1 }})">Continue &#8594;</button>
                    @else
                        <button class="qz-btn-submit" type="button" id="qzSubmitBtn" onclick="qzSubmit(this)">
                            &#10024; Submit &amp; Pay
                        </button>
                    @endif
                </div>
            </div>
            @php $step++; @endphp
            @endforeach

        </div>{{-- /qz-main --}}
    </div>{{-- /qz-shell --}}
</div>

<script>
    window.isUserLoggedIn = @json(auth()->check());
    window.loginUrlBase = "{{ route('api.nutrition.questionnaire.checkout.auth') }}";
</script>
<script src="{{ asset('js/questionnaire.js') }}"></script>
@endsection
