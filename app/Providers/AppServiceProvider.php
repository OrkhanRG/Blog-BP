<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Settings;
use Illuminate\Pagination\Paginator;
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
//      Paginator::useBootstrapFive();
        Paginator::defaultView('vendor.pagination.customPagination');


        View::composer(['front.*', 'mail::header', 'email.*'], function ($view) {
            $settings = Settings::first();
            $categories = Category::query()->where('status', 1)->get();
            $view->with('categories', $categories)->with('settings', $settings);
        });


//      Bütün View-larda Dəyişənləri paylaşır!
        /*
        $settings = Settings::first();
        $categories = Category::query()->where('status', 1)->get();
        View::share(['settings' => $settings, 'categories' => $categories]);
        */
    }
}
