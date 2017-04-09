<?php

namespace Buttress\Concrete\Exception;

class VersionMismatchException extends RuntimeException
{

    protected $version;

    public function __construct($message = '', $version = '', $code = 0, $previous = null)
    {
        $this->version = $version;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

}
