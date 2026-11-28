@php
    $formProduct = $product ?? null;
    $formulaEyebrow = old('formula_eyebrow', $formProduct?->formula_eyebrow ?? 'Meet your nutrition buddy');
    $formulaHeading = old('formula_heading', $formProduct?->formula_heading ?? 'Little gummy. <em>Thoughtful formula.</em>');
    $formulaSubheading = old('formula_subheading', $formProduct?->formula_subheading ?? ($formProduct?->name ?? 'Brain Booster Gummies'));
    $formulaTag = old('formula_tag', $formProduct?->formula_tag ?? 'Ayurveda + nutraceutical');

    $computeDefaultBgWord = function ($productModel) {
        if (! $productModel) {
            return 'BRAIN';
        }
        $name = trim((string) ($productModel->name ?? ''));
        $lower = strtolower($name);
        if (str_contains($lower, 'brain')) {
            return 'BRAIN';
        }
        if (str_contains($lower, 'immunity')) {
            return 'IMMUNE';
        }
        if (str_contains($lower, 'kadha')) {
            return 'KADHA';
        }
        if (str_contains($lower, 'calcium')) {
            return 'CALCIUM';
        }
        if (str_contains($lower, 'sleep')) {
            return 'SLEEP';
        }
        if (str_contains($lower, 'vitamin')) {
            return 'VITAMIN';
        }
        $words = preg_split('/\s+/', $name);
        foreach ($words as $w) {
            $clean = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $w));
            if ($clean !== '' && ! in_array($clean, ['NUTRIBUDDY', 'KIDS', 'THE', 'ORGANIC', 'SUPER', 'GUMMIES', 'GUMMY', 'PACK', 'COMBO'])) {
                return mb_substr($clean, 0, 10);
            }
        }
        return 'BRAIN';
    };

    $defaultBgWord = $computeDefaultBgWord($formProduct);
    $formulaBgWord = old('formula_bg_word', $formProduct?->formula_bg_word ?? $defaultBgWord);
    $formulaImage = old('formula_image', $formProduct?->formula_image ?? 'assets/brain/hero-reveal.webp');

    $statLeftNum = old('formula_stat_left_num', $formProduct?->formula_stat_left_num ?? '2');
    $statLeftTitle = old('formula_stat_left_title', $formProduct?->formula_stat_left_title ?? 'Ayurvedic botanicals');
    $statLeftSub = old('formula_stat_left_sub', $formProduct?->formula_stat_left_sub ?? 'Brahmi & Shankhpushpi');

    $statRightNum = old('formula_stat_right_num', $formProduct?->formula_stat_right_num ?? '20 mg');
    $statRightTitle = old('formula_stat_right_title', $formProduct?->formula_stat_right_title ?? 'algal DHA');
    $statRightSub = old('formula_stat_right_sub', $formProduct?->formula_stat_right_sub ?? 'in every gummy');

    $defaultBadges = ['No preservatives', 'No added colours', 'No refined sugar', '100% vegetarian'];
    $savedBadges = $formProduct?->formula_badges;
    $formulaBadges = old('formula_badges', is_array($savedBadges) && count($savedBadges) > 0 ? $savedBadges : $defaultBadges);

    $formulaCaption = old('formula_caption', $formProduct?->formula_caption ?? 'To complement a varied diet, when supplementation is needed.');
    $btnPrimaryText = old('formula_button_primary_text', $formProduct?->formula_button_primary_text ?? 'Choose your plan');
    $btnPrimaryUrl = old('formula_button_primary_url', $formProduct?->formula_button_primary_url ?? '#pdpAddToCartBtn');
    $btnSecondaryText = old('formula_button_secondary_text', $formProduct?->formula_button_secondary_text ?? 'Explore ingredients');
    $btnSecondaryUrl = old('formula_button_secondary_url', $formProduct?->formula_button_secondary_url ?? '#nb-ingredients');
    $formulaFootnote = old('formula_footnote', $formProduct?->formula_footnote ?? 'One-time purchase or Subscribe & Save');

    $formulaImageUrl = function (?string $path, ?string $fallback = null): ?string {
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
                <span class='badge bg-info-50 text-info-600 p-2 rounded-2'>
                    <iconify-icon icon='lucide:sparkles' class='fs-5'></iconify-icon>
                </span>
                <span>Meet Your Nutrition Buddy / Thoughtful Formula Section</span>
            </h5>
            <p class='text-secondary-light mb-0 mt-1 small'>Customize the formula showcase banner, background watermark, hero bottle, left/right callout stats, and quality badges.</p>
        </div>
    </div>
    <div class='card-body p-24'>
        <!-- SECTION HEADINGS -->
        <div class='row g-3 mb-20'>
            <div class='col-md-4'>
                <label class='form-label fw-bold'>Eyebrow Tagline</label>
                <input type='text' name='formula_eyebrow' class='form-control' value='{{ $formulaEyebrow }}' placeholder='Meet your nutrition buddy'>
            </div>
            <div class='col-md-4'>
                <label class='form-label fw-bold'>Main Heading</label>
                <input type='text' name='formula_heading' class='form-control' value='{{ $formulaHeading }}' placeholder='Little gummy. &lt;em&gt;Thoughtful formula.&lt;/em&gt;'>
                <small class='text-muted'>Use &lt;em&gt;text&lt;/em&gt; for highlighted italic script</small>
            </div>
            <div class='col-md-4'>
                <label class='form-label fw-bold'>Product Subheading / Name</label>
                <input type='text' name='formula_subheading' class='form-control' value='{{ $formulaSubheading }}' placeholder='Product Name'>
            </div>
        </div>

        <div class='row g-3 mb-24'>
            <div class='col-md-6'>
                <label class='form-label fw-bold'>Pill Badge Tag</label>
                <input type='text' name='formula_tag' class='form-control' value='{{ $formulaTag }}' placeholder='Ayurveda + nutraceutical'>
            </div>
            <div class='col-md-6'>
                <label class='form-label fw-bold d-flex align-items-center justify-content-between'>
                    <span>Background Watermark Word</span>
                    <span class='badge bg-primary-50 text-primary-600 fw-semibold small'>Outline typography behind bottle</span>
                </label>
                <input type='text' name='formula_bg_word' class='form-control text-uppercase' value='{{ $formulaBgWord }}' placeholder='e.g. BRAIN, IMMUNE, KADHA' maxlength='20'>
                <small class='text-muted'>Big stylized outline word displayed behind the product bottle (e.g. BRAIN, IMMUNE, VITALITY, CALCIUM).</small>
            </div>
        </div>

        <!-- HERO STAGE & STAT CALLOUTS -->
        <div class='border rounded-3 p-20 mb-24 bg-light-50'>
            <h6 class='fw-bold text-dark mb-16'>Hero Stage Showcase &amp; Stat Callouts</h6>
            <div class='row g-4 align-items-center'>
                <!-- LEFT STAT -->
                <div class='col-md-4'>
                    <div class='bg-white p-16 rounded-3 border h-100'>
                        <span class='badge bg-primary-50 text-primary-600 px-8 py-4 mb-2 rounded'>Left Stat Callout</span>
                        <div class='mb-2'>
                            <label class='form-label small fw-bold mb-1'>Metric / Number</label>
                            <input type='text' name='formula_stat_left_num' class='form-control form-control-sm fw-bold' value='{{ $statLeftNum }}' placeholder='2'>
                        </div>
                        <div class='mb-2'>
                            <label class='form-label small fw-bold mb-1'>Title</label>
                            <input type='text' name='formula_stat_left_title' class='form-control form-control-sm' value='{{ $statLeftTitle }}' placeholder='Ayurvedic botanicals'>
                        </div>
                        <div>
                            <label class='form-label small fw-bold mb-1'>Sub-text</label>
                            <input type='text' name='formula_stat_left_sub' class='form-control form-control-sm' value='{{ $statLeftSub }}' placeholder='Brahmi &amp; Shankhpushpi'>
                        </div>
                    </div>
                </div>

                <!-- CENTER HERO IMAGE -->
                <div class='col-md-4 text-center'>
                    <div class='bg-white p-16 rounded-3 border h-100'>
                        <span class='badge bg-info-50 text-info-600 px-8 py-4 mb-2 rounded'>Center Hero Bottle / Visual</span>
                        <div class='my-2 p-2 bg-light rounded-2 border d-flex align-items-center justify-content-center' style='min-height:140px;'>
                            @php
                                $heroPreview = $formulaImageUrl($formulaImage, 'assets/brain/hero-reveal.webp');
                            @endphp
                            @if ($heroPreview)
                                <img src='{{ $heroPreview }}' class='object-fit-contain w-100' style='max-height:130px;' alt='Formula Preview'>
                            @endif
                        </div>
                        <input type='hidden' name='formula_image' value='{{ $formulaImage }}'>
                        <input type='file' name='formula_image_file' class='form-control form-control-sm' accept='image/jpeg,image/png,image/webp'>
                    </div>
                </div>

                <!-- RIGHT STAT -->
                <div class='col-md-4'>
                    <div class='bg-white p-16 rounded-3 border h-100'>
                        <span class='badge bg-primary-50 text-primary-600 px-8 py-4 mb-2 rounded'>Right Stat Callout</span>
                        <div class='mb-2'>
                            <label class='form-label small fw-bold mb-1'>Metric / Number</label>
                            <input type='text' name='formula_stat_right_num' class='form-control form-control-sm fw-bold' value='{{ $statRightNum }}' placeholder='20 mg'>
                        </div>
                        <div class='mb-2'>
                            <label class='form-label small fw-bold mb-1'>Title</label>
                            <input type='text' name='formula_stat_right_title' class='form-control form-control-sm' value='{{ $statRightTitle }}' placeholder='algal DHA'>
                        </div>
                        <div>
                            <label class='form-label small fw-bold mb-1'>Sub-text</label>
                            <input type='text' name='formula_stat_right_sub' class='form-control form-control-sm' value='{{ $statRightSub }}' placeholder='in every gummy'>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 QUALITY BADGES -->
        <div class='border rounded-3 p-16 mb-24 bg-light-50'>
            <h6 class='fw-bold text-dark mb-12'>4 Quality Badges (Under Bottle)</h6>
            <div class='row g-3'>
                @for ($b = 0; $b < 4; $b++)
                    <div class='col-md-3'>
                        <div class='bg-white p-12 rounded-2 border'>
                            <label class='form-label small fw-bold mb-1'>Badge {{ $b + 1 }}</label>
                            <input type='text' name='formula_badges[{{ $b }}]' class='form-control form-control-sm' value='{{ $formulaBadges[$b] ?? ($defaultBadges[$b] ?? '') }}' placeholder='e.g. {{ $defaultBadges[$b] ?? '' }}'>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <!-- CAPTION & BUTTON ACTIONS -->
        <div class='row g-3'>
            <div class='col-12'>
                <label class='form-label fw-bold'>Dietary Caption</label>
                <input type='text' name='formula_caption' class='form-control' value='{{ $formulaCaption }}' placeholder='To complement a varied diet, when supplementation is needed.'>
            </div>
            <div class='col-md-6'>
                <div class='p-16 border rounded-3 bg-white'>
                    <label class='form-label fw-bold small mb-2'>Primary Action Button</label>
                    <div class='row g-2'>
                        <div class='col-6'>
                            <input type='text' name='formula_button_primary_text' class='form-control form-control-sm' value='{{ $btnPrimaryText }}' placeholder='Choose your plan'>
                        </div>
                        <div class='col-6'>
                            <input type='text' name='formula_button_primary_url' class='form-control form-control-sm' value='{{ $btnPrimaryUrl }}' placeholder='#pdpAddToCartBtn'>
                        </div>
                    </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class='p-16 border rounded-3 bg-white'>
                    <label class='form-label fw-bold small mb-2'>Secondary Action Button</label>
                    <div class='row g-2'>
                        <div class='col-6'>
                            <input type='text' name='formula_button_secondary_text' class='form-control form-control-sm' value='{{ $btnSecondaryText }}' placeholder='Explore ingredients'>
                        </div>
                        <div class='col-6'>
                            <input type='text' name='formula_button_secondary_url' class='form-control form-control-sm' value='{{ $btnSecondaryUrl }}' placeholder='#nb-ingredients'>
                        </div>
                    </div>
                </div>
            </div>
            <div class='col-12'>
                <label class='form-label fw-bold'>Footer Note</label>
                <input type='text' name='formula_footnote' class='form-control' value='{{ $formulaFootnote }}' placeholder='One-time purchase or Subscribe & Save'>
            </div>
        </div>
    </div>
</div>
