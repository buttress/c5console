<?php

namespace Buttress\Concrete\Console\Collection;

use Buttress\Concrete\Console\Command\Collection\Collection;
use Buttress\Concrete\Console\Command\Command;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{

    public function testCollection()
    {
        $command = $this->getMockForAbstractClass(Command::class);
        $collection = new Collection();

        $collection->add($command);
        $collection->add($command);

        $this->assertEquals([$command, $command], $collection->all());
    }

}
