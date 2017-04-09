<?php

namespace Buttress\Concrete\CommandBus\Handler\Modern;

use Buttress\Concrete\Adapter\ModernAdapter;
use Buttress\Concrete\CommandBus\Command\Cache\Clear;

class CacheHandler
{

    /** @var \Buttress\Concrete\Adapter\ModernAdapter */
    protected $adapter;

    public function __construct(ModernAdapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Handle the Clear command
     * @param \Buttress\Concrete\CommandBus\Command\Cache\Clear $clear
     */
    public function handleClear(Clear $clear)
    {
        // Attach to modern concrete5
        $this->adapter->attach();

        // Get the application and clear caches
        $this->communicator->getApplication()->clearCaches();
    }

}
