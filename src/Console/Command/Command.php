<?php

namespace Buttress\Concrete\Console\Command;

use Buttress\Concrete\Console\Command\Manager\DefinitionFactory;
use Buttress\Concrete\Locator\Site;
use Buttress\Concrete\Route\RouteCollector;

interface Command
{

    /**
     * Get the command definitions this command provides
     *
     * @param \Buttress\Concrete\Locator\Site|null $site
     * @return \Buttress\Concrete\Console\Command\Manager\CommandManager[]
     */
    public function getCommands(Site $site);

    /**
     * Register routes onto the route collector
     *
     * @param \Buttress\Concrete\Route\RouteCollector $collector
     * @param \Buttress\Concrete\Locator\Site|null $site Null is passed when a concrete5 site wasn't located
     * @return void
     */
    public function registerRoutes(RouteCollector $collector, Site $site = null);
}
