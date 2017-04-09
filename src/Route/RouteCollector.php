<?php

namespace Buttress\Concrete\Route;

use FastRoute\RouteCollector as Collector;

/**
 * A RouteCollector wrapper that adapts the API for CLI commands
 */
final class RouteCollector
{

    private $collector;

    public function __construct(Collector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * Adds a route to the collection.
     * Routes MUST be registered using "/" as a delimiter.
     *
     * The syntax used in the $route string depends on the used route parser.
     *
     * @param string $route
     * @param mixed $handler
     */
    public function addRoute($route, $handler)
    {
        $this->collector->addRoute('get', $route, $handler);
    }

    /**
     * Create a route group with a common prefix.
     *
     * All routes created in the passed callback will have the given group prefix prepended.
     *
     * @param string $prefix
     * @param callable $callback
     */
    public function addGroup($prefix, callable $callback)
    {
        $this->collector->addGroup($prefix . ':', function (Collector $collector) use ($callback) {
            return $callback(new RouteCollector($collector));
        });
    }

    /**
     * Add routes directly to the contained collector
     *
     * @param callable $callback
     */
    public function direct(callable $callback)
    {
        $callback($this->collector);
    }

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    public function getData()
    {
        return $this->collector->getData();
    }

}
