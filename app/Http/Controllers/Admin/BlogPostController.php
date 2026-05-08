<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogPostController extends Controller
{
    public function index(): View
    {
        return view('admin.ecommerce.blog-posts.index', [
            'posts' => BlogPost::with(['category', 'author'])->latest()->get(),
            'trashCount' => BlogPost::onlyTrashed()->count(),
            'categories' => BlogCategory::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function trash(): View
    {
        return view('admin.ecommerce.blog-posts.trash', [
            'posts' => BlogPost::onlyTrashed()->with(['category', 'author'])->latest('deleted_at')->get(),
            'activeCount' => BlogPost::count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'author_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:blog_posts,slug'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        BlogPost::create($validated);

        return back()->with('success', 'Blog post created successfully.');
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $validated = $request->validate([
            'blog_category_id' => ['nullable', 'exists:blog_categories,id'],
            'author_id' => ['nullable', 'exists:users,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('blog_posts', 'slug')->ignore($blogPost->id)],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'meta_keywords' => ['nullable', 'string'],
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        if ($validated['status'] === 'published' && empty($validated['published_at']) && ! $blogPost->published_at) {
            $validated['published_at'] = now();
        }

        $blogPost->update($validated);

        return back()->with('success', 'Blog post updated successfully.');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return back()->with('success', 'Blog post moved to trash successfully.');
    }

    public function restore(int $blogPost): RedirectResponse
    {
        $trashedPost = BlogPost::onlyTrashed()->findOrFail($blogPost);
        $trashedPost->restore();

        return back()->with('success', 'Blog post restored successfully.');
    }

    public function forceDestroy(int $blogPost): RedirectResponse
    {
        $trashedPost = BlogPost::onlyTrashed()->findOrFail($blogPost);
        $trashedPost->forceDelete();

        return back()->with('success', 'Blog post permanently deleted successfully.');
    }

    public function bulkForceDestroy(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'post_ids' => ['required', 'array', 'min:1'],
            'post_ids.*' => ['integer'],
        ]);

        $posts = BlogPost::onlyTrashed()
            ->whereIn('id', $validated['post_ids'])
            ->get();

        foreach ($posts as $post) {
            $post->forceDelete();
        }

        return back()->with('success', $posts->count() . ' blog post(s) permanently deleted successfully.');
    }
}
