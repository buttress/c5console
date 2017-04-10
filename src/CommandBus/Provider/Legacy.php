<?php

namespace Buttress\Concrete\CommandBus\Provider;

use Buttress\Concrete\CommandBus\Command\Cache\Clear;
use Buttress\Concrete\CommandBus\Command\HandlerLocator;
use Buttress\Concrete\CommandBus\Handler\Legacy\CacheHandler;
use Buttress\Concrete\Locator\Site;

/**
 * A handler provider for Legacy versions of concrete5
 */
class Legacy implements Provider
{

    public function register(HandlerLocator $locator, Site $site)
    {
        // If we're not in a concrete5 site, or if we're in a modern site, return
        if (!$site || version_compare($site->getVersion(), '5.7.0') > -1) {
            return;
        }

        // Add a handler for the "Clear" command
        $locator->pushHandler(Clear::class, CacheHandler::class);
    }
}
