<?php

namespace Buttress\Concrete\Client;

use Buttress\Concrete\Client\Adapter\Adapter;
use Buttress\Concrete\Client\Adapter\AdapterFactory;
use Buttress\Concrete\Client\Adapter\LegacyAdapter;
use Buttress\Concrete\Client\Adapter\ModernAdapter;
use Buttress\Concrete\Client\Connection\Connection;
use Buttress\Concrete\Console\Console;
use Buttress\Concrete\Locator\Site;
use League\Container\ServiceProvider\AbstractServiceProvider;

class ServiceProvider extends AbstractServiceProvider
{

    protected $provides = [
        Adapter::class,
        Connection::class
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
        $container = $this->getContainer();

        // Get the adapter instance from the factory
        $container->add(Adapter::class, function(AdapterFactory $factory, Site $site) {
            return $factory->fromSite($site);
        })->withArgument(AdapterFactory::class)->withArgument(Site::class);

        // Connect to concrete5
        $container->add(Connection::class, function(Concrete5 $client, Console $console) {
            if ($result = $client->connect()) {
                $console->registerErrorHandler();
                return $result;
            }
        })->withArguments([Concrete5::class, Console::class]);
    }
}
