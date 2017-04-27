<?php

namespace Buttress\Concrete\Service\Package\Driver;

use Buttress\Concrete\Client\Connection\Connection;
use Buttress\Concrete\Service\Package\PackageItem;
use Buttress\Concrete\Service\Package\PackageItemFactory;
use Buttress\Concrete\Service\Result;
use Buttress\Concrete\Exception\RuntimeException;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Core\Package\BrokenPackage;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Support\Facade\Database;
use Concrete\Core\Support\Facade\DatabaseORM;
use Concrete\Core\Support\Facade\Events;
use League\CLImate\CLImate;

class ModernDriver implements Driver
{

    /** @var \Buttress\Concrete\Client\Connection\ModernConnection */
    private $connection;

    /** @var \Buttress\Concrete\Service\Package\PackageItemFactory */
    private $factory;

    public function __construct(Connection $connection, PackageItemFactory $factory)
    {
        $this->connection = $connection;
        $this->factory = $factory;
    }

    /**
     * Get a package object from an Item
     * @return Package|\Concrete\Core\Entity\Package
     */
    private function getPackage(PackageItem $item)
    {
        return Package::getClass($item->getHandle());
    }

    /**
     * Install a package
     *
     * @param PackageItem $item
     * @return \Buttress\Concrete\Service\Result
     */
    public function install(PackageItem $item)
    {
        if (!$this->connection->isConnected()) {
            return new Result(false, 'Not connected to a concrete5 site.');
        }

        if (!$package = $this->getPackage($item)) {
            return new Result(false, 'Invalid package handle.');
        }

        if ($package instanceof BrokenPackage) {
            return new Result(false, $package->getInstallErrorMessage());
        }

        if ($package->showInstallOptionsScreen()) {
            return new Result(false,
                'Install options are not currently supported. Please install through the dashboard.');
        }

        $loader = new ClassLoader();
        if (method_exists($loader, 'registerPackageCustomAutoloaders')) {
            $loader->registerPackageCustomAutoloaders($package);
        }

        if (is_object($tests = $this->testForInstall($package))) {
            return new Result(false, $tests->getList());
        }

        try {
            $app = $this->connection->getApplication();

            if ($app->bound(PackageService::class)) {
                $result = $app->make(PackageService::class)->install($package, []);
            } else {
                $result = $package->install([]);
            }
        } catch (\Exception $e) {
            return new Result(false, $e->getMessage());
        }

        if ($result instanceof ErrorList) {
            return new Result(false, $result->getList());
        }

        return new Result();
    }

    /**
     * Uninstall a package
     *
     * @param PackageItem $item
     * @return \Buttress\Concrete\Service\Result
     */
    public function uninstall(PackageItem $item)
    {
        if (!$this->connection->isConnected()) {
            return new Result(false, 'Not connected to concrete5 site.');
        }

        if (!$package = $this->getPackage($item)) {
            return new Result(false, 'Invalid Package');
        }

        if ($package instanceof Package) {
            return new Result(false, 'That package doesn\'t look installed.');
        }

        $controller = $package->getController();
        if (is_object($tests = $controller->testForUninstall())) {
            return new Result(false, $tests->getList());
        }

        $loader = new ClassLoader();
        $loader->registerPackageCustomAutoloaders($package);

        $result = $package->uninstall();
        if ($result instanceof ErrorList) {
            return new Result(false, $result->getList());
        }

        return new Result;
    }

    /**
     * Test a package for install
     *
     * @param PackageItem $item
     * @return \Buttress\Concrete\Service\Result
     */
    public function test(PackageItem $item)
    {
        if (!$this->connection->isConnected()) {
            return new Result(false, 'Not connected to concrete5 site.');
        }

        if (!$package = $this->getPackage($item)) {
            return new Result(false, 'Invalid Package');
        }

        return $package->getController()->testForInstall();
    }

    /**
     * Show information about a package
     *
     * @param PackageItem $package
     * @param \League\CLImate\CLImate $cli
     * @return \Buttress\Concrete\Service\Result
     */
    public function show(PackageItem $package, CLImate $cli)
    {
        if (!$this->connection->isConnected()) {
            return new Result(false, 'Not connected to concrete5 site.');
        }

        $cli->flank($package->getHandle());
        $cli->out($package->isInstalled() ? 'Installed Version ' : 'Not Installed: ');
        $cli->bold()->inline($package->getVersion());
    }

    /**
     * Get a list of package item objects
     * @return PackageItem[]
     */
    public function all()
    {
        if (!$this->connection->isConnected()) {
            throw new RuntimeException('Not connected to concrete5 site.');
        }

        $installed = Package::getInstalledList();
        foreach ($installed as $item) {
            yield $this->factory->fromModern($item);
        }

        $notInstalled = Package::getAvailablePackages();
        foreach ($notInstalled as $item) {
            yield $this->factory->fromModern($item);
        }
    }

    private function testForInstall($package)
    {
        $method = new \ReflectionMethod($package, 'testForInstall');
        if ($method->isStatic()) {
            return \Package::testForInstall($package->getPackageHandle(), true);
        } else {
            return $package->testForInstall(true);
        }
    }
}
