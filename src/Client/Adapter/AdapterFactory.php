<?php

namespace Buttress\Concrete\Client\Adapter;

use Buttress\Concrete\Client\Adapter\LegacyAdapter;
use Buttress\Concrete\Client\Adapter\ModernAdapter;
use Buttress\Concrete\Locator\Site;
use Psr\Container\ContainerInterface;

class AdapterFactory
{

    /** @var \Psr\Container\ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Get an adapter from a site object
     *
     * @param \Buttress\Concrete\Locator\Site $site
     * @return mixed
     */
    public function fromSite(Site $site)
    {
        $version = version_compare($site->getVersion(), '5.6.999999');
        $legacy = $version === -1;

        return $this->container->get($legacy ? LegacyAdapter::class : ModernAdapter::class);
    }

}
