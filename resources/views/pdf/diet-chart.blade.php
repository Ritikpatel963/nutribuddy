<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>NutriBuddy Personalized Diet Chart - {{ $child->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 11px; color: #333; line-height: 1.4; margin: 0; padding: 15px; }
        .header { border-bottom: 2px solid #7B2CBF; padding-bottom: 10px; margin-bottom: 15px; }
        .header h1 { color: #7B2CBF; font-size: 20px; margin: 0 0 5px; }
        .header p { margin: 0; color: #666; font-size: 11px; }
        .meta-table { width: 100%; margin-bottom: 15px; background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 6px; }
        .meta-table td { padding: 6px 10px; font-size: 11px; }
        .meta-label { font-weight: bold; color: #495057; }
        .day-block { margin-bottom: 15px; page-break-inside: avoid; }
        .day-title { background: #7B2CBF; color: white; padding: 5px 10px; font-size: 12px; font-weight: bold; border-radius: 4px; margin-bottom: 6px; }
        .meal-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .meal-table th { background: #f1f3f5; color: #495057; font-size: 10px; text-transform: uppercase; padding: 5px; text-align: left; border: 1px solid #dee2e6; }
        .meal-table td { padding: 6px 8px; font-size: 10px; border: 1px solid #dee2e6; vertical-align: top; }
        .slot-name { font-weight: bold; color: #7B2CBF; width: 18%; }
        .dish-name { font-weight: bold; color: #212529; }
        .dish-meta { color: #6c757d; font-size: 9px; }
        .dish-nutrients { color: #2b8a3e; font-size: 9px; }
        .tips-box { background: #e6fcf5; border-left: 3px solid #0ca678; padding: 8px 10px; margin-bottom: 15px; border-radius: 4px; }
        .tips-title { font-weight: bold; color: #0ca678; font-size: 11px; margin-bottom: 4px; }
        .gaps-table { width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 15px; }
        .gaps-table th { background: #f8f9fa; border: 1px solid #dee2e6; padding: 4px 6px; font-size: 9px; text-align: left; }
        .gaps-table td { border: 1px solid #dee2e6; padding: 4px 6px; font-size: 9px; }
        .footer { text-align: center; font-size: 9px; color: #adb5bd; border-top: 1px solid #dee2e6; padding-top: 8px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>NutriBuddy Personalized Pediatric Diet Plan</h1>
        <p>A science-backed nutritional rhythm designed for {{ $child->name }}</p>
    </div>

    <table class="meta-table">
        <tr>
            <td><span class="meta-label">Child:</span> {{ $child->name }} ({{ $child->age }} yrs, {{ ucfirst($child->sex) }})</td>
            <td><span class="meta-label">Weight:</span> {{ $assessment->current_weight_kg ?? 'N/A' }} kg</td>
            <td><span class="meta-label">Height:</span> {{ $assessment->current_height_cm ?? 'N/A' }} cm</td>
        </tr>
        <tr>
            <td><span class="meta-label">Diet:</span> {{ ucfirst($assessment->diet_type ?? 'Vegetarian') }}</td>
            <td><span class="meta-label">Region:</span> {{ ucfirst($assessment->region ?? 'Pan India') }}</td>
            <td><span class="meta-label">Allergies:</span> {{ is_array($assessment->allergies) ? implode(', ', $assessment->allergies) : ($assessment->allergies ?? 'None') }}</td>
        </tr>
    </table>

    @if(filled($dietPlan->review_notes))
        <div class="tips-box">
            <div class="tips-title">Nutritionist Verification Note</div>
            <div style="font-size: 10px;">{{ $dietPlan->review_notes }}</div>
        </div>
    @endif

    {{-- MEAL SCHEDULE --}}
    @foreach(array_slice($formattedPlan, 0, 7) as $dayData)
        <div class="day-block">
            <div class="day-title">{{ $dayData['label'] }}, {{ \Carbon\Carbon::parse($dayData['date'])->format('d M Y') }}</div>
            <table class="meal-table">
                <thead>
                    <tr>
                        <th style="width: 18%;">Slot</th>
                        <th style="width: 52%;">Dish & Portion</th>
                        <th style="width: 30%;">Nutrition</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['Breakfast', 'Morning Snack', 'Lunch', 'Evening Snack', 'Dinner'] as $slot)
                        @if(isset($dayData['meals'][$slot]))
                            @php $mealData = $dayData['meals'][$slot]; @endphp
                            <tr>
                                <td class="slot-name">{{ $slot }}</td>
                                <td>
                                    @foreach($mealData['items'] as $item)
                                        <div class="dish-name">{{ $item['food_name'] }}</div>
                                        <div class="dish-meta">Serving: {{ $item['portion_g'] }}g</div>
                                        @if(!empty($item['pairing_notes']))
                                            <div style="color: #d9480f; font-size: 9px; margin-top: 2px;">
                                                Tip: {{ implode('; ', $item['pairing_notes']) }}
                                            </div>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @if(isset($mealData['total_nutrients']))
                                        <div class="dish-nutrients">
                                            {{ round($mealData['total_nutrients']['energy_kcal'] ?? 0) }} kcal | 
                                            {{ round($mealData['total_nutrients']['protein_g'] ?? 0, 1) }}g protein
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach

    {{-- NUTRIENT GAPS --}}
    @if(!empty($nutrientGaps))
        <div style="page-break-inside: avoid; margin-top: 15px;">
            <div style="font-weight: bold; font-size: 12px; color: #7B2CBF; margin-bottom: 5px;">Daily Nutrient Targets (ICMR 2020)</div>
            <table class="gaps-table">
                <thead>
                    <tr>
                        <th>Nutrient</th>
                        <th>Target</th>
                        <th>Achieved</th>
                        <th>Adequacy</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(array_slice($nutrientGaps, 0, 8) as $nutrient => $gap)
                        <tr>
                            <td>{{ ucfirst(str_replace('_', ' ', $nutrient)) }}</td>
                            <td>{{ number_format($gap['target'] ?? 0, 1) }}</td>
                            <td>{{ number_format($gap['absorbed_amount'] ?? 0, 1) }}</td>
                            <td>{{ number_format($gap['adequacy_percent'] ?? 0, 1) }}%</td>
                            <td><strong>{{ $gap['gap_category'] ?? 'Met' }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        Generated by NutriBuddy (Pediatric Nutrition Platform) &bull; For queries contact support@nutribuddy.in
    </div>
</body>
</html>
