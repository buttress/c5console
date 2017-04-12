<?php

namespace Buttress\Concrete\Service\Package;

use Buttress\Concrete\Service\Package\Driver\LegacyDriver;
use Buttress\Concrete\Service\Package\Driver\ModernDriver;
use Buttress\Concrete\Locator\Site;
use Psr\Container\ContainerInterface;

class DriverFactory
{

    /** @var \Buttress\Concrete\Locator\Site */
    private $site;

    /** @var \Buttress\Concrete\Service\Package\Container */
    private $container;

    public function __construct(Site $site, ContainerInterface $container)
    {
        $this->site = $site;
        $this->container = $container;
    }

    public function getDriver(Site $site)
    {
        $version = version_compare($site->getVersion(), '5.6.999999');
        $legacy = $version === -1;

        return $this->container->get($legacy ? LegacyDriver::class : ModernDriver::class);
    }

}
