<?php

namespace Buttress\Concrete\Client\Connection;

interface ConnectionAware
{

    /**
     * Set a connection
     * @param \Buttress\Concrete\Client\Connection\Connection $connection
     */
    public function setConnection(Connection $connection);

    /**
     * Get the connection
     * @return \Buttress\Concrete\Client\Connection\Connection
     */
    public function connection();
}
