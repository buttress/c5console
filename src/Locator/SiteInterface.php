<?php

namespace Buttress\Concrete\Locator;

/**
 * A concrete5 site
 */
interface SiteInterface
{
    /**
     * @return mixed
     */
    public function getPath();

    /**
     * @return string
     */
    public function getVersion();
}
