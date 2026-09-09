@extends('layout.layout')

@section('title', 'Diet Subscription Details - ' . $subscription->child->name)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Diet Subscription Details</h4>
        </div>
    </div>
</div>

<div class="row">
    <!-- User / Customer Info -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="header-title mb-3">Customer Information</h5>
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <div class="avatar-sm rounded bg-primary-light d-flex align-items-center justify-content-center text-primary fs-4 fw-bold">
                            {{ substr($subscription->user->name, 0, 1) }}
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mt-0 mb-1">{{ $subscription->user->name }}</h5>
                        <p class="text-muted mb-0">{{ $subscription->user->email }}</p>
                    </div>
                </div>
                <hr>
                <p><strong>Phone:</strong> {{ $subscription->user->phone ?? 'N/A' }}</p>
                <p><strong>Joined:</strong> {{ $subscription->user->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="header-title mb-3">Subscription Info</h5>
                <p><strong>Plan:</strong> {{ $subscription->subscriptionPlan->name ?? 'N/A' }}</p>
                <p><strong>Model:</strong> {{ $subscription->model }}</p>
                <p><strong>Status:</strong> 
                    <span class="badge bg-{{ $subscription->status == 'active' ? 'success' : ($subscription->status == 'pending' ? 'warning' : 'danger') }}">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </p>
                <p><strong>Payment Status:</strong> {{ ucfirst($subscription->payment_status) }}</p>
                <p><strong>Amount Paid:</strong> ₹{{ $subscription->amount_paid }}</p>
                <p><strong>Valid:</strong> {{ $subscription->starts_at->format('M d, Y') }} - {{ $subscription->expires_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Child Profile & Questionnaire -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="header-title mb-3">Child Profile</h5>
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="text-muted mt-0 mb-1">Name</h6>
                            <h4 class="mb-0">{{ $subscription->child->name }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="text-muted mt-0 mb-1">Age / DOB</h6>
                            <h4 class="mb-0">{{ $subscription->child->dob ? \Carbon\Carbon::parse($subscription->child->dob)->age . ' yrs' : 'N/A' }}</h4>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="border p-3 rounded bg-light">
                            <h6 class="text-muted mt-0 mb-1">Sex</h6>
                            <h4 class="mb-0">{{ ucfirst($subscription->child->sex) }}</h4>
                        </div>
                    </div>
                </div>

                <h5 class="header-title mb-3 mt-4">Assessment History & Answers</h5>
                
                @if($subscription->child->assessments->count() > 0)
                    <div class="accordion" id="assessmentAccordion">
                        @foreach($subscription->child->assessments as $assessment)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $assessment->id }}">
                                <button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $assessment->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                                    Assessment #{{ $assessment->id }} - Model: {{ $assessment->model_level }} ({{ $assessment->created_at->format('M d, Y') }})
                                </button>
                            </h2>
                            <div id="collapse{{ $assessment->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#assessmentAccordion">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50%">Question</th>
                                                    <th>Answer</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($assessment->answers as $ans)
                                                <tr>
                                                    <td>{{ $ans->question->question ?? 'Unknown Question' }}</td>
                                                    <td class="fw-bold">{{ is_array($ans->answer_value) ? implode(', ', $ans->answer_value) : $ans->answer_value }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No assessments found for this child.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
