<?php

use App\Http\Controllers\Frontend\CheckoutController as FrontendCheckoutController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ContactController as FrontendContactController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\NewsletterSubscriberController as FrontendNewsletterSubscriberController;
use App\Http\Controllers\Frontend\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'pages.about-us')->name('about');
Route::get('/product', [ProductController::class, 'index'])->name('product');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::view('/diet-chart', 'pages.diet-chart')->name('diet_chart');
Route::get('/blog', [BlogController::class, 'index'])->name('blog');
Route::view('/testimonials', 'pages.testimonials')->name('testimonials');
Route::get('/blog/{id}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/checkout', [FrontendCheckoutController::class, 'page'])->name('checkout');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/cookies', 'pages.cookies')->name('cookies');
Route::view('/return-policy', 'pages.return-policy')->name('return-policy');
Route::view('/terms', 'pages.terms')->name('terms');
Route::view('/cart', 'pages.cart')->name('cart.page');

Route::get('/contact', [FrontendContactController::class, 'index'])->name('contact');
Route::post('/contact', [FrontendContactController::class, 'store'])->middleware('throttle:form-submit')->name('contact.store');
Route::post('/newsletter/subscribe', [FrontendNewsletterSubscriberController::class, 'store'])->middleware('throttle:form-submit')->name('newsletter.subscribe');

Route::prefix('/guest/checkout')->name('guest.checkout.')->middleware('throttle:checkout')->group(function () {
    Route::post('/summary', [FrontendCheckoutController::class, 'guestSummary'])->name('summary');
});
