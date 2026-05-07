@php
    // Blog posts database-like array
    $blogs = [
        1 => [
            'id' => 1,
            'title' => '5 Essential Vitamins Every Child Needs',
            'category' => 'Nutrition',
            'date' => 'May 3, 2026',
            'readTime' => '5 min read',
            'emoji' => '🧬',
            'author' => 'Dr. Priya Sharma',
            'authorRole' => 'Pediatric Nutritionist',
            'authorEmoji' => '👩‍⚕️',
            'tags' => ['Nutrition', 'Vitamins', 'ChildHealth', 'HealthyKids', 'ParentingTips'],
        ],
        2 => [
            'id' => 2,
            'title' => 'How to Make Nutrition Fun for Picky Eaters',
            'category' => 'Parenting',
            'date' => 'May 1, 2026',
            'readTime' => '4 min read',
            'emoji' => '🎨',
            'author' => 'Ms. Neha Patel',
            'authorRole' => 'Child Psychologist',
            'authorEmoji' => '👩‍🏫',
            'tags' => ['Parenting', 'Picky Eaters', 'Nutrition', 'FamilyTips'],
        ],
        3 => [
            'id' => 3,
            'title' => 'Ayurvedic Approaches to Child Wellness',
            'category' => 'Wellness',
            'date' => 'Apr 28, 2026',
            'readTime' => '6 min read',
            'emoji' => '🌿',
            'author' => 'Dr. Rajesh Kumar',
            'authorRole' => 'Ayurvedic Specialist',
            'authorEmoji' => '👨‍⚕️',
            'tags' => ['Wellness', 'Ayurveda', 'ChildHealth', 'Holistic'],
        ],
        4 => [
            'id' => 4,
            'title' => 'Healthy Recipes Kids Will Actually Eat',
            'category' => 'Recipes',
            'date' => 'Apr 25, 2026',
            'readTime' => '7 min read',
            'emoji' => '🥘',
            'author' => 'Chef Priya Desai',
            'authorRole' => 'Nutritional Chef',
            'authorEmoji' => '👨‍🍳',
            'tags' => ['Recipes', 'HealthyEating', 'KidFriendly', 'EasyRecipes'],
        ],
        5 => [
            'id' => 5,
            'title' => 'Boosting Immunity Naturally: The Science Behind Ashwagandha',
            'category' => 'Nutrition',
            'date' => 'Apr 22, 2026',
            'readTime' => '5 min read',
            'emoji' => '🛡️',
            'author' => 'Dr. Priya Sharma',
            'authorRole' => 'Pediatric Nutritionist',
            'authorEmoji' => '👩‍⚕️',
            'tags' => ['Nutrition', 'Immunity', 'Ashwagandha', 'Ayurveda'],
        ],
        6 => [
            'id' => 6,
            'title' => 'Building Healthy Eating Habits From Early Childhood',
            'category' => 'Parenting',
            'date' => 'Apr 19, 2026',
            'readTime' => '6 min read',
            'emoji' => '👶',
            'author' => 'Ms. Neha Patel',
            'authorRole' => 'Child Psychologist',
            'authorEmoji' => '👩‍🏫',
            'tags' => ['Parenting', 'HealthyHabits', 'EarlyChildhood', 'Nutrition'],
        ],
    ];

    // Get the blog ID from the URL parameter or use default
    $blogId = request('id') ?? 1;
    $blog = $blogs[$blogId] ?? $blogs[1];
    $relatedBlogs = collect($blogs)->where('category', $blog['category'])->where('id', '!=', $blogId)->take(3)->values();
@endphp

