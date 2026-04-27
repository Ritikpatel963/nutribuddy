@php
    $isEdit = isset($ingredient);
    $existingBenefits = old('benefits', $isEdit ? $ingredient->benefits->pluck('heading')->toArray() : ['']);
    if (count($existingBenefits) === 0) {
        $existingBenefits = [''];
    }
@endphp

<form method="POST" action="{{ $formAction }}" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Ingredient Information</h5>
            <a href="{{ route('admin.ecommerce.ingredients.index') }}" class="btn btn-sm btn-secondary">Back to List</a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Image / Icon</label>
                    <input type="file" name="icon" class="form-control" accept="image/*">
                    @if ($isEdit && $ingredient->icon_path)
                        <img src="{{ asset('storage/' . $ingredient->icon_path) }}" alt="icon" class="mt-12 w-80-px h-80-px radius-8 border object-fit-cover">
                    @endif
                </div>

                <div class="col-md-6">
                    <label class="form-label">Category</label>
                    <div class="d-flex gap-2">
                        <select name="ingredient_category_id" class="form-select">
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ (string) old('ingredient_category_id', $isEdit ? $ingredient->ingredient_category_id : '') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('admin.ecommerce.ingredient-categories.index') }}" class="btn btn-outline-primary-600">Manage</a>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Main Heading</label>
                    <input type="text" name="main_heading" class="form-control" value="{{ old('main_heading', $isEdit ? $ingredient->main_heading : '') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Short Heading</label>
                    <input type="text" name="short_heading" class="form-control" value="{{ old('short_heading', $isEdit ? $ingredient->short_heading : '') }}" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $isEdit ? $ingredient->description : '') }}</textarea>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Sort Order</label>
                    <input type="number" min="0" name="sort_order" class="form-control" value="{{ old('sort_order', $isEdit ? $ingredient->sort_order : 0) }}">
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check form-switch mb-8">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ old('is_active', $isEdit ? $ingredient->is_active : true) ? 'checked' : '' }}>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Benefits (Tabs Heading)</h5>
            <button type="button" class="btn btn-sm btn-outline-primary-600" id="addBenefitBtn">
                <i class="ri-add-line"></i> Add Tab
            </button>
        </div>
        <div class="card-body">
            <div id="benefitsContainer" class="d-flex flex-column gap-2">
                @foreach ($existingBenefits as $benefit)
                    <div class="input-group benefit-row">
                        <input type="text" name="benefits[]" class="form-control" placeholder="Benefit tab heading" value="{{ $benefit }}">
                        <button type="button" class="btn btn-outline-danger remove-benefit" title="Remove">&times;</button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Dosage (2 Headings)</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Dosage Heading 1</label>
                    <input type="text" name="dosage_heading_one" class="form-control" value="{{ old('dosage_heading_one', $isEdit ? $ingredient->dosage_heading_one : '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dosage Heading 2</label>
                    <input type="text" name="dosage_heading_two" class="form-control" value="{{ old('dosage_heading_two', $isEdit ? $ingredient->dosage_heading_two : '') }}">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <button type="submit" class="btn btn-primary-600 px-32">{{ $submitLabel }}</button>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('benefitsContainer');
        const addBtn = document.getElementById('addBenefitBtn');

        const bindRemoveActions = () => {
            container.querySelectorAll('.remove-benefit').forEach((button) => {
                button.onclick = function () {
                    if (container.querySelectorAll('.benefit-row').length === 1) {
                        const input = container.querySelector('.benefit-row input');
                        input.value = '';
                        return;
                    }
                    this.closest('.benefit-row').remove();
                };
            });
        };

        addBtn.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'input-group benefit-row';
            row.innerHTML = '<input type="text" name="benefits[]" class="form-control" placeholder="Benefit tab heading"><button type="button" class="btn btn-outline-danger remove-benefit" title="Remove">&times;</button>';
            container.appendChild(row);
            bindRemoveActions();
        });

        bindRemoveActions();
    });
</script>
