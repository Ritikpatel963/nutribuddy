@extends('layouts.main')
@section('title', 'Blog & Tips — NutriBuddy Kids')

@push('styles')
    <style>
        .blog-container {
            max-width: 1240px;
            margin: 0 auto;
            padding: 64px 5% 76px;
        }

        .blog-section-head {
            align-items: end;
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .blog-section-kicker {
            color: var(--pk);
            display: block;
            font-family: 'Nunito', sans-serif;
            font-size: .75rem;
            font-weight: 900;
            letter-spacing: .12em;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .blog-section-title {
            color: var(--dk);
            font-family: 'Fredoka One', cursive;
            font-size: clamp(1.8rem, 4vw, 2.8rem);
            line-height: 1.15;
            margin: 0;
        }

        .blog-section-sub {
            color: #777;
            font-family: 'DM Sans', sans-serif;
            line-height: 1.7;
            margin: 10px 0 0;
            max-width: 600px;
        }

        .blog-count-pill {
            align-items: center;
            background: var(--pkl);
            border: 1px solid rgba(255, 77, 143, .18);
            border-radius: 999px;
            color: var(--pk);
            display: inline-flex;
            font-family: 'Nunito', sans-serif;
            font-size: .82rem;
            font-weight: 900;
            min-height: 40px;
            padding: 0 16px;
        }

        .blog-filters {
            background: rgba(255, 255, 255, .78);
            border: 1px solid rgba(255, 214, 232, .85);
            border-radius: 22px;
            box-shadow: 0 14px 36px rgba(13, 0, 32, .06);
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 34px;
            padding: 12px;
        }

        .blog-filter-btn {
            background: #fff;
            border: 2px solid #f1e5ef;
            border-radius: 999px;
            color: var(--dk);
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            font-size: .86rem;
            font-weight: 900;
            min-height: 42px;
            padding: 0 18px;
            transition: all .22s ease;
        }

        .blog-filter-btn:hover,
        .blog-filter-btn.active {
            background: var(--pk);
            border-color: var(--pk);
            box-shadow: 0 8px 18px rgba(255, 77, 143, .22);
            color: #fff;
            transform: translateY(-1px);
        }

        .blog-grid {
            display: grid;
            gap: 26px;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin-bottom: 44px;
        }

        .blog-card {
            background: #fff;
            border: 1px solid rgba(255, 214, 232, .9);
            border-radius: 26px;
            box-shadow: 0 18px 46px rgba(13, 0, 32, .07);
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
            position: relative;
            transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
        }

        .blog-card:hover {
            border-color: rgba(255, 77, 143, .34);
            box-shadow: 0 24px 58px rgba(255, 77, 143, .13);
            transform: translateY(-8px);
        }

        .blog-card-image {
            align-items: center;
            background:
                radial-gradient(circle at 18% 16%, rgba(255, 255, 255, .38), transparent 28%),
                linear-gradient(135deg, var(--pk), var(--pu));
            display: flex;
            height: 210px;
            justify-content: center;
            overflow: hidden;
            position: relative;
            width: 100%;
        }

        .blog-card-image::after {
            background: rgba(13, 0, 32, .1);
            content: '';
            inset: 0;
            position: absolute;
        }

        .blog-card-image img {
            height: 100%;
            inset: 0;
            object-fit: cover;
            position: absolute;
            width: 100%;
            z-index: 1;
        }

        .blog-card-emoji {
            align-items: center;
            background: rgba(255, 255, 255, .9);
            border: 1px solid rgba(255, 255, 255, .6);
            border-radius: 28px;
            box-shadow: 0 14px 32px rgba(13, 0, 32, .12);
            color: var(--dk);
            display: inline-flex;
            font-family: 'Fredoka One', cursive;
            font-size: 3.2rem;
            height: 112px;
            justify-content: center;
            position: relative;
            width: 112px;
            z-index: 2;
        }

        .blog-card-image.has-image::after {
            background: linear-gradient(180deg, rgba(13, 0, 32, .02), rgba(13, 0, 32, .24));
            z-index: 2;
        }

        .blog-card-content {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 24px;
        }

        .blog-card-category {
            background: var(--pkl);
            border-radius: 999px;
            color: var(--pk);
            display: inline-flex;
            font-family: 'Nunito', sans-serif;
            font-size: .72rem;
            font-weight: 900;
            margin-bottom: 14px;
            min-height: 28px;
            padding: 0 12px;
            text-transform: uppercase;
            width: fit-content;
        }

        .blog-card-title {
            color: var(--dk);
            flex: 1;
            font-family: 'Fredoka One', cursive;
            font-size: 1.24rem;
            line-height: 1.32;
            margin: 0 0 12px;
        }

        .blog-card-title a {
            color: inherit;
            text-decoration: none;
        }

        .blog-card-title a:hover {
            color: var(--pk);
        }

        .blog-card-excerpt {
            color: #777;
            flex: 1;
            font-size: .94rem;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .blog-card-meta {
            display: flex;
            align-items: center;
            border-top: 1px solid #f4e9f0;
            color: #aaa;
            flex-wrap: wrap;
            font-family: 'Nunito', sans-serif;
            font-size: .82rem;
            font-weight: 800;
            gap: 14px;
            padding-top: 16px;
        }

        .blog-card-date,
        .blog-card-read-time {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .blog-card-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--pk);
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            margin-top: 16px;
            text-decoration: none;
            transition: gap .2s ease, color .2s ease;
        }

        .blog-card-link:hover {
            color: var(--pkd);
            gap: 12px;
        }

        .blog-pagination {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .pagination-btn {
            background: #fff;
            border: 2px solid #f1e5ef;
            border-radius: 14px;
            color: var(--dk);
            cursor: pointer;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            min-height: 42px;
            min-width: 42px;
            transition: all .22s ease;
        }

        .pagination-btn:hover,
        .pagination-btn.active {
            background: var(--pk);
            color: #fff;
            border-color: var(--pk);
        }

        .blog-empty {
            background: #fff;
            border: 2px dashed rgba(255, 77, 143, .28);
            border-radius: 24px;
            color: #777;
            display: none;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            grid-column: 1 / -1;
            padding: 34px;
            text-align: center;
        }

        @media (max-width: 1024px) {
            .blog-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 768px) {
            .blog-section-head {
                align-items: flex-start;
            }

            .blog-filters {
                overflow-x: auto;
                padding: 10px;
            }

            .blog-filter-btn {
                flex: 0 0 auto;
            }

            .blog-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Blog Hero -->
    <section class="product-listing-hero reveal">
        <div class="product-listing-hero-inner">
            <div class="product-listing-breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span>/</span>
                <span>Blog</span>
            </div>
            <span class="product-listing-hero-badge">Wellness Wisdom</span>
            <h1 class="product-listing-hero-title">Blog & Tips</h1>
            <p class="product-listing-hero-sub">Expert advice, nutrition tips, and parenting hacks from certified Ayurvedic nutritionists and pediatricians.</p>
        </div>
    </section>

    <!-- Blog Container -->
    <section class="blog-container">
        <div class="blog-section-head">
            <div>
                <span class="blog-section-kicker">NutriBuddy Journal</span>
                <h2 class="blog-section-title">Practical Wellness Reads</h2>
                <p class="blog-section-sub">Helpful, parent-friendly articles on nutrition, habits, recipes and everyday child wellness.</p>
            </div>
            <div class="blog-count-pill"><span id="blogVisibleCount">{{ isset($blogPosts) ? count($blogPosts) : 6 }}</span>&nbsp;articles</div>
        </div>

        <!-- Filters -->
        <div class="blog-filters">
            <button type="button" class="blog-filter-btn active" data-filter="all">All Posts</button>
            <button type="button" class="blog-filter-btn" data-filter="nutrition">Nutrition</button>
            <button type="button" class="blog-filter-btn" data-filter="parenting">Parenting</button>
            <button type="button" class="blog-filter-btn" data-filter="wellness">Wellness</button>
            <button type="button" class="blog-filter-btn" data-filter="recipes">Recipes</button>
        </div>

        <!-- Blog Grid -->
        <div class="blog-grid">
            @php
                $backendBlogPosts = isset($blogPosts) ? collect($blogPosts) : collect();
                $fallbackBlogPosts = [
                    [
                        'id' => 1,
                        'title' => '5 Essential Vitamins Every Child Needs',
                        'excerpt' => 'Discover the five most important vitamins for children\'s growth, immunity, and cognitive development.',
                        'category' => 'Nutrition',
                        'date' => 'May 3, 2026',
                        'readTime' => '5 min read',
                        'emoji' => '🧬'
                    ],
                    [
                        'id' => 2,
                        'title' => 'How to Make Nutrition Fun for Picky Eaters',
                        'excerpt' => 'Turn supplement time into an exciting ritual your kids actually look forward to with these proven strategies.',
                        'category' => 'Parenting',
                        'date' => 'May 1, 2026',
                        'readTime' => '4 min read',
                        'emoji' => '🎨'
                    ],
                    [
                        'id' => 3,
                        'title' => 'Ayurvedic Approaches to Child Wellness',
                        'excerpt' => 'Explore ancient Ayurvedic wisdom for maintaining balance and vitality in children\'s health.',
                        'category' => 'Wellness',
                        'date' => 'Apr 28, 2026',
                        'readTime' => '6 min read',
                        'emoji' => '🌿'
                    ],
                    [
                        'id' => 4,
                        'title' => 'Healthy Recipes Kids Will Actually Eat',
                        'excerpt' => 'Delicious, nutrient-packed recipes that combine Ayurvedic principles with modern flavors.',
                        'category' => 'Recipes',
                        'date' => 'Apr 25, 2026',
                        'readTime' => '7 min read',
                        'emoji' => '🥘'
                    ],
                    [
                        'id' => 5,
                        'title' => 'Boosting Immunity Naturally: The Science Behind Ashwagandha',
                        'excerpt' => 'Learn why Ashwagandha is the perfect addition to your child\'s wellness routine.',
                        'category' => 'Nutrition',
                        'date' => 'Apr 22, 2026',
                        'readTime' => '5 min read',
                        'emoji' => '🛡️'
                    ],
                    [
                        'id' => 6,
                        'title' => 'Building Healthy Eating Habits From Early Childhood',
                        'excerpt' => 'Establish lifelong patterns of nutrition awareness with expert parenting techniques.',
                        'category' => 'Parenting',
                        'date' => 'Apr 19, 2026',
                        'readTime' => '6 min read',
                        'emoji' => '👶'
                    ],
                ];

                $blogPosts = $backendBlogPosts->count()
                    ? $backendBlogPosts->map(function ($post) {
                        $words = str_word_count(strip_tags($post->content ?? ''));
                        $image = trim((string) $post->featured_image);
                        $imageUrl = null;

                        if ($image !== '') {
                            $imageUrl = \Illuminate\Support\Str::startsWith($image, ['http://', 'https://'])
                                ? $image
                                : asset(\Illuminate\Support\Str::startsWith($image, ['storage/', '/storage/']) ? ltrim($image, '/') : 'storage/' . ltrim($image, '/'));
                        }

                        return [
                            'id' => $post->id,
                            'title' => $post->title,
                            'excerpt' => $post->excerpt ?: \Illuminate\Support\Str::limit(strip_tags($post->content ?? ''), 130),
                            'category' => $post->category?->name ?? 'Wellness',
                            'date' => optional($post->published_at ?? $post->created_at)->format('M j, Y'),
                            'readTime' => max(1, (int) ceil($words / 200)) . ' min read',
                            'emoji' => '📚',
                            'image' => $imageUrl,
                        ];
                    })->values()->all()
                    : $fallbackBlogPosts;
            @endphp

            @foreach($blogPosts as $post)
                <div class="blog-card" data-category="{{ strtolower($post['category']) }}">
                    @php $postImage = $post['image'] ?? null; @endphp
                    <div class="blog-card-image {{ $postImage ? 'has-image' : '' }}">
                        @if($postImage)
                            <img src="{{ $postImage }}" alt="{{ $post['title'] }}" loading="lazy">
                        @else
                            <span class="blog-card-emoji">{{ $post['emoji'] }}</span>
                        @endif
                    </div>
                    <div class="blog-card-content">
                        <span class="blog-card-category">{{ $post['category'] }}</span>
                        <h3 class="blog-card-title">
                            <a href="{{ route('blog.show', $post['id']) }}">{{ $post['title'] }}</a>
                        </h3>
                        <p class="blog-card-excerpt">{{ $post['excerpt'] }}</p>
                        <div class="blog-card-meta">
                            <span class="blog-card-date">📅 {{ $post['date'] }}</span>
                            <span class="blog-card-read-time">⏱️ {{ $post['readTime'] }}</span>
                        </div>
                        <a href="{{ route('blog.show', $post['id']) }}" class="blog-card-link">
                            Read Article →
                        </a>
                    </div>
                </div>
            @endforeach
            <div class="blog-empty" id="blogEmptyState">No articles found in this category.</div>
        </div>

        <!-- Pagination -->
        <div class="blog-pagination">
            <button class="pagination-btn active">1</button>
            <button class="pagination-btn">2</button>
            <button class="pagination-btn">3</button>
        </div>
    </section>

    <!-- Parent Reviews & FAQ -->
    @include('partials.parent-reviews')
    @include('partials.faq-section')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cards = Array.from(document.querySelectorAll('.blog-card'));
            const buttons = Array.from(document.querySelectorAll('.blog-filter-btn'));
            const visibleCount = document.getElementById('blogVisibleCount');
            const emptyState = document.getElementById('blogEmptyState');

            function filterBlog(category) {
                let count = 0;

                cards.forEach(card => {
                    const show = category === 'all' || card.dataset.category === category;
                    card.style.display = show ? '' : 'none';
                    if (show) count++;
                });

                if (visibleCount) visibleCount.textContent = count;
                if (emptyState) emptyState.style.display = count === 0 ? 'block' : 'none';
            }

            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    buttons.forEach(item => item.classList.toggle('active', item === button));
                    filterBlog(button.dataset.filter || 'all');
                });
            });

            filterBlog('all');
        });
    </script>
@endsection
