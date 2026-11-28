@php
    $formProduct = $product ?? null;
    $bannerImage = old('banner_image', $formProduct?->banner_image);

    $bannerImageUrl = function (?string $path): ?string {
        $path = trim((string) $path);
        if ($path !== '') {
            if (\Illuminate\Support\Str::startsWith($path, ['img/', 'assets/', '/img/', '/assets/'])) {
                return asset(ltrim($path, '/'));
            }
            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                return asset('storage/' . $path);
            }
        }
        return null;
    };
    $currentBannerUrl = $bannerImageUrl($bannerImage);
@endphp

<div class="card border-0 radius-12 mb-24 shadow-sm">
    <div class="card-header bg-base border-bottom d-flex justify-content-between align-items-center py-16 px-24">
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-success-50 text-success-600 p-2 rounded-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                <iconify-icon icon="lucide:image" class="fs-4"></iconify-icon>
            </span>
            <div>
                <h5 class="card-title mb-0">Product Showcase Banner (Standalone Image Section)</h5>
                <small class="text-secondary">Upload an optional promotional banner, infographic, or certificate image. Displayed prominently across the product detail page.</small>
            </div>
        </div>
    </div>

    <div class="card-body p-24">
        <div class="row g-4 align-items-center">
            <div class="col-md-7">
                <label class="form-label fw-bold">Upload Showcase Banner Image</label>
                <input type="file" name="banner_image_file" id="bannerImageFileInput" class="form-control" accept="image/*" onchange="previewShowcaseBanner(this)">
                <small class="text-muted d-block mt-1">Recommended size: 1200x500px or larger. PNG, JPG, or WebP up to 5MB.</small>
                
                <input type="hidden" name="banner_image" value="{{ $bannerImage }}">
                <input type="hidden" name="delete_banner_image" id="deleteBannerImageInput" value="0">
            </div>

            <div class="col-md-5">
                <label class="form-label fw-bold small text-muted">Banner Preview</label>
                <div class="border radius-12 p-12 bg-light-50 text-center position-relative" style="min-height: 140px; display: flex; align-items: center; justify-content: center;">
                    <div id="bannerImagePlaceholder" style="{{ $currentBannerUrl ? 'display:none;' : '' }}" class="text-muted small py-4">
                        <iconify-icon icon="lucide:image" class="fs-1 opacity-25 d-block mb-1"></iconify-icon>
                        <span>No showcase banner uploaded</span>
                    </div>

                    <img id="bannerImagePreviewEl" src="{{ $currentBannerUrl ?? '' }}" alt="Banner preview"
                        class="img-fluid rounded radius-8 shadow-xs w-100 object-fit-cover"
                        style="max-height: 180px; {{ $currentBannerUrl ? '' : 'display:none;' }}">

                    <button type="button" id="removeBannerBtn" class="btn btn-danger btn-sm p-1 px-2 position-absolute top-0 end-0 m-2 shadow-sm radius-6"
                        style="{{ $currentBannerUrl ? '' : 'display:none;' }}"
                        onclick="clearShowcaseBanner()" title="Remove Banner">
                        <iconify-icon icon="lucide:trash-2" style="font-size: 14px;"></iconify-icon>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewShowcaseBanner(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) {
            const previewEl = document.getElementById('bannerImagePreviewEl');
            const placeholder = document.getElementById('bannerImagePlaceholder');
            const removeBtn = document.getElementById('removeBannerBtn');
            const deleteInput = document.getElementById('deleteBannerImageInput');

            previewEl.src = e.target.result;
            previewEl.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
            if (removeBtn) removeBtn.style.display = 'inline-flex';
            if (deleteInput) deleteInput.value = '0';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function clearShowcaseBanner() {
    const input = document.getElementById('bannerImageFileInput');
    const previewEl = document.getElementById('bannerImagePreviewEl');
    const placeholder = document.getElementById('bannerImagePlaceholder');
    const removeBtn = document.getElementById('removeBannerBtn');
    const deleteInput = document.getElementById('deleteBannerImageInput');

    if (input) input.value = '';
    if (previewEl) {
        previewEl.src = '';
        previewEl.style.display = 'none';
    }
    if (placeholder) placeholder.style.display = 'block';
    if (removeBtn) removeBtn.style.display = 'none';
    if (deleteInput) deleteInput.value = '1';
}
</script>
