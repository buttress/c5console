<?php

namespace Buttress\Concrete\Log;

use League\CLImate\CLImate;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class LoggerTest extends TestCase
{

    public function testLogsInfo()
    {
        $climate = $this->getMockBuilder(CLImate::class)
            ->disableOriginalConstructor()
            ->setMethods(['output', 'error'])
            ->getMock();
        $climate->expects($this->exactly(4))->method('output');
        $climate->expects($this->exactly(3))->method('error');

        $logger = new Logger($climate);

        $logger->log(LogLevel::DEBUG, 'debug');
        $logger->log(LogLevel::INFO, 'info');
        $logger->log(LogLevel::NOTICE, 'notice');
        $logger->log(LogLevel::ALERT, 'alert');
        $logger->log(LogLevel::CRITICAL, 'critical');
        $logger->log(LogLevel::EMERGENCY, 'emergency');
        $logger->log(LogLevel::ERROR, 'error');
        $logger->log(LogLevel::WARNING, 'warning');
    }

    public function testDebugMode()
    {
        $climate = $this->getMockBuilder(CLImate::class)
            ->disableOriginalConstructor()
            ->setMethods(['output', 'error'])
            ->getMock();
        $climate->expects($this->once())->method('output');

        $logger = new Logger($climate);
        $logger->debug('test');

        $logger->debug = true;
        $logger->debug('test');
    }

    public function testInterpolates()
    {
        $climate = $this->getMockBuilder(CLImate::class)
            ->disableOriginalConstructor()
            ->setMethods(['output', 'error'])
            ->getMock();

        $output = '';
        $climate->expects($this->once())->method('output')->willReturnCallback(function ($input) use (&$output) {
            $output .= $input;
        });

        $logger = new Logger($climate);
        $logger->log('info', 'test {test}', ['test' => 123]);

        $this->assertContains('test 123', $output);
    }
}
