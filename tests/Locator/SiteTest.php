<?php

namespace Buttress\Concrete\Locator;

use PHPUnit\Framework\TestCase;

class SiteTest extends TestCase
{

    public function testStupidStuff()
    {
        $site = Site::create('path', 'version');
        $this->assertEquals('path', $site->getPath());
        $this->assertEquals('version', $site->getVersion());

        $site->setPath('newpath');
        $site->setVersion('newversion');
        $this->assertEquals('newpath', $site->getPath());
        $this->assertEquals('newversion', $site->getVersion());
    }
}
