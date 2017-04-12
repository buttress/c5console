<?php

namespace Buttress\Concrete\Service\Package\Driver;

use Buttress\Concrete\Service\Package\PackageItem;
use League\CLImate\CLImate;

interface Driver
{

    /**
     * Install a package
     *
     * @param PackageItem $package
     * @return \Buttress\Concrete\Service\Result
     */
    public function install(PackageItem $package);

    /**
     * Uninstall a package
     *
     * @param PackageItem $package
     * @return \Buttress\Concrete\Service\Result
     */
    public function uninstall(PackageItem $package);

    /**
     * Test a package for install
     *
     * @param PackageItem $package
     * @return \Buttress\Concrete\Service\Result
     */
    public function test(PackageItem $package);

    /**
     * Show information about a package
     *
     * @param PackageItem $package
     * @param \League\CLImate\CLImate $cli
     * @return \Buttress\Concrete\Service\Result
     */
    public function show(PackageItem $package, CLImate $cli);

    /**
     * Get a list of package item objects
     * @return PackageItem[]
     */
    public function all();


}
