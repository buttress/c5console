<?php

namespace Buttress\Concrete\Console\Command;

use Buttress\Concrete\Console\Command\Manager\CommandManager;
use Buttress\Concrete\Locator\Locator;
use Buttress\Concrete\Locator\Site;
use Buttress\Concrete\Route\RouteCollector;
use FilesystemIterator as FS;
use League\CLImate\CLImate;

class SiteCommand implements Command
{

    /** @var \Buttress\Concrete\Locator\Locator */
    private $locator;
    /**
     * @var \League\CLImate\CLImate
     */
    private $cli;

    public function __construct(Locator $locator, CLImate $cli)
    {
        $this->locator = $locator;
        $this->cli = $cli;
    }

    /**
     * Get the command definitions this command provides
     *
     * @param \Buttress\Concrete\Locator\Site|null $site
     * @return \Buttress\Concrete\Console\Command\Manager\CommandManager[]
     * @throws \Exception
     */
    public function getCommands(Site $site)
    {
        $locate = new CommandManager('site:locate');
        $locate->add('path', [
            'required' => true,
            'castTo' => 'string',
            'description' => 'The package handle to install',
            'defaultValue' => getcwd()
        ]);

        return [
            'site:locate' => $locate
        ];
    }

    /**
     * Register routes onto the route collector
     *
     * @param \Buttress\Concrete\Route\RouteCollector $collector
     * @param \Buttress\Concrete\Locator\Site|null $site Null is passed when a concrete5 site wasn't located
     * @return void
     */
    public function registerRoutes(RouteCollector $collector, Site $site = null)
    {
        $collector->addGroup(['site', 'sites'], function (RouteCollector $collector) use ($site) {
            $collector->addRoute(['locate', 'find'], function () use ($site) {
                $command = array_get($this->getCommands($site), 'site:locate');
                $command->parse();
                $path = realpath($command->get('path'));
                foreach ($this->locateWebroots($path) as $webroot) {
                    $this->outputWebroot($webroot);
                }
            });
        });
    }

    private function locateWebroots($path)
    {
        $flags = FS::CURRENT_AS_PATHNAME | FS::SKIP_DOTS;
        $files = new \RecursiveDirectoryIterator($path, $flags);

        foreach ($this->directoryGenerator($files) as $path) {
            if (($root = $this->locator->getLocation($path, false)) && $root->getPath() === $path) {
                yield $root;
            }
        }
    }

    /**
     * @param \RecursiveDirectoryIterator $iterator
     * @return \RecursiveDirectoryIterator[]
     */
    private function directoryGenerator(\RecursiveDirectoryIterator $iterator)
    {
        $children = [];
        foreach ($this->visibleChildren($iterator) as $directory) {
            $children[] = $directory;
            yield $directory->getPath();
        }

        // One level deeper
        foreach ($children as $child) {
            foreach ($this->directoryGenerator($child) as $item) {
                yield $item;
            }
        }

    }
    /**
     * @param \RecursiveDirectoryIterator $iterator
     * @return \RecursiveDirectoryIterator[]
     */
    private function visibleChildren(\RecursiveDirectoryIterator $iterator)
    {
        /** @var \RecursiveDirectoryIterator $child */
        foreach ($iterator as $child) {
            if ($iterator->hasChildren() && basename($child)[0] !== '.') {
                yield $iterator->getChildren();
            }
        }
    }

    private function outputWebroot(Site $webroot)
    {
        $version = $stringVersion = $webroot->getVersion();
        $spaces = str_repeat(' ', max(0, 9 - strlen($version)));
        $suffix = '     ';

        if ($remainder = trim(preg_replace('/^[\d\.]+/', '', $version))) {
            $stringVersion = substr($version, 0, -strlen($remainder));
            $stringVersion .= "<error>{$remainder}</error>";
        }

        // Latest major version
        if (version_compare($version, '8.0.0', '>=')) {
            $stringVersion = "<green>{$stringVersion}</green>";
        } elseif (version_compare($version, '5.7.0', '<')) {
            $stringVersion = "<error>{$stringVersion}</error>";
            $suffix = ' <bold><background_light_red>[!]</background_light_red></bold> ';
        }

        $this->cli
            ->inline(
                sprintf(
                    '<bold>%s</bold>%s%s ',
                    $stringVersion,
                    $spaces,
                    $suffix
                )
            )
            ->out($webroot->getPath());
    }
}
