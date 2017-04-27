<?php

namespace Buttress\Concrete\Client\Connection;

class LegacyConnection implements Connection
{

    /**
     * Test if this connection is connected
     * @return bool
     */
    public function isConnected()
    {
        return class_exists(\Concrete5_Model_Collection::class);
    }

    /**
     * Disconnect this connection
     * @return bool
     */
    public function disconnect()
    {
        return !$this->isConnected();
    }

}
