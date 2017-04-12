<?php

namespace Buttress\Concrete\Service\Package;

class PackageItem
{

    /** @var string */
    private $handle;

    /** @var bool */
    private $installed;

    /** @var string */
    private $version;

    /** @var string */
    private $installedVersion;

    /**
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param string $handle
     * @return PackageItem
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInstalled()
    {
        return $this->installed;
    }

    /**
     * @param bool $installed
     * @return PackageItem
     */
    public function setInstalled($installed)
    {
        $this->installed = $installed;
        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return PackageItem
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getInstalledVersion()
    {
        return $this->installedVersion;
    }

    /**
     * @param string $installedVersion
     * @return PackageItem
     */
    public function setInstalledVersion($installedVersion)
    {
        $this->installedVersion = $installedVersion;
        return $this;
    }
}
