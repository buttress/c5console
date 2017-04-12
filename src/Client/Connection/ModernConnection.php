<?php

namespace Buttress\Concrete\Client\Connection;

use Concrete\Core\Application\Application;

class ModernConnection implements Connection
{

    protected $application;

    /**
     * Connect to modern concrete5
     * @param \Concrete\Core\Application\Application $application
     */
    public function connect(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Determine if this connection is connected
     * @return mixed
     */
    public function isConnected()
    {
        return $this->application !== null;
    }

    /**
     * Disconnect a connection
     * @return bool Success or failure
     */
    public function disconnect()
    {
        if (!$this->isConnected()) {
            return false;
        }

        $this->application = null;
        return true;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }
}
