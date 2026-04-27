@extends('layout.layout')
@php
    $title = 'Blog Posts';
    $subTitle = 'Ecommerce / Blog Posts';
@endphp

@section('content')
    @include('admin.ecommerce._messages')

    <div class="card mb-24">
        <div class="card-header">
            <h5 class="card-title mb-0">Create Blog Post</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.ecommerce.blog-posts.store') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Title</label>
                    <div class="icon-field">
                        <span class="icon">
                            <iconify-icon icon="lucide:type"></iconify-icon>
                        </span>
                        <input type="text" name="title" class="form-control" placeholder="Post Title" required>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Category</label>
                    <select name="blog_category_id" class="form-select">
                        <option value="">Select</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Author</label>
                    <select name="author_id" class="form-select">
                        <option value="">Select</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label">Slug</label>
                    <div class="icon-field">
                        <span class="icon">
                            <iconify-icon icon="lucide:link"></iconify-icon>
                        </span>
                        <input type="text" name="slug" class="form-control" placeholder="slug">
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label">Excerpt</label>
                    <textarea name="excerpt" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Content</label>
                    <textarea name="content" id="blog_content" class="form-control" rows="5" required></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary-600">Create Post</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card basic-data-table">
        <div class="card-header"><h5 class="card-title mb-0">Post List</h5></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table bordered-table mb-0" id="dataTable" data-page-length='10'>
                    <thead>
                        <tr>
                            <th>Post Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($posts as $post)
                            @php
                                $statusClass = match(strtolower($post->status)) {
                                    'published' => 'success',
                                    'draft' => 'warning',
                                    'archived' => 'secondary',
                                    default => 'info'
                                };
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="text-md fw-bold text-dark">{{ $post->title }}</span>
                                        <small class="text-secondary-light fw-medium">Slug: {{ $post->slug }}</small>
                                    </div>
                                </td>
                                <td><span class="badge bg-info-100 text-info-600 px-2 fw-medium">{{ $post->category?->name ?? 'Uncategorized' }}</span></td>
                                <td><span class="text-sm text-secondary-light fw-medium">{{ $post->author?->name ?? 'Admin' }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $statusClass }}-100 text-{{ $statusClass }}-600 px-2 fw-medium">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                </td>
                                <td><span class="text-sm text-secondary-light fw-medium">{{ $post->published_at?->format('d M Y') ?? $post->created_at->format('d M Y') }}</span></td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-success-600 radius-8 d-inline-flex align-items-center gap-1 edit-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editBlogPostModal"
                                            data-title="{{ $post->title }}"
                                            data-slug="{{ $post->slug }}"
                                            data-blog_category_id="{{ $post->blog_category_id }}"
                                            data-author_id="{{ $post->author_id }}"
                                            data-status="{{ $post->status }}"
                                            data-excerpt="{{ $post->excerpt }}"
                                            data-content="{{ $post->content }}"
                                            data-action="{{ route('admin.ecommerce.blog-posts.update', $post) }}">
                                            <iconify-icon icon="lucide:edit"></iconify-icon> Edit
                                        </button>
                                        <form method="POST" action="{{ route('admin.ecommerce.blog-posts.destroy', $post) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger-600 radius-8 d-inline-flex align-items-center gap-1" onclick="return confirm('Delete this post?')">
                                                <iconify-icon icon="mingcute:delete-2-line"></iconify-icon> Delete
                                            </button>
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

    <!-- Edit Blog Post Modal -->
    <div class="modal fade" id="editBlogPostModal" tabindex="-1" aria-labelledby="editBlogPostModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBlogPostModalLabel">Edit Blog Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editBlogPostForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Title</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="lucide:type"></iconify-icon>
                                    </span>
                                    <input type="text" name="title" id="edit_title" class="form-control" placeholder="Post Title" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Category</label>
                                <select name="blog_category_id" id="edit_blog_category_id" class="form-select">
                                    <option value="">Select</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Author</label>
                                <select name="author_id" id="edit_author_id" class="form-select">
                                    <option value="">Select</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" id="edit_status" class="form-select" required>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Slug</label>
                                <div class="icon-field">
                                    <span class="icon">
                                        <iconify-icon icon="lucide:link"></iconify-icon>
                                    </span>
                                    <input type="text" name="slug" id="edit_slug" class="form-control" placeholder="slug">
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Excerpt</label>
                                <textarea name="excerpt" id="edit_excerpt" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Content</label>
                                <textarea name="content" id="edit_blog_content" class="form-control" rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-600">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>
    <script>
        let editEditor;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize CKEditors
            ClassicEditor
                .create( document.querySelector( '#blog_content' ) )
                .catch( error => { console.error( error ); } );
            
            ClassicEditor
                .create( document.querySelector( '#edit_blog_content' ) )
                .then( editor => {
                    editEditor = editor;
                } )
                .catch( error => { console.error( error ); } );

            // Initialize DataTable
            if (document.getElementById('dataTable')) {
                new DataTable('#dataTable');
            }

            const editModal = document.getElementById('editBlogPostModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    
                    const action = button.getAttribute('data-action');
                    const title = button.getAttribute('data-title');
                    const slug = button.getAttribute('data-slug');
                    const blogCategoryId = button.getAttribute('data-blog_category_id');
                    const authorId = button.getAttribute('data-author_id');
                    const status = button.getAttribute('data-status');
                    const excerpt = button.getAttribute('data-excerpt');
                    const content = button.getAttribute('data-content');

                    const form = editModal.querySelector('#editBlogPostForm');
                    form.setAttribute('action', action);
                    
                    editModal.querySelector('#edit_title').value = title || '';
                    editModal.querySelector('#edit_slug').value = slug || '';
                    editModal.querySelector('#edit_blog_category_id').value = blogCategoryId || '';
                    editModal.querySelector('#edit_author_id').value = authorId || '';
                    editModal.querySelector('#edit_status').value = status || '';
                    editModal.querySelector('#edit_excerpt').value = excerpt || '';
                    
                    if (editEditor) {
                        editEditor.setData(content || '');
                    } else {
                        editModal.querySelector('#edit_blog_content').value = content || '';
                    }
                });
            }
        });
    </script>
@endsection
