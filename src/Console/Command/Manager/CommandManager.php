<?php

namespace Buttress\Concrete\Console\Command\Manager;

use League\CLImate\Argument\Manager;
use League\CLImate\CLImate;

class CommandManager extends Manager
{
    /** @var string */
    protected $name;

    public function __construct($command='')
    {
        $this->command($command);
        parent::__construct();
    }

    /**
     * Set the name of this command
     * @param string $name
     */
    public function command($name)
    {
        $this->name = $name;
    }

    /**
     * Output a script's usage statement.
     *
     * @param CLImate $climate
     * @param array $argv
     */
    public function usage(CLImate $climate, array $argv = null)
    {
        $this->summary
            ->setClimate($climate)
            ->setDescription($this->description)
            ->setCommand($this->name)
            ->setFilter($this->filter, $this->all())
            ->output();
    }

}
