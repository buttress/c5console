<?php

namespace Buttress\Concrete\CommandBus\Provider;

use Buttress\Concrete\CommandBus\Command\Cache\Clear;
use Buttress\Concrete\CommandBus\Command\HandlerLocator;
use Buttress\Concrete\CommandBus\Command\Package\Install;
use Buttress\Concrete\CommandBus\Command\Package\ListPackages;
use Buttress\Concrete\CommandBus\Command\Package\Uninstall;
use Buttress\Concrete\CommandBus\Handler\Legacy\CacheHandler;
use Buttress\Concrete\CommandBus\Handler\Legacy\PackageHandler;
use Buttress\Concrete\Locator\Site;

/**
 * A handler provider for Legacy versions of concrete5
 */
class LegacyProvider implements Provider
{

    public function register(HandlerLocator $locator, Site $site)
    {
        // If we're not in a concrete5 site, or if we're in a modern site, return
        if (!$site || version_compare($site->getVersion(), '5.7.0') > -1) {
            return;
        }

        // Add a handler for the "Clear" command
        $locator->pushHandler(Clear::class, CacheHandler::class);
        $locator->pushHandler(Install::class, PackageHandler::class);
        $locator->pushHandler(Uninstall::class, PackageHandler::class);
        $locator->pushHandler(ListPackages::class, PackageHandler::class);
    }
}
