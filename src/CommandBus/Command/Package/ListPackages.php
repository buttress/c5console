<?php

namespace Buttress\Concrete\CommandBus\Command\Package;

class ListPackages
{

    /** @var \League\CLImate\CLImate */
    protected $cli;

    /**
     * @return \League\CLImate\CLImate
     */
    public function getCli()
    {
        return $this->cli;
    }

    /**
     * @param \League\CLImate\CLImate $cli
     * @return ListPackages
     */
    public function setCli($cli)
    {
        $this->cli = $cli;
        return $this;
    }
}
