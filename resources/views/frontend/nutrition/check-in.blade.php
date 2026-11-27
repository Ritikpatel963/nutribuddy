@extends('layouts.user-panel')
@section('title', 'Quarterly Check-in — NutriBuddy Kids')
@section('panel-page-class', 'panel-check-in')

@section('panel-content')

<div class="ci-page">

    {{-- ── Page Header ── --}}
    <div class="ci-page-head ci-anim-1">
        <h1 class="ci-title">Quarterly Check-in</h1>
        @if($child)
            <p class="ci-subtitle">Record {{ $child->name }}'s latest measurements and quarterly updates.</p>
        @else
            <p class="ci-subtitle">Create a child profile before recording a check-in.</p>
        @endif
    </div>

    {{-- ── Main Form Card ── --}}
    @if($child && $latestAssessment)
        @if($children->count() > 1)
            <form method="GET" action="{{ route('check-in') }}" class="ci-child-picker">
                <label class="ci-label" for="check-in-child">CHILD PROFILE</label>
                <select class="ci-input ci-select" id="check-in-child" name="child_id" onchange="this.form.submit()">
                    @foreach($children as $availableChild)
                        <option value="{{ $availableChild->id }}" @selected($availableChild->id === $child->id)>{{ $availableChild->name }}</option>
                    @endforeach
                </select>
            </form>
        @endif

    <form class="ci-form ci-anim-2" id="checkInForm" method="POST" action="{{ route('nutrition.check-in') }}" onsubmit="handleCheckinSubmit(event)">
        @csrf
        <input type="hidden" name="child_id" value="{{ $child->id }}">
        <div class="ci-form-error" id="ci-form-error" role="alert" hidden></div>

        {{-- Row 1: Weight + Height --}}
        <div class="ci-form-row ci-two-col">
            <div class="ci-field-group">
                <label class="ci-label" for="ci-weight">CURRENT WEIGHT (KG)</label>
                <input class="ci-input" id="ci-weight" name="weight_kg" type="number" step="0.1" min="5" max="100" required
                        value="{{ old('weight_kg', $latestAssessment->current_weight_kg) }}" placeholder="e.g. 18">
            </div>
            <div class="ci-field-group">
                <label class="ci-label" for="ci-height">CURRENT HEIGHT (CM)</label>
                <input class="ci-input" id="ci-height" name="height_cm" type="number" step="0.5" min="50" max="200" required
                        value="{{ old('height_cm', $latestAssessment->current_height_cm) }}" placeholder="e.g. 106">
            </div>
        </div>

        {{-- Times Sick --}}
        <div class="ci-field-group">
            <label class="ci-label" for="ci-sick">TIMES SICK LAST 3 MONTHS</label>
            <select class="ci-input ci-select" id="ci-sick" name="illness_count" required>
                <option value="" disabled @selected(!$latestCheckIn)>Select number of times</option>
                @foreach([0, 1, 2, 3, 4] as $illnessCount)
                    <option value="{{ $illnessCount }}" @selected((int) old('illness_count', $latestCheckIn?->illness_count) === $illnessCount)>{{ $illnessCount === 0 ? '0 times' : ($illnessCount === 4 ? '4+ times' : $illnessCount . ($illnessCount === 1 ? ' time' : ' times')) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Focus & School Performance --}}
        <div class="ci-field-group">
            <label class="ci-label" for="ci-focus">FOCUS &amp; SCHOOL PERFORMANCE</label>
            <select class="ci-input ci-select" id="ci-focus" name="focus_level" required>
                <option value="" disabled @selected(!$latestCheckIn)>Choose an update</option>
                <option value="much-better" @selected(old('focus_level', $latestCheckIn?->focus_level) === 'much-better')>Much better</option>
                <option value="slightly-better" @selected(old('focus_level', $latestCheckIn?->focus_level) === 'slightly-better')>Slightly better</option>
                <option value="same" @selected(old('focus_level', $latestCheckIn?->focus_level) === 'same')>About the same</option>
                <option value="slightly-worse" @selected(old('focus_level', $latestCheckIn?->focus_level) === 'slightly-worse')>Slightly worse</option>
                <option value="much-worse" @selected(old('focus_level', $latestCheckIn?->focus_level) === 'much-worse')>Much worse</option>
            </select>
        </div>

        {{-- Energy Level --}}
        <div class="ci-field-group">
            <label class="ci-label" for="ci-energy">ENERGY LEVEL</label>
            <select class="ci-input ci-select" id="ci-energy" name="energy_level" required>
                <option value="" disabled @selected(!$latestCheckIn)>Choose an update</option>
                <option value="very-high" @selected(old('energy_level', $latestCheckIn?->energy_level) === 'very-high')>Very high</option>
                <option value="high" @selected(old('energy_level', $latestCheckIn?->energy_level) === 'high')>High energy</option>
                <option value="same" @selected(old('energy_level', $latestCheckIn?->energy_level) === 'same')>About the same</option>
                <option value="low" @selected(old('energy_level', $latestCheckIn?->energy_level) === 'low')>Lower than before</option>
                <option value="very-low" @selected(old('energy_level', $latestCheckIn?->energy_level) === 'very-low')>Tired often</option>
            </select>
        </div>

        {{-- New Life Events --}}
        <div class="ci-field-group">
            <label class="ci-label">NEW LIFE EVENTS?</label>
            <div class="ci-chips" id="ci-chips">
                @php
                    $selectedEvents = old('life_events', $latestCheckIn?->life_events ?? ['nothing']);
                @endphp
                <button type="button" class="ci-chip {{ in_array('nothing', $selectedEvents, true) ? 'ci-chip-active' : '' }}" data-val="nothing" aria-pressed="{{ in_array('nothing', $selectedEvents, true) ? 'true' : 'false' }}">Nothing major</button>
                <button type="button" class="ci-chip {{ in_array('sport', $selectedEvents, true) ? 'ci-chip-active' : '' }}" data-val="sport" aria-pressed="{{ in_array('sport', $selectedEvents, true) ? 'true' : 'false' }}">Joined sports</button>
                <button type="button" class="ci-chip {{ in_array('exams', $selectedEvents, true) ? 'ci-chip-active' : '' }}" data-val="exams" aria-pressed="{{ in_array('exams', $selectedEvents, true) ? 'true' : 'false' }}">Exams coming</button>
                <button type="button" class="ci-chip {{ in_array('illness', $selectedEvents, true) ? 'ci-chip-active' : '' }}" data-val="illness" aria-pressed="{{ in_array('illness', $selectedEvents, true) ? 'true' : 'false' }}">Illness recovery</button>
                <button type="button" class="ci-chip {{ in_array('school', $selectedEvents, true) ? 'ci-chip-active' : '' }}" data-val="school" aria-pressed="{{ in_array('school', $selectedEvents, true) ? 'true' : 'false' }}">Changed school</button>
                <button type="button" class="ci-chip {{ in_array('travel', $selectedEvents, true) ? 'ci-chip-active' : '' }}" data-val="travel" aria-pressed="{{ in_array('travel', $selectedEvents, true) ? 'true' : 'false' }}">Travelled / relocated</button>
                <button type="button" class="ci-chip {{ in_array('birthday', $selectedEvents, true) ? 'ci-chip-active' : '' }}" data-val="birthday" aria-pressed="{{ in_array('birthday', $selectedEvents, true) ? 'true' : 'false' }}">Birthday this quarter</button>
            </div>
        </div>

        {{-- Food Changes --}}
        <div class="ci-field-group">
            <label class="ci-label" for="ci-food">FOOD CHANGES (OPTIONAL)</label>
            <textarea class="ci-input ci-textarea" id="ci-food" name="food_changes" rows="4" maxlength="2000"
                placeholder="Describe any recent changes in your child's diet or food preferences.">{{ old('food_changes', $latestCheckIn?->food_changes) }}</textarea>
        </div>

        {{-- What happens next info box --}}
        <div class="ci-info-box ci-anim-3">
            <div class="ci-info-head">
                <span class="ci-info-dot"></span>
                What happens next
            </div>
            <p class="ci-info-body">
                Your measurements and answers will be saved to this child's progress history. Weight, height and BMI are added to the assessment timeline.
            </p>
        </div>

        {{-- Submit CTA --}}
        <div class="ci-submit-wrap">
            <button type="submit" class="ci-submit-btn" id="ci-submit">
                <span class="ci-btn-text">Save Quarterly Check-in</span>
                <span class="ci-btn-spinner" aria-hidden="true"></span>
            </button>
        </div>

    </form>

    {{-- ── Success State (hidden by default) ── --}}
    <div class="ci-success ci-anim-4" id="ci-success" style="display:none">
        <div class="ci-success-icon">✓</div>
        <div class="ci-success-title">Check-in saved</div>
        <p class="ci-success-body">{{ $child->name }}'s measurements and answers are now in the progress history.</p>
        <div class="ci-success-actions">
            <a href="{{ route('growth-signal') }}" class="ci-goto-btn">View Health Scores →</a>
            <a href="{{ route('meal-plan') }}" class="ci-goto-btn ci-goto-outline">View Meal Plan →</a>
        </div>
    </div>

    @if($checkIns->isNotEmpty())
        <section class="ci-history-card" aria-labelledby="ci-history-title">
            <div class="ci-history-head">
                <div>
                    <h2 id="ci-history-title">{{ $child->name }}'s Progress History</h2>
                    <p>Measurements compared with the first recorded assessment.</p>
                </div>
            </div>
            <div class="ci-history-scroll">
                <table class="ci-history-table">
                    <thead>
                        <tr>
                            <th>Date</th><th>Weight</th><th>Change</th><th>Height</th><th>Change</th>
                            <th>Illnesses</th><th>Focus / Energy</th><th>Life events / food notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($checkIns as $checkIn)
                            @php
                                $weightChange = $checkIn->assessment?->initial_weight_kg !== null
                                    ? $checkIn->weight_kg - $checkIn->assessment->initial_weight_kg
                                    : null;
                                $heightChange = $checkIn->assessment?->initial_height_cm !== null
                                    ? $checkIn->height_cm - $checkIn->assessment->initial_height_cm
                                    : null;
                                $eventLabels = collect($checkIn->life_events ?? [])->map(fn ($event) => $event === 'nothing' ? 'Nothing major' : \Illuminate\Support\Str::headline($event));
                            @endphp
                            <tr>
                                <td>{{ $checkIn->created_at?->format('d M Y') ?? '—' }}</td>
                                <td>{{ number_format($checkIn->weight_kg, 1) }} kg</td>
                                <td>{{ $weightChange === null ? '—' : sprintf('%+.1f kg', $weightChange) }}</td>
                                <td>{{ number_format($checkIn->height_cm, 1) }} cm</td>
                                <td>{{ $heightChange === null ? '—' : sprintf('%+.1f cm', $heightChange) }}</td>
                                <td>{{ $checkIn->illness_count }}{{ $checkIn->illness_count >= 4 ? '+' : '' }}</td>
                                <td>{{ \Illuminate\Support\Str::headline($checkIn->focus_level) }} / {{ \Illuminate\Support\Str::headline($checkIn->energy_level) }}</td>
                                <td>
                                    {{ $eventLabels->join(', ') ?: '—' }}
                                    @if($checkIn->food_changes)<div class="ci-history-note">{{ $checkIn->food_changes }}</div>@endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
    @else
        <div class="ci-empty-state">
            <h2>No child assessment found</h2>
            <p>Create a child profile and complete an assessment before recording a quarterly check-in.</p>
            <a class="ci-goto-btn" href="{{ route('child-profile.create') }}">Create Child Profile</a>
        </div>
    @endif

