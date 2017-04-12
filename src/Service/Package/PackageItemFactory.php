<?php

namespace Buttress\Concrete\Service\Package;

use Package as LegacyPackage;

class PackageItemFactory
{

    /**
     * Get a package item from a modern package object
     *
     * @param \Concrete\Core\Package\Package $package
     * @return \Buttress\Concrete\Service\Package\PackageItem
     */
    public function fromModern($package)
    {
        if ($package instanceof \Concrete\Core\Entity\Package) {
            $installed = true;
        } else {
            $installed = false;
            if (is_array($errors = $package->testForInstall(true))) {
                $installed = in_array($package::E_PACKAGE_INSTALLED, $errors, true);
            }
        }

        return (new PackageItem())
            ->setHandle($package->getPackageHandle())
            ->setVersion($package->getPackageVersion())
            ->setInstalled($errors ? false : $installed)
            ->setInstalledVersion($package->getPackageVersion());
    }

    /**
     * Get a package item from a legacy package object
     * @param \Package $package
     * @return \Buttress\Concrete\Service\Package\PackageItem
     */
    public function fromLegacy(LegacyPackage $package)
    {
        return (new PackageItem())
            ->setHandle($package->getPackageHandle())
            ->setVersion($package->getPackageVersion())
            ->setInstalled($package->isPackageInstalled())
            ->setInstalledVersion($package->getPackageCurrentlyInstalledVersion());
    }

    /**
     * Create from raw values
     *
     * @param string $handle
     * @param string $version
     * @param bool $installed
     * @return \Buttress\Concrete\Service\Package\PackageItem
     */
    public function create($handle, $version, $installed)
    {
        return (new PackageItem())->setHandle($handle)->setVersion($version)->setInstalled($installed);
    }

}
