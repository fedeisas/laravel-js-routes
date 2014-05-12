<?php namespace Fedeisas\LaravelJsRoutes\Generators;

use Illuminate\Filesystem\Filesystem as File;
use Illuminate\Routing\Router;
use Illuminate\Routing\Route;
use Illuminate\Http\Request;

class RoutesJavascriptGenerator
{

    /**
     * File system instance
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $file;

    /**
     * Router instance
     * @var Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Clean routes
     * @var array
     */
    protected $routes;

    /**
     * Parsed routes
     * @var array
     */
    protected $parsedRoutes;

    public function __construct(File $file, Router $router)
    {
        $this->file = $file;
        $this->router = $router;
        $this->routes = $router->getRoutes();
    }

    /**
     * Compile routes template and generate
     *
     * @param string $path
     * @param string $name
     * @return boolean
     */
    public function make($path, $name, $options = array())
    {
        $this->parsedRoutes = $this->getParsedRoutes($options['filter']);

        $template = $this->file->get(__DIR__ . '/templates/Router.js');

        $template = str_replace("routes: null,", 'routes: ' . json_encode($this->parsedRoutes) . ',', $template);
        $template = str_replace("'Router'", "'" . $options['object'] . "'", $template);

        if ($this->file->isWritable($path)) {
            $filename = $path . '/' . $name;
            return $this->file->put($filename, $template) !== false;
        }

        return false;
    }

    protected function getParsedRoutes($filter)
    {
        $parsedRoutes = array();

        foreach ($this->routes as $route) {
            $routeInfo = $this->getRouteInformation($route);

            if ($routeInfo) {
                if ($filter) {
                    if (in_array($filter, $routeInfo['before'])) {
                        unset($routeInfo['before']);
                        $parsedRoutes[] = $routeInfo;
                    }
                } else {
                    unset($routeInfo['before']);
                    $parsedRoutes[] = $routeInfo;
                }
            }
        }

        return array_filter($parsedRoutes);
    }

    /**
     * Get the route information for a given route.
     *
     * @param string $name
     * @param \Illuminate\Routing\Route $route
     * @return array
     */
    protected function getRouteInformation(Route $route)
    {
        return array(
            'uri'    => $route->uri(),
            'name'   => $route->getName(),
            'action'   => $route->getActionName(),
            'before' => $this->getBeforeFilters($route)
        );
    }

    /**
     * Get before filters
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return string
     */
    protected function getBeforeFilters($route)
    {
        $before = array_keys($route->beforeFilters());
        return array_unique(array_merge($before, $this->getPatternFilters($route)));
    }

    /**
     * Get all of the pattern filters matching the route.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return array
     */
    protected function getPatternFilters($route)
    {
        $patterns = array();

        foreach ($route->methods() as $method) {
            $inner = $this->getMethodPatterns($route->uri(), $method);

            $patterns = array_merge($patterns, array_keys($inner));
        }

        return $patterns;
    }

    /**
     * Get the pattern filters for a given URI and method.
     *
     * @param  string  $uri
     * @param  string  $method
     * @return array
     */
    protected function getMethodPatterns($uri, $method)
    {
        return $this->router->findPatternFilters(Request::create($uri, $method));
    }
}
