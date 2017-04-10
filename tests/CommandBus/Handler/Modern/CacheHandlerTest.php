<?php

namespace Buttress\Concrete\CommandBus\Handler\Modern;

use Buttress\Concrete\Adapter\ModernAdapter;
use Buttress\Concrete\CommandBus\Command\Cache\Clear;
use PHPUnit\Framework\TestCase;

class CacheHandlerTest extends TestCase
{

    public function testClearsCache()
    {
        $app = $this->getMockBuilder(\Concrete\Core\Application\Application::class)
            ->setMethods(['clearCaches'])->getMock();

        $adapter = $this->createMock(ModernAdapter::class);
        $adapter->method('getApplication')->willReturn($app);

        $testHandler = new CacheHandler($adapter);

        $app->expects($this->once())->method('clearCaches');
        $testHandler->handleClear($this->getMockForAbstractClass(Clear::class));
    }

}
