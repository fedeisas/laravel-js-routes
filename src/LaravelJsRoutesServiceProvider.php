<?php namespace Fedeisas\LaravelJsRoutes;

use Illuminate\Support\ServiceProvider;

class LaravelJsRoutesServiceProvider extends ServiceProvider
{

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
        $this->app['routes.javascript'] = $this->app->share(function ($app) {
            $generator = new Generators\RoutesJavascriptGenerator($app['files'], $app['router'], $app['config']->get('app.url'));
            return new Commands\RoutesJavascriptCommand($generator);
        });

        $this->commands('routes.javascript');
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
