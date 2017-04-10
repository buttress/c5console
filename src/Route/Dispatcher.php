<?php

namespace Buttress\Concrete\Route;

use FastRoute\RouteCollector as Collector;
use function FastRoute\simpleDispatcher;

/**
 * A Dispatcher wrapper that adjusts the API to work with CLI
 */
class Dispatcher
{

    public function __construct(\FastRoute\Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Dispatches against the provided HTTP method verb and URI.
     *
     * Returns array with one of the following formats:
     *
     *     [self::NOT_FOUND]
     *     [self::METHOD_NOT_ALLOWED, ['GET', 'OTHER_ALLOWED_METHODS']]
     *     [self::FOUND, $handler, ['varName' => 'value', ...]]
     *
     * @param string $command
     *
     * @return array
     */
    public function dispatch($command)
    {
        return $this->dispatcher->dispatch('get', $command);
    }

    /**
     * Get a new instance of dispatcher
     * @param callable $routeDefinitionCallback
     * @param array $options
     * @return static
     */
    public static function simpleDispatcher(callable $routeDefinitionCallback, array $options = [])
    {
        return new static(simpleDispatcher(function (Collector $collector) use ($routeDefinitionCallback) {
            return $routeDefinitionCallback(new RouteCollector($collector));
        }, $options));
    }
}
