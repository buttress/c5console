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
     * @return null|string
     */
    public function locateWebroot($scriptDirectory, $workingDirectory)
    {
        // Check the current working directory
        if ($webroot = $this->searchLocation($workingDirectory)) {
            return $webroot;
        }

        // Check the current working directory with /web added the end
        if ($webroot = $this->searchLocation($workingDirectory . '/web')) {
            return $webroot;
        }

        // Check the script location
        if ($webroot = $this->searchLocation($scriptDirectory)) {
            return $webroot;
        }

        return null;
    }

    /**
     * Search a path
     *
     * @param $path
     * @return bool|null|string
     */
    private function searchLocation($path)
    {
        // If we can find a realpath to this path
        if ($path = realpath($path)) {
            do {
                // Check if an index.php file exists
                if (file_exists($path . '/index.php')) {
                    return $path;
                }

                // If not, set the path to the parent directory and check again
                $path = dirname($path);
            } while (strlen($path) > 1);
        }

        return null;
    }
}
