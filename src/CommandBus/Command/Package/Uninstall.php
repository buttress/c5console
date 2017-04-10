<?php

namespace Buttress\Concrete\CommandBus\Command\Package;

class Uninstall
{

    protected $handle;

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     * @return Install
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
        return $this;
    }

}
