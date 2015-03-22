<?php namespace Fedeisas\LaravelJsRoutes;

use Illuminate\Support\ServiceProvider;

class LaravelJsRoutesServiceProvider extends ServiceProvider
{

    protected $commands = [
        'Fedeisas\LaravelJsRoutes\Commands\RoutesJavascriptCommand',
    ];

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
