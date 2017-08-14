<?php

namespace Larrock\ComponentMenu;

use Illuminate\Support\ServiceProvider;
use Larrock\ComponentMenu\Middleware\AddMenuFront;

class LarrockComponentMenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadViewsFrom(__DIR__.'/views', 'larrock');

        $this->publishes([
            __DIR__.'/views' => base_path('resources/views/vendor/larrock')
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('larrockmenu', function() {
            $class = config('larrock.components.menu', MenuComponent::class);
            return new $class;
        });

        $this->app['router']->aliasMiddleware('AddMenuFront', AddMenuFront::class);

        if ( !class_exists('CreateMenuTable')){
            // Publish the migration
            $timestamp = date('Y_m_d_His', time());

            $this->publishes([
                __DIR__.'/database/migrations/0000_00_00_000000_create_menu_table.php' => database_path('migrations/'.$timestamp.'_create_menu_table.php')
            ], 'migrations');
        }
    }
}
