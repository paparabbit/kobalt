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
            __DIR__.'/resources/assets/adminimg' => public_path(),
            __DIR__.'/resources/assets/admin.css' => public_path('css'),
            __DIR__.'/resources/assets/admin.js' => public_path('js'),
            __DIR__.'/views/partials/nav.blade.php' => resource_path('views/hoppermagic/kobalt/partials'),
        ], 'default');

        // Publish all admin views
        $this->publishes([
            __DIR__.'/views' => resource_path('views/hoppermagic/kobalt'),
        ], 'admin-views');
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
