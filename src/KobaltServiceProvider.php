<?php

namespace Hoppermagic\Kobalt;

use Illuminate\Foundation\AliasLoader;
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
            __DIR__.'/resources/assets/admin_enhancements.js' => public_path('js/admin_enhancements.js'),
            __DIR__.'/views/partials/nav.blade.php' => resource_path('views/vendor/kobalt/partials/nav.blade.php'),
        ], 'default');

        // Publish all admin views
        $this->publishes([
            __DIR__.'/views' => resource_path('views/vendor/kobalt'),
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

        $this->registerFlashIfNeeded();
        $this->registerFormBuilderIfNeeded();

        $this->loadViewsFrom(__DIR__.'/views', 'kobalt');
    }



    /**
     * Add Flash to the container if its not there
     */
    private function registerFlashIfNeeded()
    {
        if(!$this->app->offsetExists('flash')) {

            $this->app->register('Laracasts\Flash\FlashServiceProvider');
        }

        if (!$this->aliasExists('Flash')) {

            AliasLoader::getInstance()->alias(
                'Flash',
                'Laracasts\Flash\Flash'
            );
        }
    }



    /**
     * Add form builder to the container if its not there
     */
    private function registerFormBuilderIfNeeded()
    {
        if(!$this->app->offsetExists('FormBuilder')) {

            $this->app->register('Kris\LaravelFormBuilder\FormBuilderServiceProvider');
        }

        if (!$this->aliasExists('FormBuilder')) {

            AliasLoader::getInstance()->alias(
                'FormBuilder',
                'Kris\LaravelFormBuilder\Facades\FormBuilder'
            );
        }
    }



    /**
     * Check if an alias already exists in the IOC.
     *
     * @param string $alias
     * @return bool
     */
    private function aliasExists($alias)
    {
        return array_key_exists($alias, AliasLoader::getInstance()->getAliases());
    }
}
