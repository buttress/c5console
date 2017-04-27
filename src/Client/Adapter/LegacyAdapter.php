<?php
namespace Buttress\Concrete\Client\Adapter;

use Buttress\Concrete\Client\Connection\LegacyConnection;
use Buttress\Concrete\Console\Console;
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

    /** @var \Buttress\Concrete\Console\Console */
    protected $console;

    public function __construct(Site $site, Console $console)
    {
        $this->site = $site;
        $this->console = $console;
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

        $this->console->registerErrorHandler();

        // Adodb Stuff
        $GLOBALS['ADODB_ASSOC_CASE'] = 2;
        $GLOBALS['ADODB_ACTIVE_CACHESECS'] = 300;
        $GLOBALS['ADODB_CACHE_DIR'] = DIR_FILES_CACHE_DB;

        return new LegacyConnection();
    }
}
