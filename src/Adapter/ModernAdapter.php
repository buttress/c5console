<?php
namespace Buttress\Concrete\Adapter;

use Buttress\Concrete\Locator\Site;

class ModernAdapter implements Adapter
{

    /** @var \Concrete\Core\Application\Application */
    protected $app;

    /** @var \Buttress\Concrete\Locator\Site $site */
    protected $site;

    public function __construct(Site $site)
    {
        $this->site = $site;
    }

    /**
     * Get the attached Application object
     * @return \Concrete\Core\Application\Application
     */
    public function getApplication()
    {
        return $this->app;
    }

    /**
     * Attach to a modern concrete5 site
     * @return void
     */
    public function attach()
    {
        $this->app = $this->resolveApplication();
    }

    /**
     * Resolve the application object from a concrete5 site
     * @return \Concrete\Core\Application\Application
     */
    private function resolveApplication()
    {
        $path = $this->site->getPath();

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
        $runtime = $cms->getRuntime();
        $runtime->boot();

        return $cms;
    }

}
