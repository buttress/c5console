<?php

namespace Buttress\Concrete\CommandBus\Command;

use League\Tactician\Exception\MissingHandlerException;
use Psr\Container\ContainerInterface;

/**
 * Custom handler locator that pulls handlers from the container
 */
class HandlerLocator implements \League\Tactician\Handler\Locator\HandlerLocator
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Retrieves the handler for a specified command
     *
     * @param string $commandName
     * @return object
     * @throws MissingHandlerException
     */
    public function getHandlerForCommand($commandName)
    {
        if (isset($this->handlers[$commandName])) {
            $handler = $this->handlers[$commandName];
            if ($this->container->has($handler)) {
                return $this->container->get($handler);
            }
        }

        throw new MissingHandlerException(sprintf('No handler found for "%s"', $commandName));
    }

    /**
     * Add a handler for a command
     *
     * @param string $command
     * @param string $handler
     */
    public function pushHandler($command, $handler)
    {
        $this->handlers[$command] = $handler;
    }
}
