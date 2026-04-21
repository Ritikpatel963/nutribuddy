<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.head', function ($view) {
            $data = $view->getData();
            
            $siteName = Setting::get('site_name', 'NutriBuddy');
            $defaultDescription = Setting::get('meta_description', 'Your Health Partner Store');
            $defaultKeywords = Setting::get('meta_keywords', 'nutrition, wellness, health, supplements');
            
            $metaTitle = $data['meta_title'] ?? null;
            $metaDescription = $data['meta_description'] ?? $defaultDescription;
            $metaKeywords = $data['meta_keywords'] ?? $defaultKeywords;

            // Contextual SEO
            if (isset($data['product'])) {
                $p = $data['product'];
                $metaTitle = $p->meta_title ?: $p->name;
                $metaDescription = $p->meta_description ?: Str::limit(strip_tags($p->description), 160);
                $metaKeywords = $p->meta_keywords ?: $p->brand . ', ' . $p->name;
            } elseif (isset($data['category'])) {
                $c = $data['category'];
                $metaTitle = $c->meta_title ?: $c->name;
                $metaDescription = $c->meta_description ?: $c->description;
                $metaKeywords = $c->meta_keywords ?: $c->name;
            } elseif (isset($data['post'])) {
                $post = $data['post'];
                $metaTitle = $post->meta_title ?: $post->title;
                $metaDescription = $post->meta_description ?: $post->excerpt;
                $metaKeywords = $post->meta_keywords ?: '';
            }

            $view->with([
                'seoTitle' => $metaTitle ? "$metaTitle | $siteName" : $siteName,
                'seoDescription' => $metaDescription,
                'seoKeywords' => $metaKeywords,
            ]);
        });
    }
}
