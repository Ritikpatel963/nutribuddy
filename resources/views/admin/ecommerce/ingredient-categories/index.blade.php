@extends('layout.layout')
@php
    $title = 'Ingredient Categories';
    $subTitle = 'Ecommerce / Ingredient Categories';
@endphp

@section('content')
    @include('admin.ecommerce._messages')

    <div class="card mb-24">
        <div class="card-header">
            <h5 class="card-title mb-0">Create Ingredient Category</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.ecommerce.ingredient-categories.store') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Name</label>
                    <div class="icon-field">
                        <span class="icon"><iconify-icon icon="f7:tag"></iconify-icon></span>
                        <input type="text" name="name" class="form-control" placeholder="Category Name" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Slug (optional)</label>
                    <div class="icon-field">
                        <span class="icon"><iconify-icon icon="lucide:link"></iconify-icon></span>
                        <input type="text" name="slug" class="form-control" placeholder="category-slug">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sort Order</label>
                    <input type="number" min="0" name="sort_order" class="form-control" value="0">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="form-check form-switch mb-8">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" value="1" name="is_active" checked>
                        <label class="form-check-label">Active</label>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Category description"></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary-600">Create Category</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card basic-data-table">
        <div class="card-header">
            <h5 class="card-title mb-0">Ingredient Category List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Ingredients</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->name }}</td>
                                <td>{{ $category->slug }}</td>
                                <td><span class="badge bg-info-100 text-info-600">{{ $category->ingredients_count }}</span></td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge bg-success-100 text-success-600">Active</span>
                                    @else
                                        <span class="badge bg-danger-100 text-danger-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-success-600 edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editIngredientCategoryModal"
                                            data-name="{{ $category->name }}"
                                            data-slug="{{ $category->slug }}"
                                            data-description="{{ $category->description ?? '' }}"
                                            data-sort_order="{{ $category->sort_order }}"
                                            data-is_active="{{ $category->is_active }}"
                                            data-action="{{ route('admin.ecommerce.ingredient-categories.update', $category) }}">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('admin.ecommerce.ingredient-categories.destroy', $category) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger-600" onclick="return confirm('Delete this ingredient category?')">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editIngredientCategoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Ingredient Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editIngredientCategoryForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" id="edit_slug" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" min="0" name="sort_order" id="edit_sort_order" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" value="1" name="is_active" id="edit_is_active">
                            <label class="form-check-label" for="edit_is_active">Active</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-600">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (document.getElementById('dataTable')) {
                new DataTable('#dataTable');
            }

            const editModal = document.getElementById('editIngredientCategoryModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const form = editModal.querySelector('#editIngredientCategoryForm');
                    form.setAttribute('action', button.getAttribute('data-action'));

                    editModal.querySelector('#edit_name').value = button.getAttribute('data-name') || '';
                    editModal.querySelector('#edit_slug').value = button.getAttribute('data-slug') || '';
                    editModal.querySelector('#edit_sort_order').value = button.getAttribute('data-sort_order') || 0;
                    editModal.querySelector('#edit_description').value = button.getAttribute('data-description') || '';
                    editModal.querySelector('#edit_is_active').checked = button.getAttribute('data-is_active') === '1';
                });
            }
        });
    </script>
@endsection
