<?php

namespace Buttress\Concrete\Service\Package;

use Loader;
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

            $method = new \ReflectionMethod($package, 'testForInstall');
            if ($method->isStatic()) {
                $installed = $package->isPackageInstalled();
                $errors = $package::testForInstall($package->getPackageHandle(), true);
            } else {
                $errors = $package->testForInstall(true);
            }

            if (is_array($errors)) {
                $installed = in_array($package::E_PACKAGE_INSTALLED, $errors, true);
            }

            if ($installed) {
                $errors = false;
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
        $installedVersion = null;
        Loader::model('package');
        if ($installed = LegacyPackage::getByHandle($package->getPackageHandle())) {
            $package = $installed;
            $installedVersion = $this->getPackageVersionInstalled(Loader::db(), $package);
        }

        return (new PackageItem())
            ->setHandle($package->getPackageHandle())
            ->setVersion($package->getPackageVersion())
            ->setInstalled((bool) $package->isPackageInstalled())
            ->setInstalledVersion($installedVersion);
    }

    private function getPackageVersionInstalled(\ADOConnection $db, LegacyPackage $package)
    {
        return $db->GetOne('SELECT pkgVersion from Packages where pkgID=?', [$package->getPackageID()]);
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
