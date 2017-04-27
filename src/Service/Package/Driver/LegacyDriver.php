<?php

namespace Buttress\Concrete\Service\Package\Driver;

use Buttress\Concrete\Client\Connection\Connection;
use Buttress\Concrete\Exception\RuntimeException;
use Buttress\Concrete\Service\Package\PackageItem;
use Buttress\Concrete\Service\Package\PackageItemFactory;
use Buttress\Concrete\Service\Result;
use League\CLImate\CLImate;
use Loader;
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
     * @param \Buttress\Concrete\Service\Package\PackageItem $item
     * @return Package|null
     */
    private function getPackage(PackageItem $item)
    {
        if (!$package = Package::getByHandle($item->getHandle())) {
            $package = Loader::package($item->getHandle());
        }
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

        // Fill the item with real data
        $item = $this->factory->fromLegacy($package);

        if ($item->isInstalled()) {
            return new Result(false, sprintf('<underline><bold>%s</bold></underline> is already installed.', $package->getPackageName()));
        }

        if ($package->showInstallOptionsScreen()) {
            return new Result(false, 'Install options are not currently supported. Please install through the dashboard.');
        }

        $tests = $this->test($item);
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

        // Fill the item with real data
        $item = $this->factory->fromLegacy($package);

        if (!$item->isInstalled()) {
            return new Result(false, sprintf('<underline><bold>%s</bold></underline> hasn\'t been installed yet.', $package->getPackageName()));
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

        if (is_array($tests = $package->testForInstall($package->getPackageHandle(), false))) {
            $errors = Package::mapError($tests);
        }

        return new Result((bool) !$errors, $errors);
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
     * @throws \Buttress\Concrete\Exception\RuntimeException
     */
    public function all()
    {
        if (!$this->connection->isConnected()) {
            throw new RuntimeException('Not connected to concrete5 site.');
        }

        $packages = Package::getAvailablePackages(false);

        foreach ($packages as $package) {
            yield $this->factory->fromLegacy($package);
        }
    }
}
