<?php

namespace Buttress\Concrete\Console\Command;

use Buttress\Concrete\Console\Command\Manager\CommandManager;
use Buttress\Concrete\Locator\Site;
use Buttress\Concrete\Route\RouteCollector;

class PackageCommand implements Command
{

    public function getCommands(Site $site)
    {
        // Install definition
        $install = new CommandManager('package:install');
        $install->add('handle', [
            'handle' => [
                'required' => true,
                'castTo' => 'string',
                'description' => 'The package handle to install'
            ]
        ]);

        // Uninstall definition
        $uninstall = new CommandManager('package:uninstall');
        $uninstall->add('handle', [
            'handle' => [
                'required' => true,
                'castTo' => 'string',
                'description' => 'The package handle to uninstall'
            ]
        ]);

        return [$install, $uninstall];
    }

    public function registerRoutes(RouteCollector $collector, Site $site = null)
    {
        $collector->addGroup('package', function(RouteCollector $collector) {
            // install
            $collector->addRoute(
                'install',
                'Buttress\Concrete\Console\Command\Package\InstallController::install'
            );

            // uninstall
            $collector->addRoute(
                'uninstall',
                'Buttress\Concrete\Console\Command\Package\InstallController::uninstall'
            );

            // list
            $collector->addRoute(
                'list',
                'Buttress\Concrete\Console\Command\Package\InstallController::listPackages'
            );
        });
    }

}
