@extends('layouts.main')

@section('content')
    @php
        /*
         * $formattedPlan is the canonical list produced by DietChartDisplayService::formatPlan().
         * Each entry: ['index','label','date','week','meals']
         * $sevenDayPlan keeps old callers working (day-name keyed array).
         *
         * For display we use $formattedPlan. When absent (very old cached view calls),
         * fall back to the old $sevenDayPlan shape rebuilt as a single-week list.
         */
        $planDays   = $formattedPlan ?? [];
        $totalDays  = count($planDays);
        $weekCount  = $totalDays > 0 ? (intdiv($totalDays - 1, 7) + 1) : 1;
        $multiWeek  = $weekCount > 1;

        $allSlots = ['Breakfast', 'Morning Snack', 'Lunch', 'Evening Snack', 'Snack', 'Dinner'];
        $slotTimes = [
            'Breakfast'     => '7 AM',
            'Morning Snack' => '10 AM',
            'Lunch'         => '12 PM',
            'Evening Snack' => '4 PM',
            'Snack'         => '4 PM',
            'Dinner'        => '7 PM',
        ];
    @endphp
    <div class="diet-chart-container">
        {{-- HERO --}}
        <div class="diet-chart-hero">
            <div class="hero-content">
                @if($hasPlan && $totalDays > 0)
                    <h1>{{ $totalDays }}-Day Personalized Meal Plan</h1>
                @else
                    <h1>Personalized Meal Plan</h1>
                @endif
                <p>A personalized food rhythm for {{ $child->name }}.</p>
            </div>
            <div class="hero-stats">
                @if($hasPlan && $totalDays > 0)
                    <div class="stat-card">
                        <span class="stat-value">{{ $totalDays }}</span>
                        <span class="stat-label">Days</span>
                    </div>
                @endif
                @if(!empty($healthScore) && \App\Support\PlanAccess::allows($planSlug, 'health_score'))
                    <div class="stat-card health-score-card" style="cursor: pointer; background: rgba(76, 175, 80, 0.2); border: 2px solid #4CAF50;" onclick="openHealthScoreModal()" title="Click to view health score breakdown">
                        <span class="stat-value" style="color: #4CAF50;">{{ $healthScore['total'] }}/100</span>
                        <span class="stat-label">Health Score ℹ️</span>
                    </div>
                @endif
                <div class="stat-card active">
                    <span class="stat-value">{{ ucfirst($planSlug ?? 'Basic') }}</span>
                    <span class="stat-label">Plan Tier</span>
                </div>
            </div>
        </div>

        @if($hasPlan)
            {{-- MEAL PLAN SECTION --}}
            <div class="meal-plan-section">
                <div class="plan-header" style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 16px;">
                    <div>
                        <h2>Meal Plan</h2>
                        <div class="plan-meta">
                            <span class="child-name">{{ $child->name }}</span>
                            <span class="child-age">{{ $child->age }} years</span>
                            <span class="diet-type">{{ ucfirst($assessment->diet_type ?? 'N/A') }} Diet</span>
                            <span class="region">{{ ucfirst($assessment->region ?? 'N/A') }}</span>
                        </div>
                    </div>
                    <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                        <div style="position:relative; display:inline-block;" id="downloadPdfDropdownWrapTop">
                            <button type="button" class="btn btn-primary" onclick="togglePdfDownloadMenuTop(event)" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; padding: 10px 18px; font-size: 13px; border:none; cursor:pointer;">
                                📥 Download PDF <span style="font-size:10px; margin-left:2px;">▼</span>
                            </button>
                            <div id="pdfDownloadDropdownTop" style="display:none; position:absolute; right:0; top:calc(100% + 6px); background:white; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.18); border:1px solid #e0e0e0; min-width:270px; z-index:99999; padding:8px 0; text-align:left;">
                                <a id="pdfDownloadActiveDayLinkTop" href="{{ route('child.diet-chart.download-pdf', ['child' => $child->id, 'day_index' => 0]) }}" style="display:flex; flex-direction:column; padding:10px 16px; text-decoration:none; color:#333; border-bottom:1px solid #f0f0f0; transition:background 0.15s;" onmouseover="this.style.background='#F3E8FF'" onmouseout="this.style.background='white'">
                                    <span style="font-weight:700; font-size:13px; color:#7B2CBF;">📄 Download Selected Day</span>
                                    <span id="pdfActiveDayLabelTop" style="font-size:11px; color:#666; margin-top:2px;">Day 1</span>
                                </a>
                                @if(count($weekGroups ?? []) > 1)
                                <a id="pdfDownloadActiveWeekLinkTop" href="{{ route('child.diet-chart.download-pdf', ['child' => $child->id, 'week' => 1]) }}" style="display:flex; flex-direction:column; padding:10px 16px; text-decoration:none; color:#333; border-bottom:1px solid #f0f0f0; transition:background 0.15s;" onmouseover="this.style.background='#F3E8FF'" onmouseout="this.style.background='white'">
                                    <span style="font-weight:700; font-size:13px; color:#7B2CBF;">🗓️ Download Active Week</span>
                                    <span id="pdfActiveWeekLabelTop" style="font-size:11px; color:#666; margin-top:2px;">Week 1</span>
                                </a>
                                @endif
                                <a href="{{ route('child.diet-chart.download-pdf', ['child' => $child->id, 'scope' => 'all']) }}" style="display:flex; flex-direction:column; padding:10px 16px; text-decoration:none; color:#333; transition:background 0.15s;" onmouseover="this.style.background='#F3E8FF'" onmouseout="this.style.background='white'">
                                    <span style="font-weight:700; font-size:13px; color:#2E7D32;">📑 Download Complete Plan</span>
                                    <span style="font-size:11px; color:#666; margin-top:2px;">All {{ count($formattedPlan ?? []) }} Days (Full Chart)</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                @if(filled($dietPlan->review_notes))
                    <aside class="nutritionist-remark" aria-label="Note from your nutritionist">
                        <div class="nutritionist-remark-icon"><i class="ri-chat-quote-line" aria-hidden="true"></i></div>
                        <div>
                            <span class="nutritionist-remark-label">A note from your nutritionist</span>
                            <p>{{ $dietPlan->review_notes }}</p>
                            @if($dietPlan->reviewed_at)
                                <time datetime="{{ $dietPlan->reviewed_at->toIso8601String() }}">Reviewed {{ $dietPlan->reviewed_at->format('d M Y') }}</time>
                            @endif
                        </div>
                    </aside>
                @endif

                {{-- WEEK TABS — shown only when the plan spans more than one week --}}
                @if($multiWeek)
                    <div class="week-tabs" role="tablist" aria-label="Week navigation">
                        @for($w = 1; $w <= $weekCount; $w++)
                            <button
                                class="week-tab {{ $w === 1 ? 'active' : '' }}"
                                data-week="{{ $w }}"
                                role="tab"
                                aria-selected="{{ $w === 1 ? 'true' : 'false' }}"
                                aria-controls="week-panel-{{ $w }}"
                            >Week {{ $w }}</button>
                        @endfor
                    </div>
                @endif

                {{-- One panel per week --}}
                @for($w = 1; $w <= $weekCount; $w++)
                    @php $weekDays = array_filter($planDays, fn($d) => $d['week'] === $w); @endphp
                    <div
                        class="week-panel"
                        id="week-panel-{{ $w }}"
                        role="tabpanel"
                        aria-labelledby="week-tab-{{ $w }}"
                        style="{{ $w === 1 ? '' : 'display:none;' }}"
                    >
                        {{-- DAY TABS --}}
                        <div class="week-days-nav" role="tablist" aria-label="Day navigation week {{ $w }}">
                            @foreach($weekDays as $dayData)
                                @php $isFirst = ($dayData['index'] === array_values($weekDays)[0]['index']); @endphp
                                <button
                                    class="day-tab {{ $isFirst ? 'active' : '' }}"
                                    data-day-id="day-{{ $dayData['index'] }}"
                                    data-day-index="{{ $dayData['index'] }}"
                                    data-day-label="{{ $dayData['label'] }}, {{ \Carbon\Carbon::parse($dayData['date'])->format('d M') }}"
                                    data-week="{{ $w }}"
                                    role="tab"
                                    aria-selected="{{ $isFirst ? 'true' : 'false' }}"
                                    aria-controls="day-{{ $dayData['index'] }}"
                                >
                                    <span class="day-label">{{ strtoupper(substr($dayData['label'], 0, 3)) }}</span>
                                    <span class="day-date">{{ \Carbon\Carbon::parse($dayData['date'])->format('d M') }}</span>
                                </button>
                            @endforeach
                        </div>

                        {{-- DAY PANELS --}}
                        @foreach($weekDays as $dayData)
                            @php $isFirst = ($dayData['index'] === array_values($weekDays)[0]['index']); @endphp
                            <div
                                class="day-meals"
                                id="day-{{ $dayData['index'] }}"
                                style="{{ $isFirst ? '' : 'display:none;' }}"
                                role="tabpanel"
                            >
                                <div class="day-title-section">
                                    <h3>{{ $dayData['label'] }}, {{ \Carbon\Carbon::parse($dayData['date'])->format('d M Y') }}</h3>
                                </div>

                                @foreach($allSlots as $slot)
                                    @if(isset($dayData['meals'][$slot]))
                                        @php $mealData = $dayData['meals'][$slot]; @endphp
                                        <div class="meal-slot">
                                            <div class="meal-time">{{ $slotTimes[$slot] ?? '' }}</div>
                                            <div class="meal-content">
                                                <div class="slot-name">{{ $slot }}</div>
                                                @foreach($mealData['items'] as $item)
                                                    <div class="meal-item">
                                                        <div class="meal-dish">
                                                            <h4>{{ $item['food_name'] ?? 'N/A' }}</h4>
                                                            @if(!empty($item['regional_name']))
                                                                <p class="regional-name">{{ $item['regional_name'] }}</p>
                                                            @endif
                                                            <p class="meal-portion">{{ $item['portion_g'] ?? 'N/A' }}g serving</p>
                                                            @if(!empty($item['cook_min']))
                                                                <span class="cook-time-badge">⏱️ {{ $item['cook_min'] }} mins</span>
                                                            @endif
                                                            <div class="recipe-btn-wrapper">
                                                                @if(\App\Support\PlanAccess::allows($planSlug, 'full_recipes'))
                                                                    <button type="button" class="btn-recipe-action"
                                                                        data-name="{{ $item['food_name'] ?? '' }}"
                                                                        data-cook="{{ $item['cook_min'] ?? 15 }}"
                                                                        data-portion="{{ $item['portion_g'] ?? '' }}"
                                                                        data-ingredients="{{ $item['ingredients'] ?? '' }}"
                                                                        data-method="{{ $item['method'] ?? '' }}"
                                                                        onclick="showRecipe(this)">
                                                                        👨‍🍳 View Recipe
                                                                    </button>
                                                                @else
                                                                    <button type="button" class="btn-recipe-action locked" onclick="alert('Step-by-step recipes are available on Pro and Premium plans!')">
                                                                        🔒 Recipe (Pro/Premium)
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="meal-nutrients">
                                                            <div class="nutrient">
                                                                <span class="n-value">{{ number_format($item['nutrients']['energy_kcal'] ?? 0, 0) }}</span>
                                                                <span class="n-label">kcal</span>
                                                            </div>
                                                            <div class="nutrient">
                                                                <span class="n-value">{{ number_format($item['nutrients']['protein_g'] ?? 0, 1) }}</span>
                                                                <span class="n-label">g protein</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if(!empty($item['pairing_notes']))
                                                        <div class="pairing-note">
                                                            <span class="note-icon">💡</span>
                                                            <span class="note-text">
                                                                @foreach($item['pairing_notes'] as $note)
                                                                    {{ $note }}<br>
                                                                @endforeach
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endforeach

                                                @if(isset($mealData['total_nutrients']))
                                                    <div class="meal-total">
                                                        <div class="total-label">Slot Total</div>
                                                        <div class="total-values">
                                                            <div class="total-item">
                                                                <span class="label">Energy</span>
                                                                <span class="value">{{ number_format($mealData['total_nutrients']['energy_kcal'] ?? 0, 0) }} kcal</span>
                                                            </div>
                                                            <div class="total-item">
                                                                <span class="label">Protein</span>
                                                                <span class="value">{{ number_format($mealData['total_nutrients']['protein_g'] ?? 0, 1) }}g</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endfor
            </div>

            {{-- NUTRIENT GAPS SECTION --}}
            <div class="nutrient-gaps-section">
                <h2>Detected Nutrient Gaps</h2>
                <div class="gaps-grid">
                    @foreach($nutrientGaps as $nutrient => $gap)
                        @php
                            $catClass = strtolower(str_replace(' ', '-', $gap['gap_category'] ?? ''));
                        @endphp
                        <div class="gap-card gap-{{ $catClass }}">
                            <div class="gap-header">
                                <h4>{{ ucfirst(str_replace('_', ' ', $nutrient)) }}</h4>
                                <span class="gap-badge">{{ $gap['gap_category'] ?? 'Unknown' }}</span>
                            </div>
                            <div class="gap-details">
                                <div class="gap-info">
                                    <span class="info-label">Target</span>
                                    <span class="info-value">{{ number_format($gap['target'] ?? 0, 2) }}</span>
                                </div>
                                <div class="gap-info">
                                    <span class="info-label">Achieved</span>
                                    <span class="info-value">{{ number_format($gap['absorbed_amount'] ?? 0, 2) }}</span>
                                </div>
                                <div class="gap-info">
                                    <span class="info-label">Adequacy</span>
                                    <span class="info-value">{{ number_format($gap['adequacy_percent'] ?? 0, 1) }}%</span>
                                </div>
                            </div>
                            <div class="gap-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: {{ min($gap['adequacy_percent'] ?? 0, 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- CLINICAL TIPS --}}
            @if(!empty($clinicalTips))
                <div class="clinical-tips-section">
                    <h2>Nutrition Tips for {{ $child->name }}</h2>
                    <div class="tips-grid">
                        @foreach($clinicalTips as $tip)
                            <div class="tip-card">
                                <div class="tip-header">
                                    <h4>{{ $tip['benefit'] ?? 'Nutrition Tip' }}</h4>
                                </div>
                                <div class="tip-combination">
                                    <strong>Combination:</strong> {{ $tip['combination'] ?? 'N/A' }}
                                </div>
                                @if(!empty($tip['science_reason']))
                                    <div class="tip-science">
                                        <strong>Why it works:</strong> {{ $tip['science_reason'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ACTION BUTTONS --}}
            <div class="plan-actions">
                <a href="{{ route('child.diet-chart.download-pdf', $child->id) }}" class="btn btn-primary" id="downloadPdf">📥 Download Plan as PDF</a>
                @if(!empty($shoppingList) && \App\Support\PlanAccess::allows($planSlug, 'shopping_list'))
                    <button type="button" class="btn btn-success" style="background:#2E7D32; color:white;" onclick="openShoppingListModal()">🛒 Weekly Shopping List</button>
                @endif
                @if(\App\Support\PlanAccess::allows($planSlug, 'nutritionist_chat'))
                    <a href="https://wa.me/919999999999?text=Hello%20NutriBuddy%20Nutritionist,%20I%20have%20a%20question%20about%20{{ urlencode($child->name) }}%27s%20Premium%20diet%20chart" target="_blank" class="btn btn-whatsapp" style="background:#25D366; color:white;">💬 1-on-1 Nutritionist Chat</a>
                @endif
                <button class="btn btn-secondary" id="sharePlan">🔗 Share Plan</button>
                <button class="btn btn-warning" id="updatePlan">♻️ Regenerate Plan</button>
            </div>

            {{-- RECIPE MODAL --}}
            <div id="recipeModal" class="nutri-modal-overlay" style="display:none;" onclick="if(event.target === this) closeRecipeModal()">
                <div class="nutri-modal-dialog">
                    <div class="nutri-modal-header">
                        <div>
                            <h3 id="recipeTitle" style="margin:0 0 4px; font-size:20px; font-weight:700; color:#333;">Dish Name</h3>
                            <div style="display:flex; gap:12px; font-size:12px; color:#666;">
                                <span id="recipePortion"></span>
                                <span id="recipeCook"></span>
                            </div>
                        </div>
                        <button type="button" class="nutri-modal-close" onclick="closeRecipeModal()">&times;</button>
                    </div>
                    <div class="nutri-modal-body">
                        <div style="margin-bottom:20px;">
                            <h4 style="font-size:13px; font-weight:700; color:#7B2CBF; text-transform:uppercase; margin-bottom:8px; letter-spacing:0.05em;">🛒 Ingredients per Serving</h4>
                            <ul id="recipeIngredientsList" style="margin:0; padding-left:20px; font-size:13px; color:#444; line-height:1.6;"></ul>
                        </div>
                        <div>
                            <h4 style="font-size:13px; font-weight:700; color:#7B2CBF; text-transform:uppercase; margin-bottom:8px; letter-spacing:0.05em;">👩‍🍳 Preparation & Cooking Method</h4>
                            <div id="recipeMethodContent" style="font-size:13px; color:#444; line-height:1.6; background:#f9f9f9; padding:14px 16px; border-radius:10px; border-left:4px solid #7B2CBF;"></div>
                        </div>
                    </div>
                    <div class="nutri-modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeRecipeModal()">Close</button>
                    </div>
                </div>
            </div>

            {{-- SHOPPING LIST MODAL --}}
            @if(!empty($shoppingList) || !empty($shoppingListsData))
            <div id="shoppingListModal" class="nutri-modal-overlay" style="display:none;" onclick="if(event.target === this) closeShoppingListModal()">
                <div class="nutri-modal-dialog" style="max-width:680px; width:100%;">
                    <div class="nutri-modal-header" style="flex-direction:column; align-items:stretch; gap:12px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div>
                                <h3 style="margin:0 0 4px; font-size:20px; font-weight:700; color:#333;">🛒 Grocery Shopping List</h3>
                                <p id="shoppingModalSubtitle" style="margin:0; font-size:12px; color:#666;">Categorized ingredients for {{ $child->name }}'s active plan</p>
                            </div>
                            <button type="button" class="nutri-modal-close" onclick="closeShoppingListModal()">&times;</button>
                        </div>
                        <!-- Scope Switcher Tabs -->
                        <div style="display:flex; gap:8px; flex-wrap:wrap;">
                            <button type="button" class="shopping-scope-tab active" data-scope="day" onclick="switchShoppingScope('day')" style="background:#2E7D32; color:white; border:none; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:600; cursor:pointer; transition:all 0.15s;">
                                📅 Selected Day (<span id="shoppingSelectedDayTitle">Day 1</span>)
                            </button>
                            <button type="button" class="shopping-scope-tab" data-scope="week" onclick="switchShoppingScope('week')" style="background:#f0f0f0; color:#555; border:none; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:600; cursor:pointer; transition:all 0.15s;">
                                🗓️ Active Week (<span id="shoppingSelectedWeekTitle">Week 1</span>)
                            </button>
                            <button type="button" class="shopping-scope-tab" data-scope="full" onclick="switchShoppingScope('full')" style="background:#f0f0f0; color:#555; border:none; border-radius:8px; padding:6px 14px; font-size:12px; font-weight:600; cursor:pointer; transition:all 0.15s;">
                                📦 Full Plan (<span id="shoppingTotalDaysTitle">{{ count($formattedPlan ?? []) }} Days</span>)
                            </button>
                        </div>
                    </div>
                    <div class="nutri-modal-body" style="padding:20px 24px; max-height:60vh; overflow-y:auto;">
                        <div id="shoppingListText" style="display:none;"></div>
                        <div id="shoppingListGrid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(260px, 1fr)); gap:16px;">
                            <!-- Populated dynamically by renderShoppingListModal() -->
                        </div>
                    </div>
                    <div class="nutri-modal-footer" style="justify-content:space-between;">
                        <button type="button" class="btn btn-primary" onclick="copyShoppingList()" style="background:#7B2CBF; border:none; display:inline-flex; align-items:center; gap:6px;">📋 Copy List</button>
                        <button type="button" class="btn btn-secondary" onclick="closeShoppingListModal()">Close</button>
                    </div>
                </div>
            </div>
            @endif

            {{-- HEALTH SCORE MODAL --}}
            @if(!empty($healthScore))
            <div id="healthScoreModal" class="nutri-modal-overlay" style="display:none;" onclick="if(event.target === this) closeHealthScoreModal()">
                <div class="nutri-modal-dialog">
                    <div class="nutri-modal-header">
                        <div>
                            <h3 style="margin:0 0 4px; font-size:20px; font-weight:700; color:#333;">Child Health Scorecard</h3>
                            <p style="margin:0; font-size:12px; color:#666;">Comprehensive 6-area assessment for {{ $child->name }}</p>
                        </div>
                        <button type="button" class="nutri-modal-close" onclick="closeHealthScoreModal()">&times;</button>
                    </div>
                    <div class="nutri-modal-body">
                        <div class="score-summary-banner">
                            <div class="score-number">{{ $healthScore['total'] }}<span style="font-size:16px; color:#888;">/100</span></div>
                            <div class="score-meta">
                                <span class="score-grade-badge">{{ $healthScore['grade'] }}</span>
                                <p style="margin:4px 0 0; font-size:12px; color:#666;">Calibrated against ICMR-NIN 2024 Pediatric Guidelines</p>
                            </div>
                        </div>

                        <div class="score-breakdown-list">
                            @foreach($healthScore['breakdown'] as $area => $data)
                                <div class="score-area-item">
                                    <div class="area-header">
                                        <span class="area-name">{{ $area }}</span>
                                        <span class="area-points">{{ $data['score'] }} / {{ $data['max'] }} pts</span>
                                    </div>
                                    <div class="area-progress">
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: {{ ($data['score'] / $data['max']) * 100 }}%;"></div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="nutri-modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeHealthScoreModal()">Close</button>
                    </div>
                </div>
            </div>
            @endif

        @else
            {{-- NO PLAN --}}
            <div class="no-plan-container">
                <div class="no-plan-icon">
                    <img src="{{ asset('img/no-plan.svg') }}" alt="No plan">
                </div>
                <h2>No Active Diet Plan</h2>
                <p>{{ $child->name }} doesn't have an active meal plan yet.</p>

                @if($purchasedPlans > 0)
                    <div class="plan-actions">
                        <button class="btn btn-primary" id="generateNewPlan">
                            ✨ Generate New Personalized Plan
                        </button>
                        <p class="info-text">You have {{ $purchasedPlans }} plan(s) remaining</p>
                    </div>
                @else
                    <div class="purchase-prompt">
                        <div class="prompt-icon">🛒</div>
                        <h3>Purchase a Plan to Get Started</h3>
                        <p>Create a personalized meal plan tailored to {{ $child->name }}'s nutritional needs, preferences, and health goals.</p>
                        <div class="purchase-info">
                            <div class="info-item"><span class="check">✓</span> Personalized based on health profile</div>
                            <div class="info-item"><span class="check">✓</span> Regional cuisine preferences</div>
                            <div class="info-item"><span class="check">✓</span> Allergy-safe meal options</div>
                            <div class="info-item"><span class="check">✓</span> Clinical tips & guidance</div>
                        </div>
                        <a href="{{ route('product') }}" class="btn btn-main btn-lg">Browse Plans &amp; Pricing →</a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <style>
        .diet-chart-container { max-width: 1200px; margin: 0 auto; padding: 20px; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }

        /* HERO */
        .diet-chart-hero { background: linear-gradient(135deg, #7B2CBF 0%, #5A189A 100%); color: white; padding: 40px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; box-shadow: 0 10px 40px rgba(123,44,191,.2); }
        .hero-content h1 { font-size: 32px; font-weight: 700; margin: 0 0 8px; }
        .hero-content p  { font-size: 16px; opacity: .9; margin: 0; }
        .hero-stats { display: flex; gap: 20px; }
        .stat-card { background: rgba(255,255,255,.1); padding: 20px; border-radius: 12px; text-align: center; min-width: 120px; backdrop-filter: blur(10px); }
        .stat-card.active { background: rgba(255,255,255,.2); border: 2px solid rgba(255,255,255,.5); }
        .stat-value { display: block; font-size: 28px; font-weight: 700; margin-bottom: 5px; }
        .stat-label { display: block; font-size: 12px; text-transform: uppercase; opacity: .8; }

        /* PLAN SECTION */
        .meal-plan-section { background: white; border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .plan-header { margin-bottom: 30px; border-bottom: 2px solid #f0f0f0; padding-bottom: 20px; }
        .plan-header h2 { font-size: 24px; font-weight: 700; margin: 0 0 15px; }
        .plan-meta { display: flex; gap: 20px; flex-wrap: wrap; }
        .plan-meta span { background: #f5f5f5; padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 500; color: #666; }

        /* NUTRITIONIST REMARK */
        .nutritionist-remark { display: flex; align-items: flex-start; gap: 14px; margin: 0 0 24px; padding: 18px 20px; border: 1px solid #cce8da; border-left: 4px solid #168a64; border-radius: 14px; background: linear-gradient(120deg,#f1fbf6,#fbfefc); }
        .nutritionist-remark-icon { display: grid; place-items: center; flex: 0 0 38px; width: 38px; height: 38px; border-radius: 12px; color: #168a64; background: #ddf4e8; font-size: 20px; }
        .nutritionist-remark-label { display: block; margin-bottom: 5px; color: #168a64; font-size: 12px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; }
        .nutritionist-remark p { margin: 0; color: #24483f; line-height: 1.6; white-space: pre-line; }
        .nutritionist-remark time { display: block; margin-top: 8px; color: #71817b; font-size: 12px; }

        /* WEEK TABS */
        .week-tabs { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .week-tab { background: #f9f9f9; border: 2px solid transparent; padding: 10px 20px; border-radius: 10px; cursor: pointer; font-size: 13px; font-weight: 600; transition: all .2s; }
        .week-tab.active, .week-tab:hover { background: #7B2CBF; color: white; border-color: #7B2CBF; }

        /* DAY TABS */
        .week-days-nav { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 2px solid #f0f0f0; }
        .day-tab { background: #f9f9f9; border: 2px solid transparent; padding: 12px 10px; border-radius: 12px; cursor: pointer; transition: all .3s; text-align: center; font-size: 12px; font-weight: 600; min-width: 64px; }
        .day-tab:hover { background: #f0f0f0; border-color: #7B2CBF; }
        .day-tab.active { background: #7B2CBF; color: white; border-color: #7B2CBF; }
        .day-label { display: block; font-weight: 700; margin-bottom: 4px; }
        .day-date  { display: block; font-size: 10px; opacity: .8; }

        /* DAY MEALS */
        .day-meals { animation: fadeIn .3s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .day-title-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0; }
        .day-title-section h3 { font-size: 22px; font-weight: 700; margin: 0; }

        /* MEAL SLOT */
        .meal-slot { display: flex; gap: 20px; margin-bottom: 30px; padding: 20px; background: #f9f9f9; border-radius: 12px; border-left: 4px solid #7B2CBF; }
        .meal-time { font-weight: 700; color: #7B2CBF; font-size: 14px; min-width: 60px; text-align: center; }
        .meal-content { flex: 1; }
        .slot-name { font-size: 13px; font-weight: 700; color: #7B2CBF; text-transform: uppercase; margin-bottom: 12px; letter-spacing: .05em; }

        /* MEAL ITEM */
        .meal-item { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e0e0e0; }
        .meal-item:last-of-type { margin-bottom: 0; padding-bottom: 0; border-bottom: none; }
        .meal-dish h4 { font-size: 16px; font-weight: 600; margin: 0 0 4px; color: #333; }
        .regional-name { font-size: 12px; color: #999; font-style: italic; margin: 2px 0; }
        .meal-portion { font-size: 12px; color: #666; margin: 4px 0 0; }
        .meal-nutrients { display: flex; gap: 20px; }
        .nutrient { display: flex; flex-direction: column; align-items: center; }
        .n-value { font-size: 16px; font-weight: 700; color: #7B2CBF; }
        .n-label { font-size: 11px; color: #999; text-transform: uppercase; }

        /* PAIRING NOTE */
        .pairing-note { background: #FEF3E6; border-left: 3px solid #FF9900; padding: 12px 15px; border-radius: 6px; margin: 12px 0; display: flex; gap: 10px; font-size: 13px; color: #666; }
        .note-icon { flex-shrink: 0; }

        /* MEAL TOTAL */
        .meal-total { background: white; border-radius: 8px; padding: 12px; margin-top: 12px; }
        .total-label { font-size: 11px; font-weight: 700; text-transform: uppercase; color: #999; margin-bottom: 8px; }
        .total-values { display: flex; gap: 20px; }
        .total-item { display: flex; flex-direction: column; }
        .total-item .label { font-size: 11px; color: #999; }
        .total-item .value { font-size: 14px; font-weight: 700; color: #333; }

        /* GAPS */
        .nutrient-gaps-section { background: white; border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .nutrient-gaps-section h2 { font-size: 24px; font-weight: 700; margin-bottom: 25px; }
        .gaps-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(280px,1fr)); gap: 20px; }
        .gap-card { padding: 20px; border-radius: 12px; background: #f9f9f9; border-left: 4px solid #ccc; }
        .gap-card.gap-met { border-left-color: #4CAF50; background: #F1F8E9; }
        .gap-card.gap-partial-gap { border-left-color: #FF9800; background: #FFF3E0; }
        .gap-card.gap-significant-gap { border-left-color: #FF5722; background: #FFEBEE; }
        .gap-card.gap-critical-gap { border-left-color: #F44336; background: #FFEBEE; }
        .gap-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .gap-header h4 { font-size: 14px; font-weight: 600; margin: 0; }
        .gap-badge { background: white; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .gap-details { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 15px; }
        .gap-info { display: flex; flex-direction: column; }
        .info-label { font-size: 11px; color: #999; text-transform: uppercase; margin-bottom: 4px; }
        .info-value { font-size: 14px; font-weight: 700; color: #333; }
        .gap-progress { margin-top: 15px; }
        .progress-bar { height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg,#7B2CBF,#D81C7F); border-radius: 3px; transition: width .3s; }

        /* CLINICAL TIPS */
        .clinical-tips-section { background: white; border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 20px rgba(0,0,0,.08); }
        .clinical-tips-section h2 { font-size: 24px; font-weight: 700; margin-bottom: 25px; }
        .tips-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap: 20px; }
        .tip-card { background: linear-gradient(135deg,#F8F9FA 0%,#F0F0F0 100%); padding: 20px; border-radius: 12px; border-left: 4px solid #7B2CBF; }
        .tip-card h4 { font-size: 16px; font-weight: 600; margin: 0 0 12px; color: #333; }
        .tip-combination { font-size: 13px; margin-bottom: 12px; line-height: 1.5; }
        .tip-combination strong { color: #7B2CBF; display: block; margin-bottom: 4px; }
        .tip-science { font-size: 13px; color: #666; line-height: 1.5; }
        .tip-science strong { color: #333; display: block; margin-bottom: 4px; }

        /* NO PLAN */
        .no-plan-container { background: white; border-radius: 16px; padding: 60px 40px; text-align: center; box-shadow: 0 4px 20px rgba(0,0,0,.08); margin-bottom: 30px; }
        .no-plan-icon { margin-bottom: 30px; }
        .no-plan-icon img { max-width: 150px; height: auto; opacity: .8; }
        .no-plan-container h2 { font-size: 28px; font-weight: 700; margin: 20px 0 10px; }
        .no-plan-container > p { font-size: 16px; color: #666; margin-bottom: 30px; }
        .purchase-prompt { background: #F8F9FA; border: 2px dashed #7B2CBF; padding: 40px 30px; border-radius: 16px; margin-top: 30px; }
        .prompt-icon { font-size: 48px; margin-bottom: 20px; }
        .purchase-prompt h3 { font-size: 22px; font-weight: 700; margin: 0 0 10px; color: #333; }
        .purchase-prompt > p { font-size: 15px; color: #666; margin-bottom: 25px; }
        .purchase-info { text-align: left; display: inline-block; margin-bottom: 30px; max-width: 350px; }
        .info-item { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; font-size: 14px; }
        .check { background: #4CAF50; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }

        /* ACTION BUTTONS */
        .plan-actions { display: flex; gap: 15px; justify-content: center; margin-top: 30px; flex-wrap: wrap; }
        .btn { padding: 12px 24px; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all .3s; }
        .btn-primary { background: #7B2CBF; color: white; }
        .btn-primary:hover { background: #5A189A; box-shadow: 0 4px 15px rgba(123,44,191,.3); }
        .btn-secondary { background: #E8E8E8; color: #333; }
        .btn-secondary:hover { background: #D0D0D0; }
        .btn-warning { background: #FF9800; color: white; }
        .btn-warning:hover { background: #F57C00; }
        .btn-main { background: linear-gradient(135deg,#7B2CBF,#D81C7F); color: white; padding: 15px 40px; font-size: 16px; text-decoration: none; display: inline-block; }
        .btn-main:hover { box-shadow: 0 6px 20px rgba(123,44,191,.3); color: white; }
        .btn-lg { min-width: 250px; }
        .info-text { font-size: 13px; color: #666; margin-top: 15px; }

        /* MEAL ITEM ACTIONS & BADGES */
        .cook-time-badge { display: inline-block; font-size: 11px; color: #7B2CBF; font-weight: 600; background: #F3E8FF; padding: 2px 8px; border-radius: 6px; margin-top: 4px; }
        .recipe-btn-wrapper { margin-top: 8px; }
        .btn-recipe-action { background: #F3E8FF; color: #7B2CBF; border: 1px solid #D8B4FE; border-radius: 6px; padding: 4px 10px; font-size: 11px; font-weight: 600; cursor: pointer; transition: all .2s; }
        .btn-recipe-action:hover { background: #7B2CBF; color: white; border-color: #7B2CBF; }
        .btn-recipe-action.locked { background: #f3f3f3; color: #888; border-color: #ddd; cursor: pointer; }
        .btn-recipe-action.locked:hover { background: #e8e8e8; color: #555; }

        /* MODALS */
        .nutri-modal-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.55); backdrop-filter: blur(4px); z-index: 99999; display: flex; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box; }
        .nutri-modal-dialog { background: white; border-radius: 16px; max-width: 650px; width: 100%; max-height: 85vh; display: flex; flex-direction: column; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; animation: popIn .2s ease; }
        @keyframes popIn { from { transform: scale(0.96); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .nutri-modal-header { display: flex; justify-content: space-between; align-items: flex-start; padding: 20px 24px; border-bottom: 1px solid #eee; background: #fafafa; }
        .nutri-modal-close { background: none; border: none; font-size: 26px; line-height: 1; color: #888; cursor: pointer; padding: 0; margin-left: 10px; }
        .nutri-modal-close:hover { color: #222; }
        .nutri-modal-body { padding: 24px; overflow-y: auto; flex: 1; }
        .nutri-modal-footer { padding: 16px 24px; border-top: 1px solid #eee; background: #fafafa; display: flex; justify-content: flex-end; gap: 10px; }

        /* SHOPPING LIST */
        .shopping-categories-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }
        .shopping-cat-card { background: #fdfdfd; border: 1px solid #e8e8e8; border-radius: 10px; padding: 14px 16px; }
        .shopping-cat-header { display: flex; justify-content: space-between; align-items: center; font-weight: 700; font-size: 13px; color: #2E7D32; border-bottom: 1px dashed #ddd; padding-bottom: 8px; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 0.04em; }
        .shopping-badge { background: #E8F5E9; color: #2E7D32; font-size: 11px; padding: 2px 8px; border-radius: 10px; font-weight: 600; }
        .shopping-cat-items { list-style: none; margin: 0; padding: 0; font-size: 13px; color: #444; }
        .shopping-cat-items li { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; border-bottom: 1px solid #f6f6f6; }
        .item-count { font-size: 11px; color: #888; background: #f0f0f0; padding: 1px 6px; border-radius: 4px; }

        /* HEALTH SCORECARD */
        .score-summary-banner { display: flex; align-items: center; gap: 20px; background: linear-gradient(135deg, #F1F8E9 0%, #E8F5E9 100%); border: 1px solid #C8E6C9; padding: 20px; border-radius: 12px; margin-bottom: 24px; }
        .score-number { font-size: 42px; font-weight: 800; color: #2E7D32; line-height: 1; }
        .score-grade-badge { display: inline-block; background: #2E7D32; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .score-breakdown-list { display: flex; flex-direction: column; gap: 14px; }
        .score-area-item { background: #fdfdfd; border: 1px solid #f0f0f0; padding: 12px 16px; border-radius: 8px; }
        .area-header { display: flex; justify-content: space-between; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #333; }
        .area-points { color: #7B2CBF; font-weight: 700; font-size: 12px; }
        .area-progress .progress-bar { height: 8px; background: #eee; border-radius: 4px; overflow: hidden; }
        .area-progress .progress-fill { height: 100%; background: linear-gradient(90deg, #7B2CBF, #2E7D32); border-radius: 4px; }

        @media (max-width: 768px) {
            .diet-chart-hero { flex-direction: column; text-align: center; gap: 20px; }
            .hero-stats { width: 100%; justify-content: center; }
            .week-days-nav { grid-template-columns: repeat(auto-fit,minmax(70px,1fr)); }
            .meal-slot { flex-direction: column; }
            .meal-item { flex-direction: column; align-items: flex-start; }
            .gaps-grid { grid-template-columns: 1fr; }
            .plan-actions { flex-direction: column; }
            .btn { width: 100%; }
        }
    </style>

    <script>
        // Week tab navigation
        document.querySelectorAll('.week-tab').forEach(tab => {
            tab.addEventListener('click', function () {
                const w = this.dataset.week;
                document.querySelectorAll('.week-tab').forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected','false'); });
                document.querySelectorAll('.week-panel').forEach(p => { p.style.display = 'none'; });
                this.classList.add('active');
                this.setAttribute('aria-selected','true');
                document.getElementById('week-panel-' + w).style.display = '';
            });
        });

        // Day tab navigation (delegated so it works for all week panels)
        document.addEventListener('click', function (e) {
            const tab = e.target.closest('.day-tab');
            if (!tab) return;
            const week  = tab.dataset.week;
            const dayId = tab.dataset.dayId;
            const panel = document.getElementById('week-panel-' + week);
            if (!panel) return;

            panel.querySelectorAll('.day-tab').forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected','false'); });
            panel.querySelectorAll('.day-meals').forEach(d => { d.style.display = 'none'; });

            tab.classList.add('active');
            tab.setAttribute('aria-selected','true');
            const dayEl = document.getElementById(dayId);
            if (dayEl) dayEl.style.display = '';
        });

        // Recipe Modal handlers
        function showRecipe(btn) {
            const name = btn.dataset.name || 'Recipe';
            const cook = btn.dataset.cook ? `${btn.dataset.cook} mins` : '';
            const portion = btn.dataset.portion ? `${btn.dataset.portion}g serving` : '';
            const rawIngredients = btn.dataset.ingredients || '';
            const method = btn.dataset.method || 'Standard healthy preparation as per nutritionist guidelines.';

            document.getElementById('recipeTitle').textContent = name;
            document.getElementById('recipePortion').textContent = portion ? `🍽️ ${portion}` : '';
            document.getElementById('recipeCook').textContent = cook ? `⏱️ Cook time: ${cook}` : '';

            const ingListEl = document.getElementById('recipeIngredientsList');
            ingListEl.innerHTML = '';
            if (rawIngredients.trim()) {
                const items = rawIngredients.split(/[·\n,]/).map(s => s.trim()).filter(Boolean);
                items.forEach(ing => {
                    const li = document.createElement('li');
                    li.textContent = ing;
                    ingListEl.appendChild(li);
                });
            } else {
                const li = document.createElement('li');
                li.textContent = 'Standard fresh ingredients as per local preparation.';
                ingListEl.appendChild(li);
            }

            document.getElementById('recipeMethodContent').textContent = method;
            document.getElementById('recipeModal').style.display = 'flex';
        }

        function closeRecipeModal() {
            document.getElementById('recipeModal').style.display = 'none';
        }

        // Global Nutrition State
        window.NutriShoppingListsData = @json($shoppingListsData ?? []);
        window.NutriChildPdfBaseUrl   = "{{ !empty($child) ? route('child.diet-chart.download-pdf', $child->id) : '' }}";
        window.NutriCurrentDayIndex   = 0;
        window.NutriCurrentWeekNo     = 1;
        let currentShoppingScope      = 'day';

        function togglePdfDownloadMenuTop(e) {
            if (e) e.stopPropagation();
            const dropdown = document.getElementById('pdfDownloadDropdownTop');
            if (!dropdown) return;
            const isShown = dropdown.style.display === 'block';
            dropdown.style.display = isShown ? 'none' : 'block';
        }

        document.addEventListener('click', function(e) {
            const wrap = document.getElementById('downloadPdfDropdownWrapTop');
            const dropdown = document.getElementById('pdfDownloadDropdownTop');
            if (dropdown && wrap && !wrap.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });

        function updatePdfDownloadLinksTop(dayIndex, weekNo, dayLabel) {
            const dayLink = document.getElementById('pdfDownloadActiveDayLinkTop');
            const dayText = document.getElementById('pdfActiveDayLabelTop');
            if (dayLink && window.NutriChildPdfBaseUrl) {
                dayLink.href = window.NutriChildPdfBaseUrl + '?day_index=' + dayIndex;
            }
            if (dayText) {
                dayText.textContent = 'Day ' + (dayIndex + 1) + ' (' + (dayLabel || 'Selected Day') + ')';
            }

            const weekLink = document.getElementById('pdfDownloadActiveWeekLinkTop');
            const weekText = document.getElementById('pdfActiveWeekLabelTop');
            if (weekLink && window.NutriChildPdfBaseUrl) {
                weekLink.href = window.NutriChildPdfBaseUrl + '?week=' + weekNo;
            }
            if (weekText) {
                weekText.textContent = 'Week ' + weekNo;
            }
        }

        function switchShoppingScope(scope) {
            currentShoppingScope = scope;
            renderShoppingListModal();
        }

        function renderShoppingListModal() {
            const data = window.NutriShoppingListsData;
            const container = document.getElementById('shoppingListGrid');
            const textEl = document.getElementById('shoppingListText');
            const subtitle = document.getElementById('shoppingModalSubtitle');
            if (!container) return;

            let targetData = null;
            let titleStr = '';

            if (currentShoppingScope === 'day') {
                const dayInfo = data && data.by_day ? data.by_day[window.NutriCurrentDayIndex] : null;
                targetData = dayInfo ? dayInfo.categories : {};
                titleStr = dayInfo ? dayInfo.label : ('Day ' + (window.NutriCurrentDayIndex + 1));
                if (subtitle) subtitle.textContent = 'Grocery ingredients for ' + titleStr;
            } else if (currentShoppingScope === 'week') {
                const weekInfo = data && data.by_week ? data.by_week[window.NutriCurrentWeekNo] : null;
                targetData = weekInfo ? weekInfo.categories : {};
                titleStr = weekInfo ? weekInfo.label : ('Week ' + window.NutriCurrentWeekNo);
                if (subtitle) subtitle.textContent = 'Grocery ingredients for ' + titleStr;
            } else {
                const fullInfo = data ? data.full : null;
                targetData = fullInfo ? fullInfo.categories : {};
                titleStr = fullInfo ? fullInfo.label : 'Full Plan';
                if (subtitle) subtitle.textContent = 'Grocery ingredients for ' + titleStr;
            }

            document.querySelectorAll('.shopping-scope-tab').forEach(function(btn) {
                const isActive = btn.dataset.scope === currentShoppingScope;
                btn.classList.toggle('active', isActive);
                if (isActive) {
                    btn.style.background = '#2E7D32';
                    btn.style.color = '#fff';
                } else {
                    btn.style.background = '#f0f0f0';
                    btn.style.color = '#555';
                }
            });

            container.innerHTML = '';
            let copyText = 'NutriBuddy Shopping List - ' + titleStr + '\n============================\n\n';

            const categories = Object.keys(targetData || {});
            if (categories.length === 0) {
                container.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding:35px 20px; color:#888;"><div style="font-size:28px; margin-bottom:8px;">🛒</div>No specific grocery ingredients for this selection.</div>';
                if (textEl) textEl.textContent = '';
                return;
            }

            categories.forEach(function(cat) {
                const items = targetData[cat] || {};
                const itemNames = Object.keys(items);
                if (itemNames.length === 0) return;

                copyText += cat + ':\n';
                itemNames.forEach(function(it) {
                    copyText += '  • ' + it + ' (' + items[it] + 'x)\n';
                });
                copyText += '\n';

                const card = document.createElement('div');
                card.style.cssText = 'background:#fdfdfd; border:1px solid #e8e8e8; border-radius:10px; padding:14px 16px;';

                let itemsHtml = '';
                itemNames.forEach(function(it) {
                    itemsHtml += '<li style="display:flex; justify-content:space-between; align-items:center; padding:5px 0; border-bottom:1px solid #f6f6f6;">' +
                                 '<span style="font-size:13px; color:#333;">' + it + '</span>' +
                                 '<span style="font-size:11px; color:#2E7D32; background:#E8F5E9; padding:2px 7px; border-radius:4px; font-weight:600;">' + items[it] + 'x</span>' +
                                 '</li>';
                });

                card.innerHTML = '<div style="display:flex; justify-content:space-between; align-items:center; font-weight:700; font-size:12px; color:#2E7D32; border-bottom:1px dashed #ddd; padding-bottom:8px; margin-bottom:10px; text-transform:uppercase;">' +
                                 '<span>' + cat + '</span>' +
                                 '<span style="background:#E8F5E9; color:#2E7D32; font-size:11px; padding:2px 8px; border-radius:10px;">' + itemNames.length + ' items</span>' +
                                 '</div>' +
                                 '<ul style="list-style:none; margin:0; padding:0;">' + itemsHtml + '</ul>';

                container.appendChild(card);
            });

            if (textEl) textEl.textContent = copyText.trim();
        }

        // Hook day tab click to update dynamic state
        const origDayTabClick = document.addEventListener('click', function(e) {
            const tab = e.target.closest('.day-tab');
            if (!tab) return;
            const di = parseInt(tab.dataset.dayIndex || '0', 10);
            const wn = parseInt(tab.dataset.week || '1', 10);
            const dl = tab.dataset.dayLabel || ('Day ' + (di + 1));

            window.NutriCurrentDayIndex = di;
            window.NutriCurrentWeekNo   = wn;

            const dayTabTitle = document.getElementById('shoppingSelectedDayTitle');
            if (dayTabTitle) dayTabTitle.textContent = 'Day ' + (di + 1);

            const weekTabTitle = document.getElementById('shoppingSelectedWeekTitle');
            if (weekTabTitle) weekTabTitle.textContent = 'Week ' + wn;

            updatePdfDownloadLinksTop(di, wn, dl);

            const modal = document.getElementById('shoppingListModal');
            if (modal && modal.style.display === 'flex') {
                renderShoppingListModal();
            }
        });

        // Shopping List Modal handlers
        function openShoppingListModal() {
            const m = document.getElementById('shoppingListModal');
            if (m) {
                renderShoppingListModal();
                m.style.display = 'flex';
            }
        }

        function closeShoppingListModal() {
            const m = document.getElementById('shoppingListModal');
            if (m) m.style.display = 'none';
        }

        function copyShoppingList() {
            const textEl = document.getElementById('shoppingListText');
            if (!textEl) return;
            const text = textEl.textContent.trim();
            if (!text) {
                alert('No ingredients to copy for this selection.');
                return;
            }
            if (navigator.clipboard) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Shopping list copied to clipboard!');
                }).catch(() => {
                    prompt('Copy your shopping list:', text);
                });
            } else {
                prompt('Copy your shopping list:', text);
            }
        }

        // Health Score Modal handlers
        function openHealthScoreModal() {
            const m = document.getElementById('healthScoreModal');
            if (m) m.style.display = 'flex';
        }

        function closeHealthScoreModal() {
            const m = document.getElementById('healthScoreModal');
            if (m) m.style.display = 'none';
        }

        // Global Escape key listener to close modals
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeRecipeModal();
                closeShoppingListModal();
                closeHealthScoreModal();
            }
        });

        document.getElementById('sharePlan')?.addEventListener('click', function () {
            const text = `Check out {{ $child->name }}'s personalized meal plan on NutriBuddy!`;
            if (navigator.share) {
                navigator.share({ title: 'NutriBuddy Meal Plan', text, url: window.location.href });
            } else {
                alert('Sharing coming soon!');
            }
        });

        document.getElementById('updatePlan')?.addEventListener('click', function () {
            if (confirm('Generate a new personalized plan?')) {
                window.location.href = '{{ route("child.diet-chart.regenerate", $child->id) }}';
            }
        });

        document.getElementById('generateNewPlan')?.addEventListener('click', function () {
            window.location.href = '{{ route("frontend.nutrition.questionnaire.child", $child) }}';
        });
    </script>
@endsection
