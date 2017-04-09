<?php

namespace Buttress\Concrete\Console\Command\Collection;

use Buttress\Concrete\Console\Command\Command;

class Collection
{

    protected $commands = [];

    /**
     * Add a command to the stack
     * @param Command $command
     */
    public function add(Command $command)
    {
        $this->commands[] = $command;
    }

    /**
     * Get all commands that exist
     * @return Command[]
     */
    public function all()
    {
        return $this->commands;
    }

}
