<?php

namespace Buttress\Concrete\Client;

use Buttress\Concrete\Client\Connection\Connection;
use Buttress\Concrete\Client\Adapter\Adapter;

class Concrete5 implements Client
{

    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Attempt to disconnect
     * (This isn't going to be fully supported for awhile)
     *
     * @param \Buttress\Concrete\Client\Connection\Connection $connection
     * @return bool
     */
    public function disconnect(Connection $connection)
    {
        return $connection->disconnect();
    }

    /**
     * Get a connection to a concrete5 site
     * @return Connection
     */
    public function connect()
    {
        return $this->adapter->attach();
    }
}
