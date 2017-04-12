<?php

namespace Buttress\Concrete\CommandBus\Handler;

use Buttress\Concrete\Service\Package\Package;
use Buttress\Concrete\Service\Package\PackageItem;
use Buttress\Concrete\CommandBus\Command\Package\Install;
use Buttress\Concrete\CommandBus\Command\Package\ListPackages;
use Buttress\Concrete\CommandBus\Command\Package\Uninstall;
use Buttress\Concrete\Exception\RuntimeException;

class PackageHandler
{

    /** @var \Buttress\Concrete\Service\Package\Package */
    private $package;

    public function __construct(Package $package)
    {
        $this->package = $package;
    }

    public function handleInstall(Install $command)
    {
        $result = $this->package->install((new PackageItem())->setHandle($command->getHandle()));
        if (!$result->success()) {
            $errors = $result->getErrors();
            throw new RuntimeException(reset($errors));
        }
    }

    public function handleUninstall(Uninstall $command)
    {
        $result = $this->package->uninstall((new PackageItem())->setHandle($command->getHandle()));
        if (!$result->success()) {
            $errors = $result->getErrors();
            throw new RuntimeException(reset($errors));
        }
    }

    public function handleListPackages(ListPackages $command)
    {
        $packages = iterator_to_array($this->package->all());
        usort($packages, function(PackageItem $a, PackageItem $b) {
            return $a->isInstalled() ? 1 : -1;
        });

        if ($table = array_map([$this, 'listPackage'], $packages)) {
            $command->getCli()->table($table);
        } else {
            $command->getCli()->out('No packages found.');
        }
    }

    private function listPackage(PackageItem $item)
    {
        if ($item->isInstalled()) {
            $installed = sprintf('<green>%s</green>', $item->getInstalledVersion());
        } else {
            $installed = '<yellow>No</yellow>';
        }

        return [
            'Package Handle' => $item->getHandle(),
            'Version' => $item->getVersion(),
            'Installed' => $installed,
        ];
    }
}
