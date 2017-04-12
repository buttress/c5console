<?php

namespace Buttress\Concrete\Client;

use Buttress\Concrete\Client\Connection\Connection;

interface Client
{

    /**
     * Connect to a concrete5 site
     * @return \Buttress\Concrete\Client\Connection
     */
    public function connect();

    /**
     * Attempt to disconnect
     * (This isn't going to be fully supported for awhile)
     *
     * @param \Buttress\Concrete\Client\Connection\Connection $connection
     * @return bool
     */
    public function disconnect(Connection $connection);

}
