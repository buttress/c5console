<?php

namespace Buttress\Concrete\CommandBus\Provider;

use Buttress\Concrete\CommandBus\Command\Cache\Clear;
use Buttress\Concrete\CommandBus\Command\HandlerLocator;
use Buttress\Concrete\CommandBus\Command\Package\Install;
use Buttress\Concrete\CommandBus\Command\Package\ListPackages;
use Buttress\Concrete\CommandBus\Command\Package\Uninstall;
use Buttress\Concrete\CommandBus\Handler\CacheHandler;
use Buttress\Concrete\CommandBus\Handler\PackageHandler;
use Buttress\Concrete\Locator\Site;

class DefaultProvider implements Provider
{

    /**
     * Provide handlers to the HandlerLocator
     *
     * @param \Buttress\Concrete\CommandBus\Command\HandlerLocator $locator
     * @param \Buttress\Concrete\Locator\Site $site
     * @return void
     */
    public function register(HandlerLocator $locator, Site $site)
    {
        $locator->pushHandler(Install::class, PackageHandler::class);
        $locator->pushHandler(Uninstall::class, PackageHandler::class);
        $locator->pushHandler(ListPackages::class, PackageHandler::class);
        $locator->pushHandler(Clear::class, CacheHandler::class);
    }
}
