<?php

namespace Buttress\Concrete\Console\Command\Package;

use Buttress\Concrete\CommandBus\Command\Package\Install;
use Buttress\Concrete\CommandBus\Command\Package\ListPackages;
use Buttress\Concrete\CommandBus\Command\Package\Uninstall;
use Buttress\Concrete\Console\Command\PackageCommand;
use Buttress\Concrete\Locator\Site;
use League\CLImate\CLImate;
use League\Tactician\CommandBus;

class InstallController
{

    /** @var \Buttress\Concrete\Console\Command\PackageCommand */
    private $command;

    /** @var \League\Tactician\CommandBus */
    private $bus;

    /** @var \League\CLImate\CLImate */
    private $cli;

    public function __construct(PackageCommand $command, CommandBus $bus, CLImate $cli)
    {
        $this->command = $command;
        $this->bus = $bus;
        $this->cli = $cli;
    }

    /**
     * Handle package:list
     *
     * @param \Buttress\Concrete\Locator\Site $site
     */
    public function listPackages(Site $site)
    {
        $command = new ListPackages();
        $this->bus->handle($command);
    }

    /**
     * Handle package:install
     *
     * @param \Buttress\Concrete\Locator\Site $site
     */
    public function install(Site $site)
    {
        $definition = $this->getDefinition($site, 'package:install');
        $definition->parse();
        $handle = $definition->get('handle');

        if (!$handle) {
            $packages = iterator_to_array($this->getAvailablePackages($site));
            if (!$packages) {
                $this->cli->error('No packages available to install');
            }

            $handle = $this->confirmHandle($packages);
        }

        $this->cli->info('Installing ' . $handle);
        $this->bus->handle((new Install())->setHandle($handle));
        $this->cli->green('Installed Successfully!');
    }

    /**
     * Handle package:uninstall
     *
     * @param \Buttress\Concrete\Locator\Site $site
     */
    public function uninstall(Site $site)
    {
        $definition = $this->getDefinition($site, 'package:uninstall');
        $definition->parse();
        $handle = $definition->get('handle');

        if (!$handle) {
            $packages = iterator_to_array($this->getAvailablePackages($site));
            if (!$packages) {
                $this->cli->error('No packages available to install');
            }

            $handle = $this->confirmHandle($packages);
        }

        $this->cli->info('Installing ' . $handle);
        $this->bus->handle((new Uninstall())->setHandle($handle));
        $this->cli->green('Uninstalled Successfully!');
    }

    private function getDefinition(Site $site, $command)
    {
        /** @var \Buttress\Concrete\Console\Command\Manager\CommandManager[] $definitions */
        $definitions = $this->command->getCommands($site);
        foreach ($definitions as $definition) {
            if ($definition->getCommand() === $command) {
                return $definition;
            }
        }
    }

    private function getAvailablePackages(Site $site)
    {
        $iterator = new \DirectoryIterator($site->getPath() . '/packages');

        foreach ($iterator as $item) {
            if (!$item->isDot() && $item->isDir()) {
                yield $item->getBasename();
            }
        }
    }

    private function confirmHandle($packages)
    {
        $this->cli->green()->columns($packages)->br();

        $input = $this->cli->input('Which package?');
        $input->accept($packages);
        $input->strict();

        return $input->prompt();
    }

}
