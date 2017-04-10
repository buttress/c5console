<?php

namespace Buttress\Concrete\Console\Command;

use Buttress\Concrete\Console\Command\Manager\CommandManager;
use Buttress\Concrete\Console\Command\Manager\DefinitionFactory;
use Buttress\Concrete\CommandBus\Command\Cache\Clear;
use Buttress\Concrete\Locator\Site;
use Buttress\Concrete\Route\RouteCollector;
use League\CLImate\Argument\Manager;
use League\CLImate\CLImate;
use League\CLImate\Util\Output;
use League\CLImate\Util\Writer\Buffer;
use League\Tactician\CommandBus;

class CacheCommand implements Command
{

    /** @var \League\CLImate\CLImate */
    private $cli;

    /** @var \League\Tactician\CommandBus */
    private $bus;

    public function __construct(CLImate $cli, CommandBus $bus)
    {
        $this->cli = $cli;
        $this->bus = $bus;
    }

    /**
     * Handles `c5 cache:clear`
     */
    public function clear(Site $site)
    {
        list($clear) = $commands = $this->getCommands($site);

        $clear->parse();

        if ($clear->get('quiet')) {
            $this->cli->output->defaultTo('buffer');
        }

        $this->cli->confirm('Really clear the site cache?');

        // Notify that we've started
        $this->cli->dim('Clearing cache...');

        // Create a new commandbus command
        $command = new Clear();

        // Send that command
        $this->bus->handle($command);

        // No exception was thrown so it worked!
        $this->cli->green('Done!');
    }

    /**
     * Get the command definitions this command provides
     *
     * @param \Buttress\Concrete\Locator\Site|null $site
     * @return \League\CLImate\Argument\Manager[] A list containing a manager for each command this instance offers
     */
    public function getCommands(Site $site)
    {
        $commands = [];
        if ($this->enabled($site)) {
            $clear = new CommandManager('cache:clear');
            $clear->description('Clear the site cache');
            $clear->add([
                'quiet' => [
                    'prefix' => 'q',
                    'longPrefix' => 'quiet',
                    'noValue' => true
                ]
            ]);

            $commands[] = $clear;
        }

        return $commands;
    }

    /**
     * Register command routes
     *
     * @param \Buttress\Concrete\Route\RouteCollector $collector
     * @param \Buttress\Concrete\Locator\Site|null $site
     */
    public function registerRoutes(RouteCollector $collector, Site $site = null)
    {
        if ($this->enabled($site)) {
            $collector->addRoute('cache:clear', [$this, 'clear']);
            $collector->addRoute('clear:cache', [$this, 'clear']);
        }
    }

    /**
     * Check if we should be enabled
     *
     * @param \Buttress\Concrete\Locator\Site $site
     * @return bool
     */
    private function enabled(Site $site)
    {
        return ($site && version_compare($site->getVersion(), '5.5.0') > 0);
    }
}
