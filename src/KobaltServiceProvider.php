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
        // Default assets to publish
        $this->publishes([
            __DIR__.'/resources/assets/adminimg' => public_path('adminimg'),
            __DIR__.'/resources/assets/admin.css' => public_path('css/admin.css'),
            __DIR__.'/resources/assets/admin.js' => public_path('js/admin.js'),
            __DIR__.'/views/partials' => resource_path('views/vendor/hoppermagic'),
        ], 'default');

        // Publish all admin views
        $this->publishes([
            __DIR__.'/views' => resource_path('views/hoppermagic/kobalt'),
        ], 'admin-views');


        // Push $active into the admin navigation view
        //!TODO not sure if this is a good idea or whether it would be better to set this in the main project
        //!TODO need to check this still works on publish
        view()->composer('kobalt::partials.nav', function ($view) {
            $active = \Request::segment(2);
            $view->with('active', $active);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands('Hoppermagic\Kobalt\Console\Commands\MakeKobaltResources');
        $this->commands('Hoppermagic\Kobalt\Console\Commands\MakeKobaltController');
        $this->commands('Hoppermagic\Kobalt\Console\Commands\MakeKobaltModel');
        $this->commands('Hoppermagic\Kobalt\Console\Commands\MakeKobaltForm');
        $this->commands('Hoppermagic\Kobalt\Console\Commands\MakeKobaltRequest');

        $this->loadViewsFrom(__DIR__.'/views', 'kobalt');
    }
}
