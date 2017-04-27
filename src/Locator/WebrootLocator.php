<?php

namespace Buttress\Concrete\Locator;

/**
 * Locate a concrete5 webroot
 * @package Buttress\Concrete\Locator
 */
class WebrootLocator
{

    /**
     * Locate a webroot
     *
     * @param string $scriptDirectory
     * @param string $workingDirectory
     * @param bool $recursive
     * @return null|string
     */
    public function locateWebroot($scriptDirectory, $workingDirectory, $recursive = true)
    {
        if ($root = $this->searchWorkingDirectory($workingDirectory, $recursive)) {
            return $root;
        }

        if ($root = $this->searchScriptDirectory($scriptDirectory, $recursive)) {
            return $root;
        }
    }

    /**
     * Search a working directory for a site
     *
     * @param $workingDirectory
     * @param bool $recursive
     * @return mixed
     */
    public function searchWorkingDirectory($workingDirectory, $recursive = true)
    {
        // Check the current working directory
        if ($webroot = $this->searchPath($workingDirectory, $recursive)) {
            return $webroot;
        }

        // Check the current working directory with /web added the end
        if ($webroot = $this->searchPath($workingDirectory . '/web', $recursive)) {
            return $webroot;
        }
    }

    /**
     * Search the current script directory for a webroot
     * This would only happen if this is installed as a concrete5 package
     *
     * @param $scriptDirectory
     * @param bool $recursive
     * @return string|null
     */
    public function searchScriptDirectory($scriptDirectory, $recursive = true)
    {
        // Check the current directory
        if ($webroot = $this->searchPath($scriptDirectory, false)) {
            return $webroot;
        }

        // Check one level up
        if ($webroot = $this->searchPath(dirname($scriptDirectory), false)) {
            return $webroot;
        }

        // Check two levels up and do it recursively if needed
        if ($webroot = $this->searchPath(dirname(dirname($scriptDirectory)), $recursive)) {
            return $webroot;
        }

        return null;
    }

    /**
     * Search a path
     *
     * @param $path
     * @param bool $recursive
     * @return bool|null|string
     */
    public function searchPath($path, $recursive = true)
    {
        // If we can find a realpath to this path
        if ($path = realpath($path)) {
            do {
                // Check if an index.php file exists
                if (file_exists($path . '/index.php')) {
                    return $path;
                }

                if (!$recursive) {
                    break;
                }
                // If not, set the path to the parent directory and check again
                $path = dirname($path);
            } while (strlen($path) > 1);
        }

        return null;
    }
}
