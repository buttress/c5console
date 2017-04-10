<?php

namespace Buttress\Concrete\CommandBus\Command;

use League\Tactician\Exception\MissingHandlerException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class HandlerLocatorTest extends TestCase
{

    public function testResolvesHandle()
    {
        $input = 'test';
        $output = 'worked';
        $command = 'somecommand';

        // Make a mock container that returns $output when we ask for $input
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->method('has')->willReturn(true);
        $container->method('get')->willReturnMap([[$input, $output]]);

        // Create our test object
        $locator = new HandlerLocator($container);

        // Map 'example' to the input
        $locator->pushHandler($command, $input);

        // Make sure that when we ask for 'example' we resolve all the way back to 'worked'
        $this->assertEquals($output, $locator->getHandlerForCommand($command));
    }

    public function testMissingHandler()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $this->expectException(MissingHandlerException::class);
        (new HandlerLocator($container))->getHandlerForCommand('test');
    }

    public function testMissingDefinition()
    {
        $container = $this->getMockForAbstractClass(ContainerInterface::class);
        $container->expects($this->once())->method('has')->willReturn(false);

        $this->expectException(MissingHandlerException::class);
        $handler = new HandlerLocator($container);
        $handler->pushHandler('test', 'fake');

        $handler->getHandlerForCommand('test');
    }

}
