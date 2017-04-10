<?php

namespace Buttress\Concrete\Console\Command\Argument;

class Parser extends \League\CLImate\Argument\Parser
{

    /**
     * Pull a command name and arguments from $argv.
     *
     * @param array $argv
     * @return array
     */
    protected function getCommandAndArguments(array $argv = null)
    {
        // If no $argv is provided then use the global PHP defined $argv.
        if (is_null($argv)) {
            global $argv;
        }

        $arguments = $argv;
        $script    = array_shift($arguments);
        $command   = array_shift($arguments);

        return compact('arguments', 'script', 'command');
    }
}
