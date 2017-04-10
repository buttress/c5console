<?php
namespace Buttress\Concrete\Adapter;

use Buttress\Concrete\Exception\VersionMismatchException;
use Buttress\Concrete\Locator\Site;

/**
 * An adapter that connects to legacy concrete5 sites
 * @todo Determine the lowest version of c5 we support
 */
class LegacyAdapter implements Adapter
{

    /** @var \Buttress\Concrete\Locator\Site $site */
    protected $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Attach to legacy concrete5
     * @throws \Buttress\Concrete\Exception\VersionMismatchException
     */
    public function attach()
    {
        // If the version number is more than 5.6.999, we have the wrong version.
        if (version_compare(PHP_VERSION, '5.6.999') > 0) {
            throw new VersionMismatchException('Legacy versions of concrete5 do not support PHP 7!', PHP_VERSION);
        }

        // Get the site path
        $path = $this->site->getPath();

        // Change the cwd to the site path
        chdir($path);

        // Define a couple things concrete5 expects
        define('DIR_BASE', $path);
        define('C5_ENVIRONMENT_ONLY', true);

        // Set the error reporting low
        error_reporting(E_ALL | ~E_NOTICE | ~E_WARNING | ~E_STRICT);

        // Include Adodb first, not sure why this was needed
        require_once $path . '/concrete/libraries/3rdparty/adodb/adodb.inc.php';

        // Load in legacy dispatcher
        require_once $path . '/concrete/dispatcher.php';
    }
}
