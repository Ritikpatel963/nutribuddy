<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $query = BlogPost::with('category')
            ->where('status', 'published')
            ->latest('published_at');

        if ($request->has('category') && $request->category !== 'all') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        $backendBlogPosts = $query->paginate(10);
        $categories = \App\Models\BlogCategory::all();

        return view('pages.blog', compact('backendBlogPosts', 'categories'));
    }

    public function show($id)
    {
        $blogPost = BlogPost::with(['category', 'author'])->findOrFail($id);
        
        $relatedBlogs = BlogPost::with('category')
            ->where('blog_category_id', $blogPost->blog_category_id)
            ->where('id', '!=', $blogPost->id)
            ->where('status', 'published')
            ->latest('published_at')
            ->take(3)
            ->get();
            
        $recentBlogs = BlogPost::with('category')
            ->where('status', 'published')
            ->where('id', '!=', $blogPost->id)
            ->latest('published_at')
            ->take(4)
            ->get();
            
        $popularBlogs = BlogPost::with('category')
            ->where('status', 'published')
            ->where('id', '!=', $blogPost->id)
            ->inRandomOrder()
            ->take(4)
            ->get();
            
        return view('pages.blog-show', compact('blogPost', 'relatedBlogs', 'recentBlogs', 'popularBlogs'));
    }

    public function addBlog()
    {
        return view('blog/addBlog');
    }
    
    public function blog()
    {
        return view('blog/blog');
    }
    
    public function blogDetails()
    {
        return view('blog/blogDetails');
    }
}
