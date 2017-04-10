<?php

namespace Buttress\Concrete\Console\Command\Manager;

use Buttress\Concrete\Console\Command\Argument\Parser;
use Buttress\Concrete\Console\Command\Argument\Summary;
use Buttress\Concrete\Exception\RuntimeException;
use League\CLImate\Argument\Argument;
use League\CLImate\Argument\Filter;
use League\CLImate\Argument\Manager;
use League\CLImate\CLImate;

class CommandManager extends Manager
{
    /** @var string */
    protected $name;

    public function __construct($command='')
    {
        $this->command($command);

        $this->filter  = new Filter();
        $this->summary = new Summary();
        $this->parser  = new Parser();
    }

    /**
     * Set the name of this command
     * @param string $name
     */
    public function command($name)
    {
        $this->name = $name;
    }

    public function getCommand()
    {
        return $this->name;
    }

    /**
     * Add an argument.
     *
     * @throws \Exception if $argument isn't an array or Argument object.
     * @param Argument|string|array $argument
     * @param $options
     */
    public function add($argument, array $options = [])
    {
        if (is_array($argument)) {
            $this->addMany($argument);
            return;
        }

        if (is_string($argument)) {
            $argument = Argument::createFromArray($argument, $options);
        }

        if (!($argument instanceof Argument)) {
            throw new RuntimeException('Please provide an argument name or object.');
        }

        $this->arguments[$argument->name()] = $argument;
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

    /**
     * Get a script's short statement.
     */
    public function shortUsage()
    {
        return $this->summary
            ->setDescription($this->description)
            ->setCommand($this->name)
            ->setFilter($this->filter, $this->all())
            ->summarize();
    }

}
