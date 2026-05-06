@extends('layouts.main')
@section('title', 'Blog & Tips — NutriBuddy Kids')

@push('styles')
    <style>
        .blog-hero {
            background: #0d0028;
            padding: 130px 5% 80px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .blog-hero::before {
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

        .blog-hero::after {
            content: '';
            position: absolute;
            width: 380px;
            height: 380px;
            border-radius: 38% 62% 44% 56%/62% 38% 55% 45%;
            background: radial-gradient(circle, rgba(124, 58, 237, .09), transparent 70%);
            bottom: -80px;
            left: -80px;
            animation: blobMorph 14s ease-in-out infinite reverse;
            pointer-events: none;
        }

        .blog-hero-content {
            position: relative;
            z-index: 2;
        }

        .blog-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--pkl);
            color: var(--pkd);
            border-radius: 50px;
            padding: 8px 20px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: .78rem;
            margin-bottom: 20px;
        }

        .blog-hero-title {
            font-family: 'Fredoka One', cursive;
            font-size: clamp(2.2rem, 6vw, 3.6rem);
            color: var(--wh);
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .blog-hero-sub {
            font-size: 1.05rem;
            color: #ffffff;
            max-width: 600px;
            margin: 0 auto 40px;
            line-height: 1.6;
        }

        /* Blog Grid */
        .blog-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 5%;
        }

        .blog-filters {
            display: flex;
            gap: 12px;
            margin-bottom: 50px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .blog-filter-btn {
            padding: 10px 20px;
            border: 2px solid #ddd;
            border-radius: 50px;
            background: #fff;
            color: var(--dk);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Nunito', sans-serif;
            font-size: 0.9rem;
        }

        .blog-filter-btn:hover,
        .blog-filter-btn.active {
            border-color: var(--pk);
            background: var(--pk);
            color: #fff;
        }

        .blog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .blog-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.34, 1.1, 0.64, 1);
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .blog-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .blog-card-image {
            width: 100%;
            height: 220px;
            background: linear-gradient(135deg, var(--pk), var(--pu));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3.5rem;
            overflow: hidden;
        }

        .blog-card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .blog-card-content {
            padding: 24px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .blog-card-category {
            display: inline-block;
            background: var(--pkl);
            color: var(--pk);
            padding: 6px 12px;
            border-radius: 20px;
            font-family: 'Nunito', sans-serif;
            font-weight: 900;
            font-size: 0.7rem;
            margin-bottom: 12px;
            width: fit-content;
        }

        .blog-card-title {
            font-family: 'Fredoka One', cursive;
            font-size: 1.3rem;
            color: var(--dk);
            margin-bottom: 12px;
            line-height: 1.3;
            flex: 1;
        }

        .blog-card-excerpt {
            color: #777;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 16px;
            flex: 1;
        }

        .blog-card-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 0.85rem;
            color: #aaa;
            padding-top: 16px;
            border-top: 1px solid #f0f0f0;
        }

        .blog-card-date {
            display: flex;
            align-items: center;
            gap: 6px;
        }

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
            font-weight: 700;
            text-decoration: none;
            margin-top: 16px;
            transition: all 0.3s ease;
        }

        .blog-card-link:hover {
            gap: 12px;
        }

        /* Pagination */
        .blog-pagination {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .pagination-btn {
            padding: 10px 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background: #fff;
            color: var(--dk);
            cursor: pointer;
            font-weight: 700;
            transition: all 0.3s ease;
            font-family: 'Nunito', sans-serif;
        }

        .pagination-btn:hover,
        .pagination-btn.active {
            background: var(--pk);
            color: #fff;
            border-color: var(--pk);
        }

        @media (max-width: 768px) {
            .blog-hero {
                padding: 80px 5% 50px;
            }

            .blog-hero-title {
                font-size: 2rem;
            }

            .blog-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <!-- Blog Hero -->
    <section class="blog-hero reveal">
        <div class="blog-hero-content">
            <div class="blog-hero-badge">📚 Wellness Wisdom</div>
            <h1 class="blog-hero-title">Blog & Tips</h1>
            <p class="blog-hero-sub">Expert advice, nutrition tips, and parenting hacks from certified Ayurvedic nutritionists and pediatricians.</p>
        </div>
    </section>

    <!-- Blog Container -->
    <section class="blog-container">
        <!-- Filters -->
        <div class="blog-filters">
            <button class="blog-filter-btn active" onclick="filterBlog('all')">All Posts</button>
            <button class="blog-filter-btn" onclick="filterBlog('nutrition')"> Nutrition</button>
            <button class="blog-filter-btn" onclick="filterBlog('parenting')"> Parenting</button>
            <button class="blog-filter-btn" onclick="filterBlog('wellness')"> Wellness</button>
            <button class="blog-filter-btn" onclick="filterBlog('recipes')"> Recipes</button>
        </div>

        <!-- Blog Grid -->
        <div class="blog-grid">
            @php
                $blogPosts = [
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
            @endphp

            @foreach($blogPosts as $post)
                <div class="blog-card" data-category="{{ strtolower($post['category']) }}">
                    <div class="blog-card-image">
                        <span style="font-size: 5rem;">{{ $post['emoji'] }}</span>
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
        function filterBlog(category) {
            const cards = document.querySelectorAll('.blog-card');
            const buttons = document.querySelectorAll('.blog-filter-btn');

            // Update button states
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');

            // Filter cards
            cards.forEach(card => {
                if (category === 'all' || card.getAttribute('data-category') === category.toLowerCase()) {
                    card.style.display = '';
                    setTimeout(() => card.style.opacity = '1', 10);
                } else {
                    card.style.opacity = '0';
                    setTimeout(() => card.style.display = 'none', 300);
                }
            });
        }
    </script>
@endsection
