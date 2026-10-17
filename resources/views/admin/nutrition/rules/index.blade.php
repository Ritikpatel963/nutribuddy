@extends('layouts.admin')

@php
    $title = 'Diet Generation Rules';
    $subTitle = 'Diet Generation Logic Implementation';
@endphp

@section('content')
<div class="row gy-4 mb-24">
    <div class="col-12">
        <div class="card p-4 bg-base border-0 radius-12 shadow-sm">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div class="w-48-px h-48-px rounded-circle bg-success-50 text-success-600 d-flex align-items-center justify-content-center text-2xl">
                    <iconify-icon icon="lucide:brain-circuit"></iconify-icon>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-dark">System Diet Logic Implementation</h5>
                    <p class="mb-0 text-secondary-light">Discover the intelligent rules and scoring mechanisms that power our automated diet chart generation.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row gy-4">
    <!-- Safety & Filtering Rules -->
    <div class="col-xxl-6 col-md-6">
        <div class="card p-3 p-xl-4 h-100 bg-base border-0 radius-12 shadow-sm">
            <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                <div class="w-40-px h-40-px rounded-circle bg-danger-50 text-danger-600 d-flex align-items-center justify-content-center text-xl">
                    <iconify-icon icon="lucide:shield-check"></iconify-icon>
                </div>
                <h6 class="mb-0 fw-semibold text-dark">Safety & Filtering Rules</h6>
            </div>
            <ul class="d-flex flex-column gap-3 mb-0 ps-0 list-unstyled">
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:check-circle-2" class="text-success-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Vegan:</strong> <span class="text-secondary-light">Excludes foods marked as containing dairy, egg, or honey.</span></div>
                </li>
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:check-circle-2" class="text-success-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Jain:</strong> <span class="text-secondary-light">Only includes foods flagged as Jain-friendly.</span></div>
                </li>
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:check-circle-2" class="text-success-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Eggetarian:</strong> <span class="text-secondary-light">Excludes meat, poultry, and fish.</span></div>
                </li>
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:check-circle-2" class="text-success-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Vegetarian:</strong> <span class="text-secondary-light">Excludes meat, poultry, fish, and eggs.</span></div>
                </li>
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:alert-triangle" class="text-danger-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Allergies:</strong> <span class="text-secondary-light">Strictly excludes foods containing specified allergens like dairy, nuts, gluten, or egg.</span></div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Slot & Timing Rules -->
    <div class="col-xxl-6 col-md-6">
        <div class="card p-3 p-xl-4 h-100 bg-base border-0 radius-12 shadow-sm">
            <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                <div class="w-40-px h-40-px rounded-circle bg-warning-50 text-warning-600 d-flex align-items-center justify-content-center text-xl">
                    <iconify-icon icon="lucide:clock"></iconify-icon>
                </div>
                <h6 class="mb-0 fw-semibold text-dark">Slot & Timing Rules</h6>
            </div>
            <ul class="d-flex flex-column gap-3 mb-0 ps-0 list-unstyled">
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:layout-list" class="text-primary-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Meal Slot Matching:</strong> <span class="text-secondary-light">Foods must be explicitly flagged as suitable for the target meal slot (e.g., Breakfast, Lunch, Snack, Dinner).</span></div>
                </li>
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:calendar-range" class="text-primary-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Meal Patterns:</strong> <span class="text-secondary-light">Distributes eating occasions seamlessly across standard "3 meals", "2 + snacks", and "5 meals" day structures.</span></div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Nutritional Targeting -->
    <div class="col-xxl-6 col-md-6">
        <div class="card p-3 p-xl-4 h-100 bg-base border-0 radius-12 shadow-sm">
            <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                <div class="w-40-px h-40-px rounded-circle bg-info-50 text-info-600 d-flex align-items-center justify-content-center text-xl">
                    <iconify-icon icon="lucide:pie-chart"></iconify-icon>
                </div>
                <h6 class="mb-0 fw-semibold text-dark">Nutritional Targeting</h6>
            </div>
            <ul class="d-flex flex-column gap-3 mb-0 ps-0 list-unstyled">
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:target" class="text-info-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Gap Filling:</strong> <span class="text-secondary-light">Continuously tracks cumulative daily nutrients (Energy, Protein, Iron, etc.) and prioritizes foods that effectively fill remaining gaps.</span></div>
                </li>
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:bar-chart-big" class="text-info-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Over-Nutrition Guard:</strong> <span class="text-secondary-light">Scoring penalizes foods that would drastically exceed daily limits in a single meal.</span></div>
                </li>
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:milk-off" class="text-info-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Dairy Cap:</strong> <span class="text-secondary-light">Automatically decreases the probability of selecting additional dairy items if the child's profile already indicates 2 or more dairy glasses consumed daily.</span></div>
                </li>
            </ul>
        </div>
    </div>

    <!-- Variety & Preferences -->
    <div class="col-xxl-6 col-md-6">
        <div class="card p-3 p-xl-4 h-100 bg-base border-0 radius-12 shadow-sm">
            <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                <div class="w-40-px h-40-px rounded-circle bg-primary-50 text-primary-600 d-flex align-items-center justify-content-center text-xl">
                    <iconify-icon icon="lucide:sparkles"></iconify-icon>
                </div>
                <h6 class="mb-0 fw-semibold text-dark">Variety & Preferences</h6>
            </div>
            <ul class="d-flex flex-column gap-3 mb-0 ps-0 list-unstyled">
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:history" class="text-primary-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Repetition Penalty:</strong> <span class="text-secondary-light">Foods have a defined repeat gap (e.g., 3 days). Generating the same food too soon applies a massive score penalty to force variety.</span></div>
                </li>
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:map-pin" class="text-primary-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Regional Bonus:</strong> <span class="text-secondary-light">Foods matching the family's preferred region (e.g., North Indian) receive a selection bonus.</span></div>
                </li>
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:thumbs-up" class="text-primary-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Kid Acceptance:</strong> <span class="text-secondary-light">Kid-acceptance ratings directly influence the probability of a food being picked.</span></div>
                </li>
                <li class="d-flex gap-2 align-items-start">
                    <iconify-icon icon="lucide:flame" class="text-primary-600 mt-1"></iconify-icon>
                    <div><strong class="text-dark">Cooking Medium:</strong> <span class="text-secondary-light">Extra selection priority for foods matching the family's designated cooking medium.</span></div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
