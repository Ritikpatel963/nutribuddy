@php
    $featProduct = $product ?? null;
    $defaultFeaturesItems = [
        [
            'icon' => 'img/vegan-1.png',
            'title' => 'Zero Gelatin — 100% Vegetarian',
            'description' => 'Most international gummies use animal gelatin (pig or bovine). All NutriBuddy gummies use plant-based pectin. Completely safe for every Indian family regardless of dietary beliefs.',
        ],
        [
            'icon' => 'img/sug-1.png',
            'title' => 'No Refined Sugar',
            'description' => 'We sweeten with Fructooligosaccharides (FOS), a prebiotic dietary fibre derived from chicory root. FOS feeds beneficial gut bacteria, supports digestive health, provides mild sweetness with fewer calories than regular sugar, and is not considered refined sugar.',
        ],
        [
            'icon' => 'img/pro-1.png',
            'title' => 'No Artificial Colors or Flavors',
            'description' => 'Our vibrant colors come from beetroot, turmeric, and spirulina. Our fruity burst flavors come from real fruit concentrates — not synthetic flavor chemicals tied to hyperactivity in children.',
        ],
    ];

    $defaultComparisonRows = [
        ['feature' => 'Ayurvedic herbs', 'us' => 'yes', 'others' => 'no'],
        ['feature' => 'Zero Gelatin', 'us' => 'yes', 'others' => 'no'],
        ['feature' => 'No refined sugar', 'us' => 'yes', 'others' => 'no'],
        ['feature' => 'Third-party lab tested', 'us' => 'yes', 'others' => 'no'],
        ['feature' => 'Transparent batch results', 'us' => 'yes', 'others' => 'no'],
        ['feature' => 'Pediatrician approved', 'us' => 'yes', 'others' => 'no'],
        ['feature' => 'Age 2+ safe', 'us' => 'yes', 'others' => 'no'],
        ['feature' => 'Price per day', 'us' => '~₹20', 'others' => 'Varies'],
    ];

    $featuresHeading = old('features_heading', $featProduct?->features_heading ?? "WHAT'S NOT IN IT");
    $featuresTitle = old('features_title', $featProduct?->features_title ?? 'Pure as<br><span class="acc">Nature Intended</span> 🍃');
    $featuresSubtitle = old('features_subtitle', $featProduct?->features_subtitle ?? "We obsessed over every ingredient that goes in — and even more over what we keep OUT. Because your child's body deserves only the best.");

    $savedFeaturesItems = $featProduct?->features_items;
    $featuresItems = old('features_items', is_array($savedFeaturesItems) && count($savedFeaturesItems) > 0 ? $savedFeaturesItems : $defaultFeaturesItems);

    $featuresComparisonTitle = old('features_comparison_title', $featProduct?->features_comparison_title ?? 'NutriBuddy vs. Other Brands');
    $savedComparisonRows = $featProduct?->features_comparison_rows;
    $featuresComparisonRows = old('features_comparison_rows', is_array($savedComparisonRows) && count($savedComparisonRows) > 0 ? $savedComparisonRows : $defaultComparisonRows);

    $featureAssetUrl = function (?string $path, ?string $fallback = null): ?string {
        $path = trim((string) $path);
        if ($path !== '') {
            if (\Illuminate\Support\Str::startsWith($path, ['img/', 'assets/', '/img/', '/assets/'])) {
                return asset(ltrim($path, '/'));
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                return asset('storage/' . $path);
            }
        }
        if ($fallback) {
            return \Illuminate\Support\Str::startsWith($fallback, ['img/', 'assets/'])
                ? asset($fallback)
                : asset('img/' . ltrim($fallback, '/'));
        }
        return null;
    };
@endphp

