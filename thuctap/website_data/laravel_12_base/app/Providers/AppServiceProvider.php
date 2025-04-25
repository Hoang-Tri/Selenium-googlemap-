<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;
use Illuminate\Support\Facades\View;

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
        $settings = Setting::all()->pluck('value', 'key_name');

        // Chia sẻ với tất cả các view
        View::composer('*', function ($view) use ($settings) {
            $view->with('settings', $settings);
        });
    }
}
