<?php

namespace App\Providers;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
       // Menambahkan View Composer untuk sidebar
       View::composer('layouts.sidebar', function ($view) {
        $menus = \App\Models\Menu::with('children')->whereNull('parent_id')->orderBy('order_number')->get();
        $view->with('menus', $menus);
    });
    }
}