</div>{{-- /ci-page --}}

@push('scripts')
<script>
/* ── Sidebar helpers ── */
/* ── Multi-select chips ── */
const chips = document.querySelectorAll('#ci-chips .ci-chip');
chips.forEach(chip => {
    chip.addEventListener('click', () => {
        const val = chip.dataset.val;

        if (val === 'nothing') {
            /* "Nothing major" deselects all others */
            chips.forEach(c => {
                c.classList.remove('ci-chip-active');
                c.setAttribute('aria-pressed', 'false');
            });
            chip.classList.add('ci-chip-active');
            chip.setAttribute('aria-pressed', 'true');
        } else {
            /* Remove "Nothing major" if something else selected */
            const nothingChip = document.querySelector('[data-val="nothing"]');
            nothingChip.classList.remove('ci-chip-active');
            nothingChip.setAttribute('aria-pressed', 'false');
            chip.classList.toggle('ci-chip-active');
            chip.setAttribute('aria-pressed', chip.classList.contains('ci-chip-active') ? 'true' : 'false');

            /* If nothing left selected, re-activate "Nothing major" */
            const anyActive = [...chips].some(c => c.classList.contains('ci-chip-active'));
            if (!anyActive) {
                nothingChip.classList.add('ci-chip-active');
                nothingChip.setAttribute('aria-pressed', 'true');
            }
        }
    });
});

