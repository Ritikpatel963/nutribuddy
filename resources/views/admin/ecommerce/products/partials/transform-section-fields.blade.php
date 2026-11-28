@php
    $transformProduct = $product ?? null;
    $transformDefaults = [
        ['image' => 'img/immune.png', 'title' => 'Stronger Immunity', 'description' => 'Kids fall sick less often. Parents report 60% fewer sick days in the first 3 months of consistent use.', 'week' => 'Visible by Week 3'],
        ['image' => 'img/check-height.png', 'title' => 'Height & Growth Spurt', 'description' => 'Ashwagandha + Zinc work synergistically to support natural growth hormone function and bone density.', 'week' => 'Visible by Week 8'],
        ['image' => 'img/energy-drink.png', 'title' => 'All-Day Energy', 'description' => 'No more afternoon crashes. Kids stay energetic and active through school, play, and evening activities.', 'week' => 'Visible by Week 2'],
        ['image' => 'img/mental-health.png', 'title' => 'Better Mood & Calm', 'description' => 'Adaptogenic Ashwagandha reduces cortisol — kids feel less stressed, sleep better, and wake up happier.', 'week' => 'Visible by Week 4'],
    ];
    $descriptionDefault = $transformProduct
        ? '90 days of ' . $transformProduct->name . ' — visible, measurable, life-changing results reported by thousands of parents.'
        : '90 days of consistent use — visible, measurable, life-changing results reported by thousands of parents.';
    $transformDescription = old('transform_description', $transformProduct?->transform_description ?: $descriptionDefault);
    $transformMainImage = old('transform_main_image', $transformProduct?->transform_main_image ?: '');
    $transformBeforeImage = old('transform_before_image', $transformProduct?->transform_before_image ?: ($transformProduct?->transform_main_image ?: 'assets/brain/routine-before.png'));
    $transformBeforeLabel = old('transform_before_label', $transformProduct?->transform_before_label ?: 'Before');
    $transformBeforeSublabel = old('transform_before_sublabel', $transformProduct?->transform_before_sublabel ?: 'Getting started');
    $transformAfterImage = old('transform_after_image', $transformProduct?->transform_after_image ?: 'assets/brain/routine-after.webp');
    $transformAfterLabel = old('transform_after_label', $transformProduct?->transform_after_label ?: 'After');
    $transformAfterSublabel = old('transform_after_sublabel', $transformProduct?->transform_after_sublabel ?: 'A shared little habit');
    $savedTransformResults = $transformProduct?->transform_results;
    $transformResults = old('transform_results', is_array($savedTransformResults) ? $savedTransformResults : $transformDefaults);
    $transformImageUrl = function (?string $path, ?string $fallback = null): ?string {
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
    <div class='card-header bg-base border-bottom py-16 px-24'>
        <h5 class='card-title mb-0'>Real Results / Transformation Section</h5>
        <p class='text-secondary-light mb-0 mt-1'>Controls the paragraph, dynamic Before & After split slider images, and result cards on this product detail page.</p>
    </div>
    <div class='card-body p-24'>
        <div class='mb-20'>
            <label class='form-label fw-bold'>Section Paragraph</label>
            <textarea name='transform_description' rows='2' class='form-control' maxlength='1000' placeholder='Section introduction paragraph'>{{ $transformDescription }}</textarea>
            @error('transform_description')<span class='text-danger small d-block'>{{ $message }}</span>@enderror
        </div>

        <!-- DYNAMIC BEFORE & AFTER SLIDER IMAGES -->
        <div class='border rounded-3 p-20 mb-24 bg-light-50'>
            <div class='d-flex align-items-center justify-content-between mb-16'>
                <div>
                    <h6 class='mb-1 fw-bold text-dark'>Interactive Before & After Comparison Visuals</h6>
                    <small class='text-secondary'>Upload custom Before and After images for the draggable PDP comparison slider.</small>
                </div>
            </div>
            <div class='row g-4'>
                <!-- BEFORE IMAGE -->
                <div class='col-md-6'>
                    <div class='bg-white p-16 rounded-3 border h-100'>
                        <div class='d-flex align-items-center justify-content-between mb-12'>
                            <span class='badge bg-danger-50 text-danger-600 px-12 py-6 rounded-pill fw-bold'>
                                <i class='ri-history-line me-1'></i> Before State Visual
                            </span>
                            <small class='text-muted'>Starting routine</small>
                        </div>
                        <div class='mb-12 text-center bg-light p-2 rounded-2 border' style='min-height:160px; display:flex; align-items:center; justify-content:center;'>
                            @php
                                $beforePreview = $transformImageUrl($transformBeforeImage, 'assets/brain/routine-before.png');
                            @endphp
                            @if ($beforePreview)
                                <img src='{{ $beforePreview }}' class='rounded object-fit-cover w-100' style='max-height:150px;' alt='Before Preview'>
                            @else
                                <div class='text-muted small py-4'><iconify-icon icon='lucide:image' class='fs-1 opacity-50'></iconify-icon><br>Upload Before Image</div>
                            @endif
                        </div>
                        <input type='hidden' name='transform_before_image' value='{{ $transformBeforeImage }}'>
                        <label class='form-label small fw-bold mb-1'>Upload New Before Image</label>
                        <input type='file' name='transform_before_image_file' class='form-control form-control-sm mb-12' accept='image/jpeg,image/png,image/webp'>
                        <div class='row g-2'>
                            <div class='col-6'>
                                <label class='form-label small fw-semibold mb-1'>Badge Title</label>
                                <input type='text' name='transform_before_label' class='form-control form-control-sm' value='{{ $transformBeforeLabel }}' placeholder='e.g. Before'>
                            </div>
                            <div class='col-6'>
                                <label class='form-label small fw-semibold mb-1'>Badge Sub-text</label>
                                <input type='text' name='transform_before_sublabel' class='form-control form-control-sm' value='{{ $transformBeforeSublabel }}' placeholder='e.g. Getting started'>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AFTER IMAGE -->
                <div class='col-md-6'>
                    <div class='bg-white p-16 rounded-3 border h-100'>
                        <div class='d-flex align-items-center justify-content-between mb-12'>
                            <span class='badge bg-success-50 text-success-600 px-12 py-6 rounded-pill fw-bold'>
                                <i class='ri-sparkling-line me-1'></i> After State Visual
                            </span>
                            <small class='text-muted'>Transformed habit</small>
                        </div>
                        <div class='mb-12 text-center bg-light p-2 rounded-2 border' style='min-height:160px; display:flex; align-items:center; justify-content:center;'>
                            @php
                                $afterPreview = $transformImageUrl($transformAfterImage, 'assets/brain/routine-after.webp');
                            @endphp
                            @if ($afterPreview)
                                <img src='{{ $afterPreview }}' class='rounded object-fit-cover w-100' style='max-height:150px;' alt='After Preview'>
                            @else
                                <div class='text-muted small py-4'><iconify-icon icon='lucide:image' class='fs-1 opacity-50'></iconify-icon><br>Upload After Image</div>
                            @endif
                        </div>
                        <input type='hidden' name='transform_after_image' value='{{ $transformAfterImage }}'>
                        <label class='form-label small fw-bold mb-1'>Upload New After Image</label>
                        <input type='file' name='transform_after_image_file' class='form-control form-control-sm mb-12' accept='image/jpeg,image/png,image/webp'>
                        <div class='row g-2'>
                            <div class='col-6'>
                                <label class='form-label small fw-semibold mb-1'>Badge Title</label>
                                <input type='text' name='transform_after_label' class='form-control form-control-sm' value='{{ $transformAfterLabel }}' placeholder='e.g. After'>
                            </div>
                            <div class='col-6'>
                                <label class='form-label small fw-semibold mb-1'>Badge Sub-text</label>
                                <input type='text' name='transform_after_sublabel' class='form-control form-control-sm' value='{{ $transformAfterSublabel }}' placeholder='e.g. A shared little habit'>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type='hidden' name='transform_main_image' value='{{ $transformMainImage }}'>

        <div class='border rounded-3 mt-4'>
            <div class='border-bottom py-14 px-16 d-flex align-items-center justify-content-between'>
                <div>
                    <h6 class='mb-0'>Right-side Result Cards</h6>
                    <small class='text-secondary-light'>Icon, heading, paragraph, and optional timing label.</small>
                </div>
                <button type='button' class='btn btn-success-600 btn-sm radius-8' data-add-transform-result>Add Result</button>
            </div>
            <div class='p-16' data-transform-results>
                @foreach ($transformResults as $index => $result)
                    @include('admin.ecommerce.products.partials.transform-result-row', compact('index', 'result', 'transformImageUrl', 'transformDefaults'))
                @endforeach
            </div>
        </div>
    </div>
</div>

<template data-transform-result-template>
    <div class='transform-result-row border rounded-3 p-3 mb-3 bg-light'>
        <input type='hidden' data-field='image' value=''>
        <div class='row g-3 align-items-start'>
            <div class='col-md-2'>
                <div class='border rounded-3 bg-white d-flex align-items-center justify-content-center' style='width:72px;height:72px'><iconify-icon icon='lucide:image'></iconify-icon></div>
                <input type='file' data-field='image-file' class='form-control form-control-sm mt-2' accept='image/jpeg,image/png,image/webp'>
            </div>
            <div class='col-md-9'>
                <input type='text' data-field='title' class='form-control mb-2' maxlength='150' placeholder='Result heading'>
                <textarea data-field='description' rows='2' class='form-control mb-2' maxlength='1000' placeholder='Result paragraph'></textarea>
                <input type='text' data-field='week' class='form-control' maxlength='80' placeholder='Optional: Visible by Week 3'>
            </div>
            <div class='col-md-1 text-end'><button type='button' class='btn btn-outline-danger-600 btn-sm' data-remove-transform-result><i class='fas fa-trash'></i></button></div>
        </div>
    </div>
</template>

<script>
    (() => {
        const list = document.querySelector('[data-transform-results]');
        const addButton = document.querySelector('[data-add-transform-result]');
        const template = document.querySelector('[data-transform-result-template]');
        if (!list || !addButton || !template) return;

        const reindex = () => {
            list.querySelectorAll('.transform-result-row').forEach((row, index) => {
                row.querySelector('[data-field=image]').name = `transform_results[${index}][image]`;
                row.querySelector('[data-field=image-file]').name = `transform_result_images[${index}]`;
                row.querySelector('[data-field=title]').name = `transform_results[${index}][title]`;
                row.querySelector('[data-field=description]').name = `transform_results[${index}][description]`;
                row.querySelector('[data-field=week]').name = `transform_results[${index}][week]`;
            });
        };

        addButton.addEventListener('click', () => {
            list.appendChild(template.content.firstElementChild.cloneNode(true));
            reindex();
        });

        list.addEventListener('click', event => {
            const button = event.target.closest('[data-remove-transform-result]');
            if (!button) return;
            button.closest('.transform-result-row').remove();
            reindex();
        });

        reindex();
    })();
</script>
