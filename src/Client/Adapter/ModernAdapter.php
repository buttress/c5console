<?php
namespace Buttress\Concrete\Client\Adapter;

use Buttress\Concrete\Client\Connection\ModernConnection;
use Buttress\Concrete\Console\Console;
use Buttress\Concrete\Locator\Site;

class ModernAdapter implements Adapter
{

    /** @var \Concrete\Core\Application\Application */
    protected static $app;

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
     * Get the attached Application object
     * @return \Concrete\Core\Application\Application
     */
    public function getApplication()
    {
        return static::$app;
    }

    /**
     * Attach to a modern concrete5 site
     * @return void
     */
    public function attach()
    {
        $connection =  new ModernConnection();
        $connection->connect($this->resolveApplication());

        return $connection;
    }

    /**
     * Resolve the application object from a concrete5 site
     * @return \Concrete\Core\Application\Application
     */
    private function resolveApplication()
    {
        $path = $this->site->getPath();
        chdir($path);

        // Define some required constants
        define('DIR_BASE', $path);
        define('C5_ENVIRONMENT_ONLY', true);

        // Load in the required constants
        require $path . '/concrete/bootstrap/configure.php';

        // Load in concrete5's autoloader
        require $path . '/concrete/bootstrap/autoload.php';

        // Get the concrete5 application
        /** @var \Concrete\Core\Application\Application $cms */
        $cms = require $path . '/concrete/bootstrap/start.php';

        // Boot the runtime
        if (method_exists($cms, 'getRuntime')) {
            $runtime = $cms->getRuntime();
            $runtime->boot();
        }

        $this->console->registerErrorHandler();
        return $cms;
    }
}
