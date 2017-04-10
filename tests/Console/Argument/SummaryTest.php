<?php

namespace Buttress\Concrete\Console\Argument;

use Buttress\Concrete\Console\Command\Argument\Summary;
use League\CLImate\Argument\Filter;
use League\CLImate\CLImate;
use PHPUnit\Framework\TestCase;

class SummaryTest extends TestCase
{

    public function testOutputCallsSummary()
    {
        $filter = $this->createMock(Filter::class);
        $filter->method('optional')->willReturn([]);
        $filter->method('required')->willReturn([]);

        $summary = $this->getMockBuilder(Summary::class)
            ->disableOriginalConstructor()
            ->setMethods([
                'summarize'
            ])
            ->getMock();
        $summary->expects($this->once())->method('summarize');


        $cli = $this->getMockBuilder(CLImate::class)->setMethods(['out'])->getMock();
        $cli->method('out')->willReturnSelf();

        $summary->setDescription('Description');
        $summary->setFilter($filter, []);
        $summary->setClimate($cli);

        $summary->output();
    }
}
