<?php

namespace Buttress\Concrete\Locator\Detector;

use Buttress\Concrete\Locator\Site;

final class LegacyDetector implements Detector
{

    public function detect($path)
    {
        if (file_exists($path . '/concrete/config/version.php')) {
            if (!defined('C5_EXECUTE')) {
                define('C5_EXECUTE', true);
            }

            @include $path . '/concrete/config/version.php';

            if (isset($APP_VERSION) && $APP_VERSION) {
                return Site::create($path, $APP_VERSION);
            }
        }

        return null;
    }

}
