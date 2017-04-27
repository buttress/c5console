<?php

namespace Buttress\Concrete\CommandBus\Handler;

use Buttress\Concrete\Client\Connection\Connection;
use Buttress\Concrete\Client\Connection\LegacyConnection;
use Buttress\Concrete\Client\Connection\ModernConnection;
use Buttress\Concrete\CommandBus\Command\Cache\Clear;
use Buttress\Concrete\Locator\Site;
use Cache;
use Concrete\Core\Site\Service;
use League\CLImate\CLImate;
use Loader;

class CacheHandler
{
    /** @var \Buttress\Concrete\Locator\Site */
    private $site;

    /** @var \League\CLImate\CLImate */
    private $cli;

    /** @var \Buttress\Concrete\Client\Connection\Connection */
    private $connection;

    public function __construct(Connection $connection, CLImate $cli, Site $site)
    {
        $this->connection = $connection;
        $this->cli = $cli;
        $this->site = $site;
    }

    /**
     * Handle the Clear command
     * @param \Buttress\Concrete\CommandBus\Command\Cache\Clear $clear
     */
    public function handleClear(Clear $clear)
    {
        if ($this->connection instanceof ModernConnection) {
            $this->clearModern($this->connection);
        } else {
            $this->clearLegacy($this->connection);
        }
    }

    /**
     * Clear cache for a ModernConnection
     * @param \Buttress\Concrete\Client\Connection\ModernConnection $connection
     */
    private function clearModern(ModernConnection $connection)
    {
        $app = $connection->getApplication();

        if (class_exists(Service::class)) {
            $site = $app->make(Service::class)->getDefault()->getSiteName();
        } else {
            $site = $app['config']['concrete.site'];
        }

        $app->clearCaches();
        $this->notify($site);
    }

    /**
     * Clear cache for a LegacyConnection
     * @param LegacyConnection $connection
     */
    private function clearLegacy(LegacyConnection $connection)
    {
        $site = SITE;
        Loader::library('cache');
        $cache = new Cache;
        $cache->flush();
        $this->notify($site);
    }

    private function notify($site)
    {
        $this->cli->info(
            sprintf('Cleared cache on <bold>%s</bold>', $site)
        );
    }

}
