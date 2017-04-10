<?php

namespace Buttress\Concrete\Provider\LeagueContainer;

use Buttress\Concrete\CommandBus\Command\HandlerLocator;
use Buttress\Concrete\Console\Command\Collection\Collection;
use Buttress\Concrete\Console\Console;
use Buttress\Concrete\Locator\Locator;
use Buttress\Concrete\Locator\Site;
use Buttress\Concrete\Log\Logger;
use Concrete\Core\Application\Application;
use League\CLImate\CLImate;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class ServiceProvider extends AbstractServiceProvider
{

    protected $provides = [
        LoggerInterface::class,
        Console::class,
        CLImate::class,
        CommandBus::class,
        ContainerInterface::class,
        HandlerLocator::class,
        Site::class,
        Application::class
    ];

    public function register()
    {
        $container = $this->getContainer();

        // Share the CLImate object
        $container->share(CLImate::class, function () use ($container) {
            return new CLImate();
        });

        // Share the command bus handler locator
        $container->share(HandlerLocator::class)->withArgument($container);

        // Add the command bus
        $container->add(
            CommandBus::class,
            function (ClassNameExtractor $extractor, HandlerLocator $locator, HandleClassNameInflector $inflector) {
                $handlerMiddleware = new CommandHandlerMiddleware($extractor, $locator, $inflector);

                return new CommandBus([$handlerMiddleware]);
            }
        )->withArguments([ClassNameExtractor::class, HandlerLocator::class, HandleClassNameInflector::class]);

        // Share the console object
        $container->share(Console::class)
            ->withArgument($container)
            ->withArgument(Collection::class)
            ->withArgument(Site::class);

        $container->share(Site::class, function () use ($container) {
            $site = $container->get(Locator::class)->getLocation();
            if (!$site) {
                $site = new Site();
            }
            return $site;
        });

        // Share the container
        $container->share(ContainerInterface::class, $container);

        // Share the logger class
        $container->share(LoggerInterface::class, Logger::class)->withArgument(CLImate::class);
    }
}
