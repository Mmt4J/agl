<?php

use Illuminate\Support\Facades\Route;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

// Admin Routes
// Adjust the middleware to whatever your actual auth/role setup ends up being -
// 'auth' alone is a placeholder until roles exist.


Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('/', 'admin.overview')->name('overview');

    Route::prefix('leads')->name('leads.')->group(function () {
        Route::livewire('/quote-requests', 'admin.leads.quote-requests')->name('quote-requests');
        Route::livewire('/contact-messages', 'admin.leads.contact-messages')->name('contact-messages');
        Route::livewire('/newsletter', 'admin.leads.newsletter')->name('newsletter');
    });

    Route::prefix('content')->name('content.')->group(function () {
        Route::livewire('/services', 'admin.content.services')->name('services');
        Route::livewire('/pricing', 'admin.content.pricing')->name('pricing');
        Route::livewire('/portfolio', 'admin.content.portfolio')->name('portfolio');
        Route::livewire('/blog', 'admin.content.blog')->name('blog');
        Route::livewire('/testimonials', 'admin.content.testimonials')->name('testimonials');
        Route::livewire('/faqs', 'admin.content.faqs')->name('faqs');
    });

    Route::prefix('reference')->name('reference.')->group(function () {
        Route::livewire('/devices-industries', 'admin.reference.devices-industries')->name('devices-industries');
        Route::livewire('/repair-pricing', 'admin.reference.repair-pricing')->name('repair-pricing');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::livewire('/business-hours', 'admin.settings.business-hours')->name('business-hours');
        Route::livewire('/company', 'admin.settings.company')->name('company');
        Route::livewire('/users', 'admin.settings.users')->name('users');
    });
});


// Website Routes
Route::livewire('/', 'pages::website.home')->name('website.home');
Route::livewire('/about', 'pages::website.about')->name('website.about');
Route::livewire('/services', 'pages::website.services')->name('website.services');
Route::livewire('/industries', 'pages::website.industries')->name('website.industries');
Route::livewire('/portfolio', 'pages::website.portfolio')->name('website.portfolio');
Route::livewire('/pricing', 'pages::website.pricing')->name('website.pricing');
Route::livewire('/blog', 'pages::website.blog.index')->name('website.blog');
Route::livewire('/blog/{post}', 'pages::website.blog.show')->name('website.blog.show');
Route::livewire('/contact', 'pages::website.contact')->name('website.contact');
Route::livewire('/quote', 'pages::website.quote')->name('website.quote');



require __DIR__.'/settings.php';