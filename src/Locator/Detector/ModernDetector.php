<?php

namespace Buttress\Concrete\Locator\Detector;

use Buttress\Concrete\Locator\Site;

class ModernDetector implements Detector
{

    public function detect($path)
    {
        if (file_exists($path . '/concrete/config/concrete.php')) {
            $data = @include $path . '/concrete/config/concrete.php';
            if (isset($data['version'])) {
                return (new Site())->setPath($path)->setVersion($data['version']);
            }
        }

        return null;
    }
}