@extends('layouts.main')
@section('title', $blog['title'] . ' — NutriBuddy Kids')

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
        }

        .blog-detail-author-name {
            font-weight: 700;
            color: var(--dk);
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
                <span>{{ $blog['title'] }}</span>
            </div>
            <span class="blog-detail-category">📚 {{ $blog['category'] }}</span>
            <h1 class="blog-detail-title">{{ $blog['title'] }}</h1>
            <div class="blog-detail-meta">
                <div class="blog-detail-meta-item">📅 {{ $blog['date'] }}</div>
                <div class="blog-detail-meta-item">⏱️ {{ $blog['readTime'] }}</div>
                <div class="blog-detail-author">
                    <div class="blog-detail-author-avatar">{{ $blog['authorEmoji'] }}</div>
                    <div class="blog-detail-author-info">
                        <span class="blog-detail-author-name">{{ $blog['author'] }}</span>
                        <span class="blog-detail-author-title">{{ $blog['authorRole'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Content -->
    <div class="blog-content-wrapper">
        <article class="blog-article">
            <div class="blog-featured-image">{{ $blog['emoji'] }}</div>

            <h2 id="intro">Understanding Your Child's Nutritional Needs</h2>
            <p>Every parent wants their child to grow up healthy and strong. A crucial part of this journey is ensuring they receive the essential vitamins and minerals their growing bodies need. In this comprehensive guide, we'll explore the five most important vitamins for children's development and how to ensure they're getting enough.</p>

            <div class="blog-highlight">
                <p>💡 Did you know? Children's nutritional needs are 40-50% higher than adults' on a per-pound basis because they're growing so rapidly!</p>
            </div>

            <h3 id="vitamin-d">1. Vitamin D: The Sunshine Vitamin</h3>
            <p>Vitamin D is essential for calcium absorption and bone development. While our bodies can produce vitamin D when exposed to sunlight, many children don't get enough sun exposure, especially in urban areas.</p>
            <ul>
                <li>Recommended daily intake: 600-1000 IU for children</li>
                <li>Best sources: Fortified milk, fatty fish, egg yolks</li>
                <li>Benefits: Strong bones, immune support, mood regulation</li>
            </ul>

            <h3 id="vitamin-c">2. Vitamin C: The Immunity Booster</h3>
            <p>This water-soluble vitamin is crucial for immune function and collagen production. It also helps protect cells from damage caused by free radicals.</p>
            <ul>
                <li>Recommended daily intake: 15-45 mg for children</li>
                <li>Best sources: Citrus fruits, berries, bell peppers</li>
                <li>Benefits: Enhanced immunity, wound healing, antioxidant protection</li>
            </ul>

            <h3 id="vitamin-a">3. Vitamin A: The Vision Protector</h3>
            <p>Essential for eye health and vision development, vitamin A also supports immune function and skin health. Children's eyes are still developing, making this vitamin particularly important.</p>
            <ul>
                <li>Recommended daily intake: 300-600 mcg for children</li>
                <li>Best sources: Sweet potatoes, carrots, spinach, kale</li>
                <li>Benefits: Healthy vision, immune support, skin health</li>
            </ul>

            <h3 id="b-vitamins">4. B Vitamins: Energy & Brain Power</h3>
            <p>The B-complex vitamins are essential for converting food into energy and supporting brain development. They play a crucial role in cognitive function and concentration.</p>
            <ul>
                <li>Recommended daily intake: Varies by age and specific B vitamin</li>
                <li>Best sources: Whole grains, legumes, meat, eggs</li>
                <li>Benefits: Energy production, brain development, nervous system support</li>
            </ul>

            <h3 id="iron">5. Iron: Building Strong Blood</h3>
            <p>Iron is critical for cognitive development and oxygen transport throughout the body. Iron deficiency can lead to anemia and developmental delays if left untreated.</p>
            <ul>
                <li>Recommended daily intake: 7-10 mg for children</li>
                <li>Best sources: Red meat, beans, fortified cereals</li>
                <li>Benefits: Healthy brain development, energy, oxygen transport</li>
            </ul>

            <div class="blog-highlight">
                <p>🎯 Pro Tip: Pair iron-rich foods with vitamin C sources to enhance absorption. For example, serve beans with bell peppers or citrus juice!</p>
            </div>

            <h2 id="fun">Making Nutrition Fun & Sustainable</h2>
            <p>The challenge isn't just about meeting nutritional requirements—it's about making it sustainable and enjoyable for your child. Here are some proven strategies:</p>

            <h3>Lead by Example</h3>
            <p>Children are more likely to develop healthy eating habits if they see their parents practicing them. Make nutritious choices visible and enthusiastic.</p>

            <h3>Make It Interactive</h3>
            <p>Involve children in meal planning and preparation. Let them choose from healthy options and help in the kitchen. This creates ownership and excitement.</p>

            <h3>Create Positive Associations</h3>
            <p>Avoid using treats as rewards or punishments. Instead, create positive experiences around nutritious foods through family meals and fun recipes.</p>

            <h2 id="supplements">When Supplements Make Sense</h2>
            <p>While whole foods should be your primary source of vitamins, supplements can be helpful when dietary intake is insufficient. NutriBuddy's formulations are designed to fill nutritional gaps with kid-friendly delivery methods.</p>

            <p style="margin-top: 40px; font-style: italic; color: #999;">Have questions about your child's nutrition? Consult with a pediatrician or registered dietitian to create a personalized nutrition plan.</p>
        </article>

        <!-- Table of Contents -->
        <aside class="blog-toc">
            <h4 class="blog-toc-title">📑 Contents</h4>
            <ul>
                <li><a href="#intro">Understanding Needs</a></li>
                <li><a href="#vitamin-d">Vitamin D</a></li>
                <li><a href="#vitamin-c">Vitamin C</a></li>
                <li><a href="#vitamin-a">Vitamin A</a></li>
                <li><a href="#b-vitamins">B Vitamins</a></li>
                <li><a href="#iron">Iron</a></li>
                <li><a href="#fun">Making It Fun</a></li>
                <li><a href="#supplements">Supplements</a></li>
            </ul>
        </aside>
    </div>

    <!-- Footer -->
    <section class="blog-footer">
        <div class="blog-tags">
            @foreach($blog['tags'] as $tag)
                <a href="#" class="blog-tag">#{{ $tag }}</a>
            @endforeach
        </div>

        @if($relatedBlogs->count() > 0)
            <h3 style="font-family: 'Fredoka One', cursive; font-size: 1.4rem; color: var(--dk); margin-bottom: 20px;">📖 Related Articles</h3>
            <div class="blog-related">
                @foreach($relatedBlogs as $relatedBlog)
                    <div class="blog-related-card">
                        <div class="blog-related-card-emoji">{{ $relatedBlog['emoji'] }}</div>
                        <h4>{{ $relatedBlog['title'] }}</h4>
                        <p>{{ substr($relatedBlog['title'], 0, 60) }}...</p>
                        <a href="{{ route('blog.show', $relatedBlog['id']) }}">Read Article →</a>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Parent Reviews & FAQ -->
    @include('partials.parent-reviews')
    @include('partials.faq-section')
@endsection
