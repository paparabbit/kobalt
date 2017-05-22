<?php

namespace Hoppermagic\Kobalt;

use Illuminate\Support\ServiceProvider;

class KobaltServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes.php';

        $this->publishes([
            __DIR__.'/resources/assets/adminimg' => public_path(),
            __DIR__.'/resources/assets/admin.css' => public_path('css'),
            __DIR__.'/resources/assets/admin.js' => public_path('js'),
        ], 'public');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
//        $this->app->make('Hoppermagic\Bigwig\Controllers\AdminController');
        $this->loadViewsFrom(__DIR__.'/views', 'kobalt');
    }
}