/* ── Form submit ── */
function handleCheckinSubmit(e) {
    e.preventDefault();

    const btn = document.getElementById('ci-submit');
    const form = document.getElementById('checkInForm');
    const success = document.getElementById('ci-success');
    const error = document.getElementById('ci-form-error');
    const events = [...form.querySelectorAll('.ci-chip-active')].map(chip => chip.dataset.val);

    btn.classList.add('ci-loading');
    btn.disabled = true;
    error.hidden = true;

    fetch(form.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': form.querySelector('[name="_token"]').value,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            child_id: form.elements.child_id.value,
            weight_kg: form.elements.weight_kg.value,
            height_cm: form.elements.height_cm.value,
            illness_count: form.elements.illness_count.value,
            focus_level: form.elements.focus_level.value,
            energy_level: form.elements.energy_level.value,
            life_events: events,
            food_changes: form.elements.food_changes.value,
        }),
    }).then(async response => {
        const data = await response.json();
        if (!response.ok || !data.success) {
            const validationMessage = Object.values(data.errors || {}).flat()[0];
            throw new Error(validationMessage || data.message || 'Unable to save this check-in. Please try again.');
        }

        form.style.display = 'none';
        success.style.display = 'block';
        success.classList.remove('ci-anim-4');
        void success.offsetWidth;
        success.classList.add('ci-anim-4');
        window.setTimeout(() => window.location.reload(), 900);
    }).catch(saveError => {
        error.textContent = saveError.message;
        error.hidden = false;
    }).finally(() => {
        btn.classList.remove('ci-loading');
        btn.disabled = false;
    });
}
</script>
@endpush

@endsection
