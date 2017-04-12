<?php

namespace Buttress\Concrete\Service\Package\Driver;

use Buttress\Concrete\Client\Connection\Connection;
use Buttress\Concrete\Service\Package\PackageItem;
use Buttress\Concrete\Service\Result;
use League\CLImate\CLImate;
use Package;

class LegacyDriver implements Driver
{

    /** @var \Buttress\Concrete\Client\Connection\Connection */
    private $connection;

    /** @var \Buttress\Concrete\Service\Package\PackageItemFactory */
    private $factory;

    public function __construct(Connection $connection, PackageItemFactory $factory)
    {
        $this->connection = $connection;
        $this->factory = $factory;
    }

    /**
     * @param \Buttress\Concrete\Service\Package\PackageItem $package
     * @return Package|null
     */
    private function getPackage(PackageItem $package)
    {
        $package = Loader::package($package->getHandle());
        return is_object($package) ? $package : null;
    }

    /**
     * Install a package
     *
     * @param \Buttress\Concrete\Service\Package\PackageItem $item
     * @return \Buttress\Concrete\Service\Result
     */
    public function install(PackageItem $item)
    {
        if (!$this->connection->isConnected()) {
            return new Result(false, 'Not connected to concrete5 site.');
        }

        if (!$package = $this->getPackage($item)) {
            return new Result(false, 'Invalid package handle.');
        }

        if ($package->showInstallOptionsScreen()) {
            return new Result(false, 'Install options are not currently supported. Please install through the dashboard.');
        }

        $tests = $this->test($package);
        if (!$tests->success()) {
            return $tests;
        }

        try {
            $package->install([]);
        } catch (Exception $e) {
            return new Result(false, [$e->getMessage()]);
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
            return new Result(false, 'Invalid package handle.');
        }

        try {
            $package->uninstall();
        } catch (\Exception $e) {
            return new Result(false, [$e->getMessage()]);
        }

        return new Result();
    }

    /**
     * Test a package for install
     *
     * @param PackageItem $package
     * @return \Buttress\Concrete\Service\Result
     */
    public function test(PackageItem $package)
    {
        if (!$this->connection->isConnected()) {
            return new Result(false, 'Not connected to concrete5 site.');
        }

        $package = $this->getPackage($package);
        $errors = [];

        if (is_array($tests = Package::testForInstall($package, false))) {
            $errors = Package::mapError($tests);
        }

        return new Result((bool) $errors, $errors);
    }

    /**
     * Show information about a package
     *
     * @param PackageItem $package
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
            return new Result(false, 'Not connected to concrete5 site.');
        }

        $packages = Package::getAvailablePackages(false);

        foreach ($packages as $package) {
            yield $this->factory->fromLegacy($package);
        }
    }
}
