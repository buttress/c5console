<?php

namespace Buttress\Concrete\Service\Package;

use Buttress\Concrete\Service\Package\Driver\Driver;
use League\CLImate\CLImate;

class Package
{

    protected $driver;

    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Install a package
     *
     * @param PackageItem $package
     * @return \Buttress\Concrete\Service\Result
     */
    public function install(PackageItem $package)
    {
        return $this->driver->install($package);
    }

    /**
     * Uninstall a package
     *
     * @param PackageItem $package
     * @return \Buttress\Concrete\Service\Result
     */
    public function uninstall(PackageItem $package)
    {
        return $this->driver->uninstall($package);
    }

    /**
     * Test a package for install
     *
     * @param PackageItem $package
     * @return \Buttress\Concrete\Service\Result
     */
    public function test(PackageItem $package)
    {
        return $this->driver->test($package);
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
        return $this->driver->show($package, $cli);
    }

    /**
     * Get a list of package item objects
     * @return PackageItem[]
     */
    public function all()
    {
        return $this->driver->all();
    }

}
