<?php

namespace Buttress\Concrete\Console;

use Buttress\Concrete\CommandBus\Command\HandlerLocator;
use Buttress\Concrete\CommandBus\Provider\Provider;
use Buttress\Concrete\Console\Command\Collection\Collection;
use Buttress\Concrete\Console\Command\Command;
use Buttress\Concrete\Locator\Site;
use Buttress\Concrete\Route\RouteCollector;
use League\CLImate\CLImate;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class ConsoleTest extends TestCase
{

    public function testPreparesCommands()
    {
        $command = $this->getMockForAbstractClass(Command::class);

        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->method('get')->willReturnMap([['test', $command]]);

        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())->method('add')->with($command);

        $console = new Console($container, $collection, Site::create('', ''));
        $this->setProperty($console, 'providers', []);
        $this->setProperty($console, 'commands', ['test']);

        $console->prepare();
    }

    public function testPreparesHandlers()
    {
        $site = Site::create('', '');

        $handler = $this->createMock(HandlerLocator::class);

        $provider = $this->getMockForAbstractClass(Provider::class);
        $provider->expects($this->once())->method('register')->with($handler, $site);

        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->method('get')->willReturnMap([[HandlerLocator::class, $handler], ['test', $provider]]);

        $collection = $this->createMock(Collection::class);

        $console = new Console($container, $collection, $site);
        $this->setProperty($console, 'providers', ['test']);
        $this->setProperty($console, 'commands', []);

        $console->prepare();
    }

    public function testRun()
    {
        $site = Site::create('', '');
        $ran = false;

        $command = $this->getMockForAbstractClass(Command::class);
        $command->expects($this->exactly(2))->method('registerRoutes')
            ->willReturnCallback(function (RouteCollector $collector) use ($command, &$ran) {
                $collector->addRoute('test', function () use (&$ran) {
                    $ran = true;
                });
            });

        $collection = $this->createMock(Collection::class);
        $collection->method('all')->willReturn([$command]);

        $cli = $this->createMock(CLImate::class);

        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->method('get')->willReturnMap([[CLImate::class, $cli]]);

        $console = new Console($container, $collection, $site);
        $console->run([]);
        $console->run(['c5', 'test']);

        $this->assertTrue($ran, 'The route callback never ran.');
    }

    private function setProperty($object, $name, $value)
    {
        $class = new \ReflectionClass($object);
        $property = $class->getProperty($name);
        if (!$property->isPublic()) {
            $property->setAccessible(true);
        }

        $property->setValue($object, $value);
    }
}
