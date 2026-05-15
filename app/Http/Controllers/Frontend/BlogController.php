<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        $blogPosts = BlogPost::with('category')
            ->where('status', 'published')
            ->latest('published_at')
            ->latest()
            ->get();

        return view('pages.blog', compact('blogPosts'));
    }

    public function show(int $id)
    {
        return view('pages.blog-show', compact('id'));
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
