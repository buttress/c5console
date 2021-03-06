<?php

namespace Buttress\Concrete\Console;

use Buttress\Concrete\CommandBus\Provider\DefaultProvider;
use Buttress\Concrete\Console\Command\CacheCommand;
use Buttress\Concrete\CommandBus\Command\HandlerLocator;
use Buttress\Concrete\Console\Command\Collection\Collection;
use Buttress\Concrete\Console\Command\HelpCommand;
use Buttress\Concrete\Console\Command\PackageCommand;
use Buttress\Concrete\Console\Command\SiteCommand;
use Buttress\Concrete\Exception\BaseException;
use Buttress\Concrete\Exception\ErrorHandler;
use Buttress\Concrete\Exception\RuntimeException;
use Buttress\Concrete\Exception\VersionMismatchException;
use Buttress\Concrete\Locator\Site;
use Buttress\Concrete\Route\Dispatcher;
use Buttress\Concrete\Route\RouteCollector;
use League\CLImate\Argument\Argument;
use League\CLImate\Argument\Manager;
use League\CLImate\CLImate;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Whoops\Handler\PlainTextHandler;
use Whoops\Run;

/**
 * The main entry point to the c5console project
 */
class Console
{

    /** @var \Psr\Container\ContainerInterface */
    public $container;

    /** @var \Buttress\Concrete\Console\Command\Collection */
    public $collection;

    /** @var string The last invoked filename */
    public $filename;

    /** @var string The last invoked command string */
    public $commandName;

    /** @var \Buttress\Concrete\Locator\Site */
    protected $site;

    /** @var \Buttress\Concrete\Exception\ErrorHandler */
    protected $errorHandler;

    /**
     * A list of the available console commands
     * @var string[]
     */
    protected $commands = [
        CacheCommand::class,
        HelpCommand::class,
        PackageCommand::class,
        SiteCommand::class
    ];

    /**
     * A list of CommandBus Handler Providers
     * @var string[]
     */
    protected $providers = [
        DefaultProvider::class
    ];

    public function __construct(
        ContainerInterface $container,
        Collection $collection,
        Site $site,
        ErrorHandler $e
    ) {
        $this->container = $container;
        $this->collection = $collection;
        $this->site = $site;
        $this->errorHandler = $e;
    }

    public function run(array $arguments)
    {
        $this->filename = array_shift($arguments);
        $this->commandName = array_shift($arguments);

        $site = $this->site;
        $cli = $this->container->get(CLImate::class);

        $dispatcher = Dispatcher::simpleDispatcher(function (RouteCollector $routes) use ($site) {
            foreach ($this->collection->all() as $command) {
                $command->registerRoutes($routes, $site);
            }
        });

        return $this->dispatch($dispatcher, $cli);
    }

    /**
     * Prepare to run, this method is used for loading service providers and things like that.
     */
    public function prepare()
    {
        $this->registerCommands();
        $this->registerHandlers();

        $manager = new Manager();
        $manager->add('verbose', [
            'prefix' => 'v',
            'longPrefix' => 'verbose',
            'noValue' => true
        ]);
        $manager->parse();

        if ($manager->get('verbose')) {
            $this->errorHandler->setVerbose(true);
        }

        $this->registerErrorHandler();
        return $this;
    }

    /**
     * @param $dispatcher
     * @return int
     */
    protected function dispatch(Dispatcher $dispatcher, CLImate $cli)
    {
        $command = $this->commandName ?: 'help';

        $result = $dispatcher->dispatch($command);
        switch ($result[0]) {
            case 0:
                $cli->error(sprintf('Command "%s" not found.', $command));
                return 1;
            case 1:
                list(, $callable, $data) = $result;

                return $this->runCallable($cli, $callable, $data);
                break;
            case 2:
                $cli->error(sprintf('Unexpected routing error.'));
                return 1;
        }
    }

    protected function registerCommands()
    {
        foreach ($this->commands as $command) {
            $this->collection->add($this->container->get($command));
        }
    }

    protected function registerHandlers()
    {
        $handler = $this->container->get(HandlerLocator::class);
        foreach ($this->providers as $provider) {
            $this->container->get($provider)->register($handler, $this->site);
        }
    }

    /**
     * Run a callable
     * @param \League\CLImate\CLImate $cli
     * @param callable $callable
     * @param array $data
     * @return int
     */
    private function runCallable(CLImate $cli, $callable, array $data)
    {
        try {
            if (is_string($callable)) {
                if (strpos($callable, '::')) {
                    list($class, $method) = explode('::', $callable, 2);
                    $callable = [$this->container->get($class), $method];
                } else {
                    $callable = $this->container->get($callable);
                }
            }

            if (!is_callable($callable)) {
                throw new RuntimeException('Invalid route callable');
            }

            return $callable($this->site, ...$data);
        } catch (VersionMismatchException $e) {
            $cli->error('Invalid Version: ' . $e->getMessage())
                ->dim(sprintf('Detected version "%s"', $e->getVersion()));
        } catch (BaseException $e) {
            $cli->error('Runtime Error: ' . $e->getMessage());
        }

        return 1;
    }

    public function registerErrorHandler()
    {
        $this->errorHandler->register();
    }
}
