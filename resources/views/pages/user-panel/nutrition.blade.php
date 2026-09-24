@extends('layouts.user-panel')
@section('title', 'Nutrition — NutriBuddy')
@section('panel-page-class', 'panel-nutrition')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/nutrition.css') }}?v={{ time() }}">
@endpush

@section('panel-content')
<div class="ud-main">
    <div class="page">
        <div class="nutrition-container">
            
            <!-- Date Selector -->
            <div class="nutri-date-selector">
                <div class="nutri-date-item">
                    <span class="nutri-date-day">Sun</span>
                    <span class="nutri-date-num">20</span>
                </div>
                <div class="nutri-date-item active">
                    <span class="nutri-date-day">Mon</span>
                    <span class="nutri-date-num">21</span>
                </div>
                <div class="nutri-date-item">
                    <span class="nutri-date-day">Tue</span>
                    <span class="nutri-date-num">22</span>
                </div>
                <div class="nutri-date-item">
                    <span class="nutri-date-day">Wed</span>
                    <span class="nutri-date-num">23</span>
                </div>
                <div class="nutri-date-item">
                    <span class="nutri-date-day">Thu</span>
                    <span class="nutri-date-num">24</span>
                </div>
            </div>

            <!-- Tabs -->
            <div class="nutri-tabs">
                <div class="nutri-tab active" data-target="tab-food">Food View</div>
                <div class="nutri-tab" data-target="tab-nutri">Nutri View</div>
                <div class="nutri-tab" data-target="tab-calories">Daily Calories</div>
                <div class="nutri-tab" data-target="tab-insights">Insights & Progress</div>
            </div>

            <!-- TAB 1: FOOD VIEW -->
            <div id="tab-food" class="nutri-tab-content active">
                
                <!-- Calorie & Macro Summary -->
                <div class="nutri-card">
                    <div class="cal-summary-grid">
                        <div class="cal-ring-container">
                            <svg viewBox="0 0 100 100" class="cal-ring-svg">
                                <circle cx="50" cy="50" r="40" class="cal-ring-bg"></circle>
                                <circle cx="50" cy="50" r="40" class="cal-ring-progress" style="stroke-dasharray: 251.3; stroke-dashoffset: 251.3;"></circle>
                            </svg>
                            <div class="cal-ring-text">
                                <span class="cal-ring-val" id="calsConsumed">0</span>
                                <span class="cal-ring-lbl">Consumed</span>
                            </div>
                        </div>
                        <div class="macro-bars">
                            <div style="font-weight: 800; font-size: 0.9rem; margin-bottom: 4px;">
                                <span id="calsRemaining" style="color: var(--pk);">2100</span> kcal remaining
                            </div>
                            <div class="macro-bar-item">
                                <div class="macro-bar-labels">
                                    <span class="macro-name">Protein</span>
                                    <span class="macro-val">0 / 152g</span>
                                </div>
                                <div class="macro-track"><div class="macro-fill protein" style="width: 0%"></div></div>
                            </div>
                            <div class="macro-bar-item">
                                <div class="macro-bar-labels">
                                    <span class="macro-name">Carbs</span>
                                    <span class="macro-val">0 / 236g</span>
                                </div>
                                <div class="macro-track"><div class="macro-fill carbs" style="width: 0%"></div></div>
                            </div>
                            <div class="macro-bar-item">
                                <div class="macro-bar-labels">
                                    <span class="macro-name">Fats</span>
                                    <span class="macro-val">0 / 61g</span>
                                </div>
                                <div class="macro-track"><div class="macro-fill fats" style="width: 0%"></div></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meals -->
                <div class="meal-section" id="meal-breakfast">
                    <div class="meal-header">
                        <div class="meal-info">
                            <h3>Breakfast</h3>
                            <div class="meal-meta">325 kcal · 12g Protein</div>
                        </div>
                        <button class="meal-add-btn">+</button>
                    </div>
                    <div class="food-list">
                        <div class="food-item">
                            <div>
                                <div class="food-name">Oatmeal</div>
                                <div class="food-details">1 bowl · P: 4g · C: 27g · F: 3g</div>
                            </div>
                            <div class="food-cal">150 Kcal</div>
                        </div>
                        <div class="food-item">
                            <div>
                                <div class="food-name">Whole Milk</div>
                                <div class="food-details">1 glass · P: 8g · C: 12g · F: 8g</div>
                            </div>
                            <div class="food-cal">175 Kcal</div>
                        </div>
                    </div>
                </div>

                <div class="meal-section" id="meal-lunch">
                    <div class="meal-header">
                        <div class="meal-info">
                            <h3>Lunch</h3>
                            <div class="meal-meta">450 kcal · 25g Protein</div>
                        </div>
                        <button class="meal-add-btn">+</button>
                    </div>
                    <div class="food-list">
                        <div class="food-item">
                            <div>
                                <div class="food-name">Brown Rice</div>
                                <div class="food-details">1 cup · P: 5g · C: 45g · F: 1.5g</div>
                            </div>
                            <div class="food-cal">200 Kcal</div>
                        </div>
                        <div class="food-item">
                            <div>
                                <div class="food-name">Chicken Curry</div>
                                <div class="food-details">150g · P: 20g · C: 5g · F: 12g</div>
                            </div>
                            <div class="food-cal">250 Kcal</div>
                        </div>
                    </div>
                </div>

                <div class="meal-section" id="meal-snacks">
                    <div class="meal-header">
                        <div class="meal-info">
                            <h3>Snacks</h3>
                            <div class="meal-meta">150 kcal · 5g Protein</div>
                        </div>
                        <button class="meal-add-btn">+</button>
                    </div>
                    <div class="food-list">
                        <div class="food-item">
                            <div>
                                <div class="food-name">Almonds</div>
                                <div class="food-details">30g · P: 5g · C: 5g · F: 12g</div>
                            </div>
                            <div class="food-cal">150 Kcal</div>
                        </div>
                    </div>
                </div>

                <div class="meal-section" id="meal-dinner">
                    <div class="meal-header">
                        <div class="meal-info">
                            <h3>Dinner</h3>
                            <div class="meal-meta">300 kcal · 15g Protein</div>
                        </div>
                        <button class="meal-add-btn">+</button>
                    </div>
                    <div class="food-list">
                        <div class="food-item">
                            <div>
                                <div class="food-name">Roti</div>
                                <div class="food-details">2 pieces · P: 4g · C: 28g · F: 1g</div>
                            </div>
                            <div class="food-cal">140 Kcal</div>
                        </div>
                        <div class="food-item">
                            <div>
                                <div class="food-name">Lentil Soup (Dal)</div>
                                <div class="food-details">1 bowl · P: 11g · C: 20g · F: 4g</div>
                            </div>
                            <div class="food-cal">160 Kcal</div>
                        </div>
                    </div>
                </div>

                <div class="meal-section" id="supplements" style="margin-top: 30px; border-color: var(--pu);">
                    <div class="meal-header">
                        <div class="meal-info">
                            <h3 style="color: var(--pu);">Daily Supplements</h3>
                            <div class="meal-meta">0 of 3 taken</div>
                        </div>
                    </div>
                    <div class="food-list">
                        <div class="food-item sup-item" style="cursor: pointer;" onclick="this.classList.toggle('sup-taken')">
                            <div>
                                <div class="food-name">Whey Protein</div>
                                <div class="food-details">Post-workout (1 scoop)</div>
                            </div>
                            <div class="sup-check"></div>
                        </div>
                        <div class="food-item sup-item" style="cursor: pointer;" onclick="this.classList.toggle('sup-taken')">
                            <div>
                                <div class="food-name">Multivitamin</div>
                                <div class="food-details">With breakfast (1 pill)</div>
                            </div>
                            <div class="sup-check"></div>
                        </div>
                        <div class="food-item sup-item" style="cursor: pointer;" onclick="this.classList.toggle('sup-taken')">
                            <div>
                                <div class="food-name">Omega-3 Fish Oil</div>
                                <div class="food-details">With dinner (2 capsules)</div>
                            </div>
                            <div class="sup-check"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: NUTRI VIEW -->
            <div id="tab-nutri" class="nutri-tab-content">
                <div class="nutri-card">
                    <div class="nutri-card-header">
                        <span class="nutri-card-title">Nutrient Breakdown</span>
                    </div>

                    <!-- Carbohydrates -->
                    <div class="nutrient-group">
                        <div class="nutrient-group-title" style="color: var(--or);">Carbohydrates</div>
                        <div class="nutrient-row">
                            <div class="nutrient-row-labels">
                                <span>Total Sugar</span>
                                <span>0g / 50g</span>
                            </div>
                            <div class="nutrient-row-track"><div class="nutrient-row-fill" style="width: 0%; background: var(--or);"></div></div>
                        </div>
                        <div class="nutrient-row">
                            <div class="nutrient-row-labels">
                                <span>Added Sugar</span>
                                <span>0g / 25g</span>
                            </div>
                            <div class="nutrient-row-track"><div class="nutrient-row-fill" style="width: 0%; background: var(--or);"></div></div>
                        </div>
                        <div class="nutrient-row">
                            <div class="nutrient-row-labels">
                                <span>Dietary Fiber</span>
                                <span>0g / 30g</span>
                            </div>
                            <div class="nutrient-row-track"><div class="nutrient-row-fill" style="width: 0%; background: var(--or);"></div></div>
                        </div>
                    </div>

                    <!-- Protein -->
                    <div class="nutrient-group">
                        <div class="nutrient-group-title" style="color: var(--pu);">Protein</div>
                        <div class="nutrient-row">
                            <div class="nutrient-row-labels">
                                <span>Total Protein</span>
                                <span>0g / 152g</span>
                            </div>
                            <div class="nutrient-row-track"><div class="nutrient-row-fill" style="width: 0%; background: var(--pu);"></div></div>
                        </div>
                    </div>

                    <!-- Fat -->
                    <div class="nutrient-group">
                        <div class="nutrient-group-title" style="color: var(--ye);">Fat</div>
                        <div class="nutrient-row">
                            <div class="nutrient-row-labels">
                                <span>Saturated</span>
                                <span>0g / 20g</span>
                            </div>
                            <div class="nutrient-row-track"><div class="nutrient-row-fill" style="width: 0%; background: var(--ye);"></div></div>
                        </div>
                        <div class="nutrient-row">
                            <div class="nutrient-row-labels">
                                <span>Unsaturated</span>
                                <span>0g / 40g</span>
                            </div>
                            <div class="nutrient-row-track"><div class="nutrient-row-fill" style="width: 0%; background: var(--ye);"></div></div>
                        </div>
                        <div class="nutrient-row">
                            <div class="nutrient-row-labels">
                                <span>Trans</span>
                                <span>0g / 0g</span>
                            </div>
                            <div class="nutrient-row-track"><div class="nutrient-row-fill" style="width: 0%; background: var(--ye);"></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: DAILY CALORIES -->
            <div id="tab-calories" class="nutri-tab-content">
                <div class="nutri-card">
                    <div style="text-align: center; margin-bottom: 24px;">
                        <h2 style="font-family: 'Fredoka One', cursive; margin-bottom: 8px;">Target Calories</h2>
                        <div style="font-size: 2rem; color: var(--pu); font-weight: 900;">2100 <span style="font-size: 1rem; color: var(--muted);">Kcal</span></div>
                    </div>

                    <div style="display: flex; justify-content: space-around; padding: 20px 0; border-top: 1.5px solid var(--border); border-bottom: 1.5px solid var(--border); margin-bottom: 24px;">
                        <div style="text-align: center;">
                            <div style="font-weight: 800; color: var(--or); margin-bottom: 4px;">Carbs</div>
                            <div style="font-size: 1.1rem; font-weight: 900;">236g</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-weight: 800; color: var(--pu); margin-bottom: 4px;">Protein</div>
                            <div style="font-size: 1.1rem; font-weight: 900;">152g</div>
                        </div>
                        <div style="text-align: center;">
                            <div style="font-weight: 800; color: var(--ye); margin-bottom: 4px;">Fats</div>
                            <div style="font-size: 1.1rem; font-weight: 900;">61g</div>
                        </div>
                    </div>

                </div>

                <div class="nutri-card">
                    <h3 style="font-family: 'Fredoka One', cursive; font-size: 1.1rem; margin-bottom: 16px;">Calories Consumed</h3>
                    <div style="display: flex; gap: 8px; margin-bottom: 20px;">
                        <button class="chart-filter-btn active" style="padding: 6px 16px; font-size: 0.8rem; border-radius: 20px; border: 1.5px solid var(--border); background: var(--pkl); color: var(--pk); font-weight: 800; cursor: pointer;">W</button>
                        <button class="chart-filter-btn" style="padding: 6px 16px; font-size: 0.8rem; border-radius: 20px; border: 1.5px solid var(--border); background: #fff; color: var(--muted); font-weight: 800; cursor: pointer;">M</button>
                        <button class="chart-filter-btn" style="padding: 6px 16px; font-size: 0.8rem; border-radius: 20px; border: 1.5px solid var(--border); background: #fff; color: var(--muted); font-weight: 800; cursor: pointer;">6M</button>
                    </div>
                    <div style="height: 150px; background: var(--cr); border-radius: 12px; display: flex; align-items: flex-end; justify-content: space-around; padding: 20px 10px 0; border-bottom: 1px solid var(--border);">
                        <div style="width: 12%; background: linear-gradient(to top, var(--pu), var(--pk)); height: 40%; border-radius: 6px 6px 0 0; opacity: 0.6;"></div>
                        <div style="width: 12%; background: linear-gradient(to top, var(--pu), var(--pk)); height: 60%; border-radius: 6px 6px 0 0; opacity: 0.7;"></div>
                        <div style="width: 12%; background: linear-gradient(to top, var(--pu), var(--pk)); height: 50%; border-radius: 6px 6px 0 0; opacity: 0.65;"></div>
                        <div style="width: 12%; background: linear-gradient(to top, var(--pu), var(--pk)); height: 80%; border-radius: 6px 6px 0 0; opacity: 0.8;"></div>
                        <div style="width: 12%; background: linear-gradient(to top, var(--pu), var(--pk)); height: 45%; border-radius: 6px 6px 0 0; opacity: 0.6;"></div>
                        <div style="width: 12%; background: linear-gradient(to top, var(--pu), var(--pk)); height: 90%; border-radius: 6px 6px 0 0; opacity: 1.0;"></div>
                        <div style="width: 12%; background: linear-gradient(to top, var(--pu), var(--pk)); height: 75%; border-radius: 6px 6px 0 0; opacity: 0.85;"></div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: INSIGHTS & PROGRESS -->
            <div id="tab-insights" class="nutri-tab-content">
                <div class="nutri-card">
                    <h3 style="font-family: 'Fredoka One', cursive; font-size: 1.2rem; margin-bottom: 20px;">Growth Signal</h3>
                    
                    <div style="display: flex; gap: 8px; margin-bottom: 20px;">
                        <button class="chart-filter-btn active" style="padding: 6px 16px; font-size: 0.8rem; border-radius: 20px; border: 1.5px solid var(--border); background: var(--pkl); color: var(--pk); font-weight: 800; cursor: pointer;">1M</button>
                        <button class="chart-filter-btn" style="padding: 6px 16px; font-size: 0.8rem; border-radius: 20px; border: 1.5px solid var(--border); background: #fff; color: var(--muted); font-weight: 800; cursor: pointer;">3M</button>
                        <button class="chart-filter-btn" style="padding: 6px 16px; font-size: 0.8rem; border-radius: 20px; border: 1.5px solid var(--border); background: #fff; color: var(--muted); font-weight: 800; cursor: pointer;">1Y</button>
                    </div>
                    
                    <div class="growth-chart" style="height: 180px; background: linear-gradient(to bottom, var(--pul), #fff); border-radius: 12px; border: 1.5px solid var(--border); position: relative; overflow: hidden; display: flex; align-items: flex-end;">
                        <!-- SVG Curve Simulation -->
                        <svg viewBox="0 0 100 40" preserveAspectRatio="none" style="width: 100%; height: 100%;">
                            <path d="M0 40 L0 30 Q10 35 20 20 T40 15 T60 25 T80 5 T100 10 L100 40 Z" fill="var(--pu)" opacity="0.3"></path>
                            <path d="M0 30 Q10 35 20 20 T40 15 T60 25 T80 5 T100 10" fill="none" stroke="var(--pu)" stroke-width="2"></path>
                        </svg>
                        
                        <!-- Floating Data Points -->
                        <div style="position: absolute; left: 20%; bottom: 45%; width: 10px; height: 10px; background: #fff; border: 2px solid var(--pu); border-radius: 50%;"></div>
                        <div style="position: absolute; left: 60%; bottom: 35%; width: 10px; height: 10px; background: #fff; border: 2px solid var(--pu); border-radius: 50%;"></div>
                        <div style="position: absolute; left: 80%; bottom: 85%; width: 10px; height: 10px; background: #fff; border: 2px solid var(--pu); border-radius: 50%; box-shadow: 0 0 10px var(--pu);"></div>
                    </div>
                    <div style="text-align: center; margin-top: 16px; font-weight: 700; color: var(--dk);">
                        Muscle Mass <span style="color: var(--pk);">+2.4 kg</span> this month
                    </div>
                </div>

                <div class="nutri-card" style="margin-top: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <div>
                            <h3 style="font-family: 'Fredoka One', cursive; font-size: 1.2rem; margin: 0 0 4px 0;">Quarterly Check-In</h3>
                            <div style="font-size: 0.85rem; color: var(--muted); font-weight: 700;">Next Check-in: Oct 15, 2026</div>
                        </div>
                        <span style="background: var(--orl); color: var(--or); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 800;">In 23 Days</span>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 24px;">
                        <div style="display: flex; gap: 12px;">
                            <div style="flex: 1;">
                                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--dk); margin-bottom: 6px;">Current Weight (kg)</label>
                                <input type="number" placeholder="e.g. 75.5" style="width: 100%; padding: 12px; border: 1.5px solid var(--border); border-radius: 12px; font-size: 0.9rem; background: var(--cr); outline: none;">
                            </div>
                            <div style="flex: 1;">
                                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--dk); margin-bottom: 6px;">Body Fat (%)</label>
                                <input type="number" placeholder="e.g. 15" style="width: 100%; padding: 12px; border: 1.5px solid var(--border); border-radius: 12px; font-size: 0.9rem; background: var(--cr); outline: none;">
                            </div>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--dk); margin-bottom: 6px;">Waist Measurement (inches)</label>
                            <input type="number" placeholder="e.g. 32" style="width: 100%; padding: 12px; border: 1.5px solid var(--border); border-radius: 12px; font-size: 0.9rem; background: var(--cr); outline: none;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8rem; font-weight: 700; color: var(--dk); margin-bottom: 6px;">Notes / How are you feeling?</label>
                            <textarea placeholder="Any updates on your energy levels or progress..." rows="3" style="width: 100%; padding: 12px; border: 1.5px solid var(--border); border-radius: 12px; font-size: 0.9rem; background: var(--cr); resize: none; outline: none;"></textarea>
                        </div>
                    </div>
                    
                    <button style="width: 100%; padding: 14px; background: var(--dk); color: #fff; border: none; border-radius: 12px; font-weight: 800; font-size: 0.9rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">Submit Check-in Data</button>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Add Food Modal/Sheet -->
