<?php
namespace Buttress\Concrete\CommandBus\Handler\Legacy;

use Buttress\Concrete\Adapter\LegacyAdapter;
use Buttress\Concrete\CommandBus\Command\Cache\Clear;
use Buttress\Concrete\Locator\Site;
use Cache;
use Loader;
use Psr\Log\LoggerInterface;

/**
 * Class CacheHandler
 * @package Buttress\Concrete\CommandBus\Handler\Legacy
 */
class CacheHandler
{

    protected $adapter;
    protected $site;
    protected $logger;

    /**
     * CacheHandler constructor.
     * @param \Buttress\Concrete\Adapter\LegacyAdapter $adapter
     * @param \Buttress\Concrete\Locator\Site $site
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LegacyAdapter $adapter, Site $site, LoggerInterface $logger)
    {
        $this->adapter = $adapter;
        $this->site = $site;
        $this->logger = $logger;
    }

    /**
     * Handles the "clear" command
     *
     * @param \Buttress\Concrete\CommandBus\Command\Cache\Clear $clear
     * @throws \Buttress\Concrete\Exception\VersionMismatchException
     */
    public function handleClear(Clear $clear)
    {
        $this->adapter->attach();

        $this->logger->info('Clearing cache from "{site}"', ['site' => SITE]);

        Loader::library('cache');
        $cache = new Cache;
        $cache->flush();
    }
}
