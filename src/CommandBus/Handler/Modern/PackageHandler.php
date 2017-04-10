<?php

namespace Buttress\Concrete\CommandBus\Handler\Modern;

use Buttress\Concrete\Adapter\ModernAdapter;
use Buttress\Concrete\CommandBus\Command\Package\Install;
use Buttress\Concrete\CommandBus\Command\Package\ListPackages;
use Buttress\Concrete\CommandBus\Command\Package\Uninstall;
use Buttress\Concrete\Exception\RuntimeException;
use Buttress\Concrete\Locator\Site;
use Concrete\Core\Package\PackageService;
use League\CLImate\CLImate;
use Psr\Log\LoggerInterface;

class PackageHandler
{

    protected $site;

    protected $adapter;

    protected $logger;

    protected $cli;

    public function __construct(Site $site, ModernAdapter $adapter, LoggerInterface $logger, CLImate $cli)
    {
        $this->site = $site;
        $this->adapter = $adapter;
        $this->logger = $logger;
        $this->cli = $cli;
    }

    public function handleListPackages(ListPackages $command)
    {
        $service = $this->getService();
        $installed = $this->names($service->getInstalledList());
        $notInstalled = $this->names($service->getAvailablePackages());

        $this->cli->flank('<green>Installed Packages</green>');
        $this->outputList($installed);


        $this->cli->br()->flank('Available Packages');
        $this->outputList($notInstalled);
    }

    private function outputList(array $list)
    {
        if (count($list) > 8) {
            $this->cli->columns($list);
        } else {
            $this->cli->out($list);
        }
    }

    private function names(array $packages)
    {
        return (array) array_map(function($package) {
            return $package->getPackageHandle();
        }, $packages);
    }

    public function handleInstall(Install $command)
    {
        $service = $this->getService();
        $handle = $command->getHandle();

        if ($package = $this->getUninstalledPackage($service, $handle)) {
            $service->install($package, []);
        } else {
            throw new RuntimeException('Invalid package handle "' . $handle . '"');
        }
    }

    public function handleUninstall(Uninstall $command)
    {
        $service = $this->getService();
        $handle = $command->getHandle();
        if ($package = $this->getInstalledPackage($service, $handle)) {
            $service->uninstall($package);
        } else {
            throw new RuntimeException('Invalid package handle "' . $handle . '"');
        }
    }

    private function getUninstalledPackage(PackageService $service, $handle)
    {
        $packages = $service->getAvailablePackages();
        foreach ($packages as $package) {
            if ($package->getPackageHandle() === $handle) {
                return $package;
            }
        }
    }

    private function getInstalledPackage(PackageService $service, $handle)
    {
        $packages = $service->getAvailablePackages(false);
        foreach ($packages as $package) {
            if ($package->getPackageHandle() === $handle) {
                return $package;
            }
        }
    }

    /**
     * @return PackageService
     */
    private function getService()
    {
        $this->adapter->attach();
        $application = $this->adapter->getApplication();
        $application->bind(LoggerInterface::class, $this->logger);

        /** @var PackageService $service */
        return $application->make(PackageService::class);
    }


}
