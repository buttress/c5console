<?php

namespace Buttress\Concrete\Route;

use PHPUnit\Framework\TestCase;

class DispatcherTest extends TestCase
{

    public function testDispatch()
    {
        $wrapped = $this->createMock(\FastRoute\Dispatcher::class);
        $wrapped->expects($this->once())->method('dispatch')->withConsecutive(['get', 'test']);

        $dispatcher = new Dispatcher($wrapped);
        $dispatcher->dispatch('test');
    }

    public function testSimpleConstructor()
    {
        $this->assertInstanceOf(Dispatcher::class, Dispatcher::simpleDispatcher(function () {
        }));
    }
}
