@php
    $words = str_word_count(strip_tags($blogPost->content ?? ''));
    $readTime = max(1, (int) ceil($words / 200)) . ' min read';
    $date = optional($blogPost->published_at ?? $blogPost->created_at)->format('M j, Y');
    $authorName = $blogPost->author?->name ?? 'Admin';
    $categoryName = $blogPost->category?->name ?? 'Wellness';
    
    $image = trim((string) $blogPost->featured_image);
    $imageUrl = null;
    if ($image !== '') {
        $imageUrl = \Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])
            ? $image
            : asset(\Illuminate\Support\Str::startsWith($image, ['storage/', '/storage/']) ? ltrim($image, '/') : 'storage/' . ltrim($image, '/'));
    }
@endphp

@extends('layouts.main')
@section('title', html_entity_decode($blogPost->title) . ' — NutriBuddy Kids')

@push('styles')
    <style>
        .blog-detail-hero {
            background: #0d0028;
            padding: 100px 5% 60px;
            position: relative;
            overflow: hidden;
        }

        .blog-detail-hero::before {
            content: '';
            position: absolute;
            width: 560px;
            height: 560px;
            border-radius: 62% 38% 56% 44%/48% 62% 38% 52%;
            background: radial-gradient(circle, rgba(255, 77, 143, .12), transparent 70%);
            top: -160px;
            right: -120px;
            animation: blobMorph 10s ease-in-out infinite;
            pointer-events: none;
        }

        .blog-detail-header {
            padding-top: 23px;
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .blog-detail-breadcrumb {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            font-size: 0.9rem;
            color: #ffffff;
            flex-wrap: wrap;
        }

        .blog-detail-breadcrumb a {
            color: var(--pk);
            text-decoration: none;
            font-weight: 700;
        }

        .blog-detail-breadcrumb a:hover {
            text-decoration: underline;
        }

        .blog-detail-category {
            display: inline-block;
            background: var(--pkl);
            color: var(--pk);
            padding: 8px 16px;
            border-radius: 50px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: 0.75rem;
            margin-bottom: 16px;
        }

        .blog-detail-title {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.8rem, 5vw, 2.8rem);
            color: var(--wh);
            line-height: 1.3;
            margin-bottom: 20px;
        }

        .blog-detail-meta {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            color: #ffffff;
            font-size: 0.95rem;
            padding-bottom: 24px;
            border-bottom: 2px solid #f0f0f0;
        }

        .blog-detail-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .blog-detail-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .blog-detail-author-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--pk), var(--pu));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
        }

        .blog-detail-author-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .blog-detail-author-name {
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 2px;
        }

        .blog-detail-author-title {
            font-size: 0.8rem;
            color: #ffffff;
        }

        /* Article Content */
        .blog-content-wrapper {
            max-width: 1100px;
            margin: 60px auto;
            padding: 0 5%;
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 40px;
        }

        .blog-featured-image {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, var(--pk), var(--pu));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 6rem;
            margin-bottom: 50px;
            overflow: hidden;
        }

        .blog-featured-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .blog-article {
            line-height: 2;
            font-size: 1.05rem;
            color: #444;
            min-width: 0; /* Prevents flex/grid blowout */
        }
        
        .blog-sidebar {
            min-width: 0;
        }

        .blog-article img, 
        .blog-article iframe, 
        .blog-article video {
            max-width: 100%;
            height: auto;
        }

        .blog-article h2 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.8rem;
            color: var(--dk);
            margin: 40px 0 20px 0;
        }

        .blog-article h3 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.4rem;
            color: var(--pk);
            margin: 30px 0 15px 0;
        }

        .blog-article p {
            margin-bottom: 20px;
        }

        .blog-article ul,
        .blog-article ol {
            margin: 20px 0 20px 30px;
        }

        .blog-article li {
            margin-bottom: 12px;
        }

        .blog-highlight {
            background: var(--pkl);
            padding: 24px;
            border-left: 4px solid var(--pk);
            border-radius: 8px;
            margin: 30px 0;
        }

        .blog-highlight p {
            margin: 0;
            font-weight: 700;
            color: var(--pk);
        }

        /* Sidebar */
        .blog-toc {
            background: #f9f9f9;
            padding: 24px;
            border-radius: 12px;
            position: sticky;
            top: 20px;
            height: fit-content;
            border: 1px solid #e8e8e8;
        }

        .blog-toc-title {
            font-family: 'Fredoka One', cursive;
            font-size: 1.1rem;
            color: var(--dk);
            margin-bottom: 16px;
        }

        .blog-toc ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .blog-toc li {
            margin-bottom: 12px;
        }

        .blog-toc a {
            color: #777;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .blog-toc a:hover {
            color: var(--pk);
            margin-left: 8px;
        }

        /* Article Footer */
        .blog-footer {
            max-width: 1100px;
            margin: 80px auto 60px;
            padding: 0 5%;
            border-top: 2px solid #f0f0f0;
            padding-top: 40px;
        }

        .blog-tags {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 40px;
        }

        .blog-tag {
            background: #f0f0f0;
            color: #666;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .blog-tag:hover {
            background: var(--pkl);
            color: var(--pk);
        }

        .blog-related {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-top: 40px;
        }

        .blog-related-card {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 12px;
            padding: 16px;
            transition: all 0.3s ease;
        }

        .blog-related-card:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .blog-related-card-emoji {
            font-size: 2.5rem;
            margin-bottom: 12px;
        }

        .blog-related-card h4 {
            font-family: 'Fredoka One', cursive;
            font-size: 1.1rem;
            color: var(--dk);
            margin-bottom: 8px;
        }

        .blog-related-card p {
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 12px;
        }

        .blog-related-card a {
            color: var(--pk);
            font-weight: 700;
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .blog-detail-hero {
                padding: 60px 5% 40px;
            }

            .blog-content-wrapper {
                grid-template-columns: 1fr;
            }

            .blog-featured-image {
                height: 250px;
                font-size: 3rem;
            }

            .blog-toc {
                position: static;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Hero -->
    <section class="blog-detail-hero">
        <div class="blog-detail-header">
            <div class="blog-detail-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>/</span>
                <a href="{{ route('blog') }}">Blog</a>
                <span>/</span>
                <span>{{ html_entity_decode($blogPost->title) }}</span>
            </div>
            <span class="blog-detail-category">{{ $categoryName }}</span>
            <h1 class="blog-detail-title">{{ html_entity_decode($blogPost->title) }}</h1>
            <div class="blog-detail-meta">
                <div class="blog-detail-meta-item">📅 {{ $date }}</div>
                <div class="blog-detail-meta-item">⏱️ {{ $readTime }}</div>
                <div class="blog-detail-author">
                    <div class="blog-detail-author-avatar">👤</div>
                    <div class="blog-detail-author-info">
                        <span class="blog-detail-author-name">{{ $authorName }}</span>
                        <span class="blog-detail-author-title">Author</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content -->
    <div class="blog-content-wrapper">
        <article class="blog-article">
            @if($imageUrl)
                <div class="blog-featured-image" style="background: none; font-size: initial;">
                    <img src="{{ $imageUrl }}" alt="{{ html_entity_decode($blogPost->title) }}">
                </div>
            @endif

            {!! $blogPost->content !!}

            <div class="blog-share-icons" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
                <h4 style="font-family: 'Fredoka One', cursive; margin-bottom: 16px; font-size: 1.2rem; color: var(--dk);">Share this article</h4>
                <div style="display: flex; gap: 12px;">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" style="width: 44px; height: 44px; border-radius: 50%; background: #1877F2; color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-weight: bold; font-family: sans-serif; font-size: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">f</a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($blogPost->title) }}" target="_blank" style="width: 44px; height: 44px; border-radius: 50%; background: #000000; color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-weight: bold; font-family: sans-serif; font-size: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">𝕏</a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->fullUrl()) }}&title={{ urlencode($blogPost->title) }}" target="_blank" style="width: 44px; height: 44px; border-radius: 50%; background: #0A66C2; color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-weight: bold; font-family: sans-serif; font-size: 1.1rem; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">in</a>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($blogPost->title . ' ' . request()->fullUrl()) }}" target="_blank" style="width: 44px; height: 44px; border-radius: 50%; background: #25D366; color: #fff; display: flex; align-items: center; justify-content: center; text-decoration: none; font-weight: bold; font-family: sans-serif; font-size: 1.2rem; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">W</a>
                </div>
            </div>
        </article>

        <aside class="blog-sidebar">
            <div class="blog-toc" style="margin-bottom: 30px;">
                <h4 class="blog-toc-title">🕒 Recent Posts</h4>
                <ul>
                    @foreach($recentBlogs as $recent)
                        <li>
                            <a href="{{ route('blog.show', $recent->id) }}" style="display:block; line-height: 1.4;">
                                <strong>{{ \Illuminate\Support\Str::limit(html_entity_decode($recent->title), 40) }}</strong>
                                <div style="font-size: 0.8rem; color: #999; margin-top: 4px;">{{ optional($recent->published_at ?? $recent->created_at)->format('M j, Y') }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            
            <div class="blog-toc">
                <h4 class="blog-toc-title">🔥 Popular Posts</h4>
                <ul>
                    @foreach($popularBlogs as $popular)
                        <li>
                            <a href="{{ route('blog.show', $popular->id) }}" style="display:block; line-height: 1.4;">
                                <strong>{{ \Illuminate\Support\Str::limit(html_entity_decode($popular->title), 40) }}</strong>
                                <div style="font-size: 0.8rem; color: #999; margin-top: 4px;">{{ optional($popular->published_at ?? $popular->created_at)->format('M j, Y') }}</div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>
    </div>

    <!-- Footer -->
    <section class="blog-footer">
        @if($relatedBlogs->count() > 0)
            <h3 style="font-family: 'Fredoka One', cursive; font-size: 1.4rem; color: var(--dk); margin-bottom: 20px;">📖 Related Articles</h3>
            <div class="blog-related">
                @foreach($relatedBlogs as $relatedBlog)
                    <div class="blog-related-card">
                        <div class="blog-related-card-emoji">📚</div>
                        <h4>{{ html_entity_decode($relatedBlog->title) }}</h4>
                        <p>{{ \Illuminate\Support\Str::limit(html_entity_decode(strip_tags($relatedBlog->content)), 60) }}</p>
                        <a href="{{ route('blog.show', $relatedBlog->id) }}">Read Article →</a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Parent Reviews & FAQ -->
    @include('partials.parent-reviews')
    @include('partials.faq-section')
@endsection
