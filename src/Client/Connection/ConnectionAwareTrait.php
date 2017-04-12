<?php

namespace Buttress\Concrete\Client\Connection;

trait ConnectionAwareTrait
{

    /** @var \Buttress\Concrete\Client\Connection\Connection */
    private $traitConnection;

    /**
     * Get the connection
     * @return \Buttress\Concrete\Client\Connection\Connection
     */
    public function connection()
    {
        return $this->traitConnection;
    }

    /**
     * Set the connection
     * @param mixed $traitConnection
     * @return static
     */
    public function setConnection(Connection $traitConnection)
    {
        $this->traitConnection = $traitConnection;
        return $this;
    }

}
