<?php

namespace Buttress\Concrete\Console\Command;

use Buttress\Concrete\Console\Command\Manager\CommandManager;
use Buttress\Concrete\Console\Console;
use Buttress\Concrete\Locator\Site;
use Buttress\Concrete\Route\RouteCollector;
use League\CLImate\CLImate;

class HelpCommand implements Command
{

    protected $console;
    protected $cli;

    public function __construct(Console $console, CLImate $cli)
    {
        $this->console = $console;
        $this->cli = $cli;
    }

    public function help(Site $site)
    {
        list($help) = $this->getCommands($site);

        $help->parse();
        $command = $help->get('command');
        $cli = $this->cli;

        if ($command && $command !== 'help') {
            return $this->outputComamndUsage($site, $command, $cli);
        }

        return $this->outputDefaultUsage($site, $help, $cli);
    }

    private function table(Site $site, array $commands)
    {
        foreach ($commands as $consoleCommand) {
            $available = $consoleCommand->getCommands($site);

            foreach ($available as $command) {
                yield [
                    '<blue>Command</blue>' => $command->getCommand(),
                    '<blue>Usage</blue>' => $command->shortUsage()
                ];
            }
        }
    }

    /**
     * Get the command definitions this command provides
     *
     * @param \Buttress\Concrete\Locator\Site|null $site
     * @return CommandManager[] A list containing a manager for each command this instance offers
     * @throws \Exception
     */
    public function getCommands(Site $site)
    {
        $help = new CommandManager('help');
        $help->add('command', [
            'description' => 'The command to get Help for',
            'castTo' => 'string'
        ]);

        return [$help];
    }

    /**
     * Register routes onto the route collector
     *
     * @param \Buttress\Concrete\Route\RouteCollector $collector
     * @param \Buttress\Concrete\Locator\Site|null $site Null is passed when a concrete5 site wasn't located
     * @return void
     */
    public function registerRoutes(RouteCollector $collector, Site $site = null)
    {
        $collector->addRoute('help', [$this, 'help']);
    }

    /**
     * @param \Buttress\Concrete\Locator\Site $site
     * @param $help
     * @param $cli
     */
    protected function outputDefaultUsage(Site $site, $help, $cli)
    {
        $help->usage($this->cli);

        $cli->rule();
        $cli->green('Available Commands:');

        $commands = $this->console->collection->all();
        $cli->table(iterator_to_array($this->table($site, $commands)));
    }

    protected function outputComamndUsage(Site $site, $name, $cli)
    {
        $commands = $this->console->collection->all();

        foreach ($commands as $consoleCommand) {
            $available = $consoleCommand->getCommands($site);

            foreach ($available as $command) {
                if ($command->getCommand() === $name) {
                    $command->usage($cli);
                    return 0;
                }
            }
        }

        $cli->error(sprintf('Command "%s" not found.', $name));
        return 1;
    }

}
