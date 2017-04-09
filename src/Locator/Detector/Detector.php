<?php

namespace Buttress\Concrete\Locator\Detector;

interface Detector
{

    /**
     * Detect a concrete5 site
     *
     * @param string $path The current root
     * @return null|\Buttress\Concrete\Locator\Site
     */
    public function detect($path);

}
