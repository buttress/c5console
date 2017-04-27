<?php

namespace Buttress\Concrete\Locator;

/**
 * A concrete5 site
 */
final class Site implements SiteInterface
{

    /** @var string The path to the webroot */
    private $path;

    /** @var string The version number */
    private $version;

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     * @return Site
     */
    public function setPath($path)
    {
        $this->path = $path;
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
     * @return Site
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * A simple factory method for creating a site object
     *
     * @param string $path
     * @param string $version
     * @return Site
     */
    public static function create($path = '', $version = '')
    {
        return (new static)->setVersion($version)->setPath($path);
    }
}
