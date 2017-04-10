<?php

namespace Buttress\Concrete\CommandBus\Handler\Legacy;

use Buttress\Concrete\Adapter\LegacyAdapter;
use Buttress\Concrete\CommandBus\Command\Package\Install;
use Buttress\Concrete\CommandBus\Command\Package\Uninstall;
use Buttress\Concrete\Exception\RuntimeException;
use League\CLImate\CLImate;
use Package;

class PackageHandler
{

    /** @var \Buttress\Concrete\Adapter\LegacyAdapter */
    private $adapter;

    /** @var \League\CLImate\CLImate */
    private $cli;

    public function __construct(LegacyAdapter $adapter, CLImate $cli)
    {
        $this->adapter = $adapter;
        $this->cli = $cli;
    }

    public function handleListPackages()
    {
        $this->adapter->attach();

        $installed = Package::getInstalledHandles();
        $notInstalled = $this->names(Package::getAvailablePackages());

        $this->cli->flank('<green>Installed Packages</green>');
        $this->outputList($installed);

        $this->cli->br()->flank('Available Packages');
        $this->outputList($notInstalled);
    }

    public function handleInstall(Install $install)
    {
        $this->adapter->attach();
        $handle = $install->getHandle();
        $package = \Loader::package($handle);

        if ($tests = Package::testForInstall($handle, true)) {
            if (is_array($tests)) {
                $errors = Package::mapError($tests);
                throw new RuntimeException(array_shift($errors));
            }

            $package->install();
        }
    }

    public function handleUninstall(Uninstall $uninstall)
    {
        $this->adapter->attach();
        $handle = $uninstall->getHandle();
        if ($package = Package::getByHandle($handle)) {
            $package->uninstall();
        } else {
            throw new RuntimeException('Invalid Package');
        }
    }

    private function outputList(array $packages)
    {
        if (count($packages) < 10) {
            $this->cli->out($packages);
        } else {
            $this->cli->columns($packages);
        }
    }

    private function names(array $packages)
    {
        return array_map(function (Package $package) {
            return $package->getPackageHandle();
        }, $packages);
    }
}
