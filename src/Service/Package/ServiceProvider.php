<?php

namespace Buttress\Concrete\Service\Package;

use Buttress\Concrete\Service\Package\Driver\Driver;
use Buttress\Concrete\Service\Package\Driver\LegacyDriver;
use Buttress\Concrete\Service\Package\Driver\ModernDriver;
use Buttress\Concrete\Locator\Site;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{

    protected $provides = [
        Driver::class,
        ModernDriver::class,
        LegacyDriver::class
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->container property or the `getContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        $this->getContainer()->add(Driver::class, function(DriverFactory $factory, Site $site) {
            return $factory->getDriver($site);
        })->withArgument(DriverFactory::class)->withArgument(Site::class);
    }
}