<div class='card border-0 radius-12 mb-24'>
    <div class='card-header bg-base border-bottom py-16 px-24 d-flex align-items-center justify-content-between'>
        <div>
            <h5 class='card-title mb-0 d-flex align-items-center gap-2'>
                <span class='badge bg-success-50 text-success-600 p-2 rounded-2'>
                    <iconify-icon icon='lucide:leaf' class='fs-5'></iconify-icon>
                </span>
                <span>Pure as Nature Intended / What's NOT in it</span>
            </h5>
            <p class='text-secondary-light mb-0 mt-1 small'>Customize feature highlights (Zero Gelatin, No Refined Sugar, etc.) and the NutriBuddy vs Other Brands comparison table.</p>
        </div>
    </div>
    <div class='card-body p-24'>
        <div class='row g-4 mb-24'>
            <div class='col-md-4'>
                <label class='form-label fw-bold'>Section Eyebrow Tagline</label>
                <input type='text' name='features_heading' class='form-control' value='{{ $featuresHeading }}' placeholder="e.g. WHAT'S NOT IN IT">
            </div>
            <div class='col-md-8'>
                <label class='form-label fw-bold'>Main Section Title</label>
                <input type='text' name='features_title' class='form-control' value='{{ $featuresTitle }}' placeholder='e.g. Pure as Nature Intended 🍃'>
                <small class='text-muted'>HTML tags like &lt;br&gt; or &lt;span class="acc"&gt; are supported for gradient styling.</small>
            </div>
            <div class='col-12'>
                <label class='form-label fw-bold'>Section Description</label>
                <textarea name='features_subtitle' rows='2' class='form-control' placeholder="We obsessed over every ingredient that goes in...">{{ $featuresSubtitle }}</textarea>
            </div>
        </div>

        <!-- 1. FEATURE HIGHLIGHT ITEMS REPEATER -->
        <div class='border rounded-3 p-16 mb-24 bg-light-50'>
            <div class='d-flex align-items-center justify-content-between border-bottom pb-12 mb-16'>
                <div>
                    <h6 class='mb-0 fw-bold text-dark'>Ingredient Quality Highlights</h6>
                    <small class='text-secondary'>Key feature cards with icon, title, and description</small>
                </div>
                <button type='button' class='btn btn-success-600 btn-sm radius-8 d-inline-flex align-items-center gap-1' id='btnAddFeatureItem'>
                    <i class='ri-add-line'></i> Add Feature Card
                </button>
            </div>

            <div id='featuresItemsWrapper'>
                @foreach ($featuresItems as $fIndex => $fItem)
                    @php
                        $itemIcon = $fItem['icon'] ?? '';
                        $itemFallback = $defaultFeaturesItems[$fIndex % count($defaultFeaturesItems)]['icon'] ?? 'img/vegan-1.png';
                        $resolvedIcon = $featureAssetUrl($itemIcon, $itemFallback);
                    @endphp
                    <div class='feature-item-row border rounded-3 p-16 mb-16 bg-white shadow-sm'>
                        <input type='hidden' name='features_items[{{ $fIndex }}][icon]' class='feature-item-icon-input' value='{{ $itemIcon }}'>
                        <div class='row g-3 align-items-start'>
                            <div class='col-md-2 text-center'>
                                <div class='border rounded-3 bg-light d-flex align-items-center justify-content-center mx-auto overflow-hidden mb-2' style='width:64px;height:64px'>
                                    @if ($resolvedIcon)
                                        <img src='{{ $resolvedIcon }}' class='w-100 h-100 object-fit-contain p-2 feature-icon-preview' alt=''>
                                    @else
                                        <iconify-icon icon='lucide:image' class='fs-3 text-secondary'></iconify-icon>
                                    @endif
                                </div>
                                <label class='btn btn-outline-secondary btn-xs w-100'>
                                    <span>Upload Icon</span>
                                    <input type='file' name='features_item_images[{{ $fIndex }}]' class='d-none feature-item-file-input' accept='image/*'>
                                </label>
                            </div>
                            <div class='col-md-9'>
                                <input type='text' name='features_items[{{ $fIndex }}][title]' class='form-control form-control-sm mb-2 fw-semibold' value='{{ $fItem['title'] ?? '' }}' placeholder='Feature Title (e.g. Zero Gelatin — 100% Vegetarian)'>
                                <textarea name='features_items[{{ $fIndex }}][description]' rows='2' class='form-control form-control-sm' placeholder='Detailed description of this clean ingredient benefit...'>{{ $fItem['description'] ?? '' }}</textarea>
                            </div>
                            <div class='col-md-1 text-end'>
                                <button type='button' class='btn btn-outline-danger btn-sm p-1 px-2 remove-feature-item-btn' title='Remove'>
                                    <iconify-icon icon='lucide:trash-2' class='fs-6'></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 2. NUTRiBUDDY VS OTHERS COMPARISON TABLE REPEATER -->
        <div class='border rounded-3 p-16 bg-light-50'>
            <div class='d-flex align-items-center justify-content-between border-bottom pb-12 mb-16'>
                <div class='flex-grow-1 me-3'>
                    <div class='row g-2 align-items-center'>
                        <div class='col-md-5'>
                            <h6 class='mb-0 fw-bold text-dark'>Comparison Table</h6>
                        </div>
                        <div class='col-md-7'>
                            <div class='input-group input-group-sm'>
                                <span class='input-group-text bg-white fw-semibold'>Table Title:</span>
                                <input type='text' name='features_comparison_title' class='form-control form-control-sm' value='{{ $featuresComparisonTitle }}' placeholder='NutriBuddy vs. Other Brands'>
                            </div>
                        </div>
                    </div>
                </div>
                <button type='button' class='btn btn-primary-600 btn-sm radius-8 d-inline-flex align-items-center gap-1' id='btnAddComparisonRow'>
                    <i class='ri-add-line'></i> Add Comparison Row
                </button>
            </div>

            <div class='table-responsive bg-white rounded-3 border'>
                <table class='table table-sm align-middle mb-0' id='comparisonTable'>
                    <thead class='table-light'>
                        <tr>
                            <th style='width:45%;'>Feature / Attribute</th>
                            <th style='width:25%; color:var(--mn, #00A651); font-weight:700;' class='text-center'>NutriBuddy (Us)</th>
                            <th style='width:20%; color:#777;' class='text-center'>Other Brands</th>
                            <th style='width:10%;' class='text-center'>Action</th>
                        </tr>
                    </thead>
                    <tbody id='comparisonRowsWrapper'>
                        @foreach ($featuresComparisonRows as $rIndex => $cRow)
                            <tr class='comparison-row'>
                                <td>
                                    <input type='text' name='features_comparison_rows[{{ $rIndex }}][feature]' class='form-control form-control-sm' value='{{ $cRow['feature'] ?? '' }}' placeholder='e.g. Zero Gelatin' required>
                                </td>
                                <td>
                                    <select name='features_comparison_rows[{{ $rIndex }}][us]' class='form-select form-select-sm text-center'>
                                        <option value='yes' {{ strtolower($cRow['us'] ?? '') === 'yes' ? 'selected' : '' }}>✓ Yes / True</option>
                                        <option value='no' {{ strtolower($cRow['us'] ?? '') === 'no' ? 'selected' : '' }}>✗ No / False</option>
                                        @if(!in_array(strtolower($cRow['us'] ?? ''), ['yes', 'no']))
                                            <option value='{{ $cRow['us'] }}' selected>{{ $cRow['us'] }} (Custom text)</option>
                                        @endif
                                        <option value='~₹20'>~₹20 (Daily price)</option>
                                    </select>
                                </td>
                                <td>
                                    <select name='features_comparison_rows[{{ $rIndex }}][others]' class='form-select form-select-sm text-center'>
                                        <option value='no' {{ strtolower($cRow['others'] ?? '') === 'no' ? 'selected' : '' }}>✗ No / False</option>
                                        <option value='yes' {{ strtolower($cRow['others'] ?? '') === 'yes' ? 'selected' : '' }}>✓ Yes / True</option>
                                        @if(!in_array(strtolower($cRow['others'] ?? ''), ['yes', 'no']))
                                            <option value='{{ $cRow['others'] }}' selected>{{ $cRow['others'] }} (Custom text)</option>
                                        @endif
                                        <option value='Varies'>Varies (Pricing)</option>
                                    </select>
                                </td>
                                <td class='text-center'>
                                    <button type='button' class='btn btn-outline-danger btn-sm p-1 px-2 remove-comp-row-btn' title='Remove Row'>
                                        <iconify-icon icon='lucide:trash-2' class='fs-6'></iconify-icon>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Feature item cards repeater
    const itemsWrapper = document.getElementById('featuresItemsWrapper');
    const addFeatureBtn = document.getElementById('btnAddFeatureItem');
    let featureItemCount = {{ count($featuresItems) }};

    if (addFeatureBtn && itemsWrapper) {
        addFeatureBtn.addEventListener('click', function () {
            const index = featureItemCount++;
            const div = document.createElement('div');
            div.className = 'feature-item-row border rounded-3 p-16 mb-16 bg-white shadow-sm';
            div.innerHTML = `
                <input type='hidden' name='features_items[\${index}][icon]' class='feature-item-icon-input' value=''>
                <div class='row g-3 align-items-start'>
                    <div class='col-md-2 text-center'>
                        <div class='border rounded-3 bg-light d-flex align-items-center justify-content-center mx-auto overflow-hidden mb-2' style='width:64px;height:64px'>
                            <iconify-icon icon='lucide:image' class='fs-3 text-secondary'></iconify-icon>
                        </div>
                        <label class='btn btn-outline-secondary btn-xs w-100'>
                            <span>Upload Icon</span>
                            <input type='file' name='features_item_images[\${index}]' class='d-none feature-item-file-input' accept='image/*'>
                        </label>
                    </div>
                    <div class='col-md-9'>
                        <input type='text' name='features_items[\${index}][title]' class='form-control form-control-sm mb-2 fw-semibold' placeholder='Feature Title (e.g. Zero Gelatin)'>
                        <textarea name='features_items[\${index}][description]' rows='2' class='form-control form-control-sm' placeholder='Description of this clean ingredient benefit...'></textarea>
                    </div>
                    <div class='col-md-1 text-end'>
                        <button type='button' class='btn btn-outline-danger btn-sm p-1 px-2 remove-feature-item-btn' title='Remove'>
                            <iconify-icon icon='lucide:trash-2' class='fs-6'></iconify-icon>
                        </button>
                    </div>
                </div>
            `;
            itemsWrapper.appendChild(div);
            attachFeatureItemListeners(div);
        });

        itemsWrapper.querySelectorAll('.feature-item-row').forEach(attachFeatureItemListeners);
    }

    function attachFeatureItemListeners(row) {
        const removeBtn = row.querySelector('.remove-feature-item-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                row.remove();
            });
        }
        const fileInput = row.querySelector('.feature-item-file-input');
        if (fileInput) {
            fileInput.addEventListener('change', function () {
                if (fileInput.files && fileInput.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const box = row.querySelector('div[style*="width:64px"]');
                        if (box) {
                            box.innerHTML = `<img src="\${e.target.result}" class="w-100 h-100 object-fit-contain p-1">`;
                        }
                    };
                    reader.readAsDataURL(fileInput.files[0]);
                }
            });
        }
    }

    // Comparison rows repeater
    const compWrapper = document.getElementById('comparisonRowsWrapper');
    const addCompBtn = document.getElementById('btnAddComparisonRow');
    let compRowCount = {{ count($featuresComparisonRows) }};

    if (addCompBtn && compWrapper) {
        addCompBtn.addEventListener('click', function () {
            const index = compRowCount++;
            const tr = document.createElement('tr');
            tr.className = 'comparison-row';
            tr.innerHTML = `
                <td>
                    <input type='text' name='features_comparison_rows[\${index}][feature]' class='form-control form-control-sm' placeholder='Feature attribute' required>
                </td>
                <td>
                    <select name='features_comparison_rows[\${index}][us]' class='form-select form-select-sm text-center'>
                        <option value='yes' selected>✓ Yes / True</option>
                        <option value='no'>✗ No / False</option>
                        <option value='~₹20'>~₹20 (Daily price)</option>
                    </select>
                </td>
                <td>
                    <select name='features_comparison_rows[\${index}][others]' class='form-select form-select-sm text-center'>
                        <option value='no' selected>✗ No / False</option>
                        <option value='yes'>✓ Yes / True</option>
                        <option value='Varies'>Varies (Pricing)</option>
                    </select>
                </td>
                <td class='text-center'>
                    <button type='button' class='btn btn-outline-danger btn-sm p-1 px-2 remove-comp-row-btn' title='Remove Row'>
                        <iconify-icon icon='lucide:trash-2' class='fs-6'></iconify-icon>
                    </button>
                </td>
            `;
            compWrapper.appendChild(tr);
            attachCompRowListeners(tr);
        });

        compWrapper.querySelectorAll('.comparison-row').forEach(attachCompRowListeners);
    }

    function attachCompRowListeners(row) {
        const removeBtn = row.querySelector('.remove-comp-row-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function () {
                row.remove();
            });
        }
    }
});
</script>
