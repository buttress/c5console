<?php

namespace Buttress\Concrete\CommandBus\Provider;

use Buttress\Concrete\CommandBus\Command\Cache\Clear;
use Buttress\Concrete\CommandBus\Command\HandlerLocator;
use Buttress\Concrete\CommandBus\Command\Package\Install;
use Buttress\Concrete\CommandBus\Command\Package\ListPackages;
use Buttress\Concrete\CommandBus\Command\Package\Uninstall;
use Buttress\Concrete\CommandBus\Handler\Modern\CacheHandler;
use Buttress\Concrete\CommandBus\Handler\Modern\PackageHandler;
use Buttress\Concrete\Locator\Site;

/**
 * Modern command handler provider
 */
class ModernProvider implements Provider
{

    public function register(HandlerLocator $locator, Site $site)
    {
        // If we're not in a concrete5 site, or if we're in a legacy site, return
        if (!$site || version_compare($site->getVersion(), '5.7.0') < 0) {
            return;
        }

        // Add handlers
        $locator->pushHandler(Clear::class, CacheHandler::class);
        $locator->pushHandler(Install::class, PackageHandler::class);
        $locator->pushHandler(Uninstall::class, PackageHandler::class);
        $locator->pushHandler(ListPackages::class, PackageHandler::class);
    }
}
