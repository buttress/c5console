<?php

namespace Buttress\Concrete\Route;

use PHPUnit\Framework\TestCase;

class RouteCollectorTest extends TestCase
{

    public function testWrappedMethods()
    {
        $testCollector = $this->createMock(\FastRoute\RouteCollector::class);
        $testCollector->expects($this->once())->method('getData');
        $testCollector->expects($this->once())->method('addRoute')->with('get', 'test', 'value');
        $testCollector->expects($this->once())->method('addGroup')->with('test:')
            ->willReturnCallback(function($name, $callable) use ($testCollector) {
                return $callable($testCollector);
            });

        $collector = new RouteCollector($testCollector);
        $collector->getData();
        $collector->addGroup('test', function($collector) {
            $this->assertInstanceOf(RouteCollector::class, $collector);
        });
        $collector->addRoute('test', 'value');
    }

    public function testDirect()
    {
        $testCollector = $this->createMock(\FastRoute\RouteCollector::class);
        $collector = new RouteCollector($testCollector);
        $collector->direct(function($testCollector) {
            $this->assertInstanceOf(\FastRoute\RouteCollector::class, $testCollector);
            $this->assertNotInstanceOf(RouteCollector::class, $testCollector);
        });
    }

}
