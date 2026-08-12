@extends('layout.layout')
@php
    $title = 'Create Blog Post';
    $subTitle = 'Ecommerce / Blog Posts / Create';
@endphp

@section('content')
    <style>
        .ck-editor__editable_inline {
            min-height: 300px;
        }
    </style>
    @include('admin.ecommerce._messages')

    <div class="card mb-24">
        <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
            <h5 class="card-title mb-0">Create Blog Post</h5>
            <a href="{{ route('admin.ecommerce.blog-posts.index') }}" class="btn btn-sm btn-outline-primary-600">
                <iconify-icon icon="lucide:arrow-left"></iconify-icon> Back to Posts
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.ecommerce.blog-posts.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <div class="icon-field">
                        <span class="icon">
                            <iconify-icon icon="lucide:type"></iconify-icon>
                        </span>
                        <input type="text" name="title" class="form-control" placeholder="Post Title" value="{{ old('title') }}" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug</label>
                    <div class="icon-field">
                        <span class="icon">
                            <iconify-icon icon="lucide:link"></iconify-icon>
                        </span>
                        <input type="text" name="slug" class="form-control" placeholder="slug" value="{{ old('slug') }}">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="blog_category_id" class="form-select">
                        <option value="">Select</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('blog_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Author</label>
                    <select name="author_id" class="form-select">
                        <option value="">Select</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" {{ old('author_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Featured Image</label>
                    <div class="d-flex align-items-start gap-3">
                        <div class="flex-grow-1">
                            <input type="file" name="featured_image" id="featured_image_input" class="form-control" accept="image/*">
                        </div>
                        <div class="flex-shrink-0 border rounded overflow-hidden d-flex align-items-center justify-content-center bg-light" id="featured_image_preview_container" style="display: none !important; width: 100px; height: 100px;">
                            <img id="featured_image_preview" src="" alt="Preview" class="object-fit-cover w-100 h-100">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Content</label>
                    <textarea name="content" id="blog_content" class="form-control" rows="5">{{ old('content') }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary-600">Create Post</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize CKEditors
            ClassicEditor
                .create( document.querySelector( '#blog_content' ), {
                    ckfinder: {
                        uploadUrl: '{{ route('admin.ecommerce.blog-posts.upload-image') }}?_token={{ csrf_token() }}'
                    }
                })
                .catch( error => { console.error( error ); } );

            // Image Preview
            const imgInput = document.getElementById('featured_image_input');
            const imgPreviewContainer = document.getElementById('featured_image_preview_container');
            const imgPreview = document.getElementById('featured_image_preview');

            imgInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imgPreview.src = e.target.result;
                        imgPreviewContainer.style.setProperty('display', 'block', 'important');
                    }
                    reader.readAsDataURL(file);
                } else {
                    imgPreview.src = '';
                    imgPreviewContainer.style.setProperty('display', 'none', 'important');
                }
            });
        });
    </script>
@endsection
