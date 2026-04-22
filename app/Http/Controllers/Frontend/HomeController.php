<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = \App\Models\Product::where('is_active', true)
            ->with(['primaryImage', 'category', 'images'])
            ->latest()
            ->limit(3)
            ->get();

        return view('pages.index', compact('featuredProducts'));
    }

    public function calendar()
    {
        return view('calendar');
    }

    public function chatMessage()
    {
        return view('chatMessage');
    }

    public function chatempty()
    {
        return view('chatempty');
    }

    public function veiwDetails()
    {
        return view('veiwDetails');
    }

    public function email()
    {
        return view('email');
    }

    public function error1()
    {
        return view('error');
    }

    public function faq()
    {
        return view('faq');
    }

    public function gallery()
    {
        return view('gallery');
    }

    public function kanban()
    {
        return view('kanban');
    }

    public function pricing()
    {
        return view('pricing');
    }

    public function termsCondition()
    {
        return view('termsCondition');
    }

    public function widgets()
    {
        return view('widgets');
    }

    public function chatProfile()
    {
        return view('chatProfile');
    }

    public function blankPage()
    {
        return view('blankPage');
    }

    public function comingSoon()
    {
        return view('comingSoon');
    }

    public function starred()
    {
        return view('starred');
    }

    public function testimonials()
    {
        return view('testimonials');
    }

    public function maintenance()
    {
        return view('maintenance');
    }

}
