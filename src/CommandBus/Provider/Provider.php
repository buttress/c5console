<?php

namespace Buttress\Concrete\CommandBus\Provider;

use Buttress\Concrete\CommandBus\Command\HandlerLocator;
use Buttress\Concrete\Locator\Site;

/**
 * CommandBus provider
 * Providers add handlers to the command bus HandlerLocator
 */
interface Provider
{

    /**
     * Provide handlers to the HandlerLocator
     *
     * @param \Buttress\Concrete\CommandBus\Command\HandlerLocator $locator
     * @param \Buttress\Concrete\Locator\Site $site
     * @return void
     */
    public function register(HandlerLocator $locator, Site $site);
}
