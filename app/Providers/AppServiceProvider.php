<?php

namespace App\Providers;

use Zenaton\Client;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use App\Http\ViewComposers\MenuComposer;
use App\Http\ViewComposers\HeaderComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        setLocale(LC_TIME, config('app.locale'));

        view()->composer('front/layout', MenuComposer::class);

        view()->composer('back/layout', HeaderComposer::class);

        Blade::if('admin', function () {
            return auth()->user()->role === 'admin';
        });

        Blade::if('redac', function () {
            return auth()->user()->role === 'redac';
        });

        Blade::if('request', function ($url) {
            return request()->is($url);
        });

        Client::init(env('ZENATON_APP_ID'), env('ZENATON_API_TOKEN'), env('ZENATON_APP_ENV'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