<div class="nutri-modal-overlay" id="addFoodModal">
    <div class="nutri-modal-content">
        <div class="nutri-modal-header">
            <h2>Add Food</h2>
            <button class="close-modal">&times;</button>
        </div>
        
        <input type="text" class="food-search-input" placeholder="Search foods... e.g. Dosa, Chicken">
        
        <div class="search-results">
            <div class="search-result-item">
                <div>
                    <div class="food-name">Dosa Batter</div>
                    <div class="food-details">80g · P: 3.2g · C: 25.6g · F: 0.6g</div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="food-cal">121 Kcal</span>
                    <button class="search-result-add">Add</button>
                </div>
            </div>
            
            <div class="search-result-item">
                <div>
                    <div class="food-name">Chicken Breast</div>
                    <div class="food-details">100g · P: 31g · C: 0g · F: 3.6g</div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="food-cal">165 Kcal</span>
                    <button class="search-result-add">Add</button>
                </div>
            </div>

            <div class="search-result-item">
                <div>
                    <div class="food-name">Milk (Whole)</div>
                    <div class="food-details">250ml · P: 8g · C: 12g · F: 8g</div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="food-cal">150 Kcal</span>
                    <button class="search-result-add">Add</button>
                </div>
            </div>

            <div class="search-result-item">
                <div>
                    <div class="food-name">Almonds</div>
                    <div class="food-details">30g · P: 6g · C: 6g · F: 14g</div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <span class="food-cal">164 Kcal</span>
                    <button class="search-result-add">Add</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/nutrition.js') }}?v={{ time() }}"></script>
@endpush
