<?php

namespace Buttress\Concrete\Locator;

use Buttress\Concrete\Locator\Detector\Detector;
use Buttress\Concrete\Locator\Detector\LegacyDetector;
use Buttress\Concrete\Locator\Detector\ModernDetector;

/**
 * concrete5 installation locator
 */
class Locator
{

    /** @var \Buttress\Concrete\Locator\Site */
    protected $location;

    /** @var Detector[] */
    protected $detectors = [];

    /** @var \Buttress\Concrete\Locator\WebrootLocator */
    protected $locator;

    public function __construct(WebrootLocator $locator, ModernDetector $modern, LegacyDetector $legacy)
    {
        $this->locator = $locator;
        $this->addDetector($modern);
        $this->addDetector($legacy);
    }

    /**
     * Add a detector to detect with
     *
     * @param \Buttress\Concrete\Locator\Detector\Detector $detector
     */
    public function addDetector(Detector $detector)
    {
        $this->detectors[] = $detector;
    }

    /**
     * Locate the concrete5 core we're wanting
     *
     * @param string|null $currentPath If null is passed, getcwd will be used.
     * @param bool $recursive
     * @return \Buttress\Concrete\Locator\Site|null
     */
    public function getLocation($currentPath = null, $recursive = true)
    {
        // If we weren't given a currentpath, just use the cwd
        if (!$currentPath) {
            $currentPath = getcwd();
        }

        // If we have found a webroot
        if ($path = $this->locator->searchWorkingDirectory($currentPath, $recursive)) {
            // Check each of the detectors to see if we've found a concrete5 site
            foreach ($this->detectors as $detector) {
                if ($result = $detector->detect($path)) {
                    return $result;
                }
            }
        }

        return null;
    }
}
