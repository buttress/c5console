<?php

namespace Buttress\Concrete\Console\Argument;

use Buttress\Concrete\Console\Command\Argument\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{

    public function testGetCommandAndArguments()
    {
        $mock = $this->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCommandAndArguments'])
            ->getMock();

        $method = (new \ReflectionClass(Parser::class))->getMethod('getCommandAndArguments');
        $method->setAccessible(true);
        $result = $method->invoke($mock, ['c5', 'command', 'derp']);

        $this->assertEquals([
            'arguments' => ['derp'],
            'script' => 'c5',
            'command' => 'command'
        ], $result);
    }

    public function testGetGlobalCommandAndArguments()
    {
        $mock = $this->getMockBuilder(Parser::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCommandAndArguments'])
            ->getMock();

        $method = (new \ReflectionClass(Parser::class))->getMethod('getCommandAndArguments');
        $method->setAccessible(true);

        $old = $GLOBALS['argv'];
        $GLOBALS['argv'] = ['c5', 'command', 'global'];
        $result = $method->invoke($mock);

        $this->assertEquals([
            'arguments' => ['global'],
            'script' => 'c5',
            'command' => 'command'
        ], $result);

        // Set the argv back
        $GLOBALS['argv'] = $old;
    }

}
