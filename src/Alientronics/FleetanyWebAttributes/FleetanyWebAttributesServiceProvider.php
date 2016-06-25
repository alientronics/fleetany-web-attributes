<?php

namespace Alientronics\FleetanyWebAttributes;

use Illuminate\Support\ServiceProvider;

/**
 * Class FleetanyWebAttributesServiceProvider
 * @package Alientronics\FleetanyWebAttributes
 */
class FleetanyWebAttributesServiceProvider extends ServiceProvider
{

    /**
     * @return void
     */
    public function boot()
    {
        $this->publishViews();
        $this->publishTranslations();
        $this->publishControllers();
        $this->publishEntities();
        $this->publishRepositories();
        
        $this->loadViewsFrom(__DIR__.'/../../views/', 'fleetany-web-attributes');
        
        // Routes
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../../routes.php';
        }
    }
    
    /**
     * Publish the views files to the application views directory
     */
    public function publishViews()
    {
        $this->publishes([
            __DIR__ . '/../../views/' => base_path('/resources/views'),
        ], 'translations');
    }
    
    /**
     * Publish the translations files to the application translations directory
     */
    public function publishTranslations()
    {
        $this->publishes([
            __DIR__ . '/../../translations/' => base_path('/resources/lang'),
        ], 'translations');
    }
    
    /**
     * Publish the controllers files to the application controllers directory
     */
    public function publishControllers()
    {
        $this->publishes([
            __DIR__ . '/Controllers/' => base_path('/app/Http/Controllers'),
        ], 'controllers');
    }
    
    /**
     * Publish the entities files to the application entities directory
     */
    public function publishEntities()
    {
        $this->publishes([
            __DIR__ . '/Entities/' => base_path('/app/Entities'),
        ], 'entities');
    }
    
    /**
     * Publish the repositories files to the application repositories directory
     */
    public function publishRepositories()
    {
        $this->publishes([
            __DIR__ . '/Repositories/' => base_path('/app/Repositories'),
        ], 'repositories');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
