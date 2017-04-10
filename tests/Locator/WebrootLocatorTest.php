<?php

namespace Buttress\Concrete\Locator;

use PHPUnit\Framework\TestCase;

class WebrootLocatorTest extends TestCase
{

    public function testDetectsIndex()
    {
        $webroot = __DIR__ . '/Fixtures/modern';
        $locator = new WebrootLocator();
        $this->assertEquals($webroot, $locator->locateWebroot('/', $webroot));
        $this->assertEquals($webroot, $locator->locateWebroot($webroot, '/'));
    }

    public function testBubblesOut()
    {
        $webroot = __DIR__ . '/Fixtures/modern';
        $locator = new WebrootLocator();

        $test = $webroot . '/some/deep/directory';
        $this->assertEquals($webroot, $locator->locateWebroot('/', $test));
        $this->assertEquals($webroot, $locator->locateWebroot($test, '/'));
    }

    public function testWithWebDirectory()
    {
        $webroot = __DIR__ . '/Fixtures/withweb/web';
        $locator = new WebrootLocator();

        $test = dirname($webroot);
        $this->assertEquals($webroot, $locator->locateWebroot('/', $test));
    }

    public function testNoFalsePositive()
    {
        $webroot = __DIR__ . '/Fixtures/busted';
        $locator = new WebrootLocator();

        $test = dirname($webroot);
        $this->assertNull($locator->locateWebroot('/', $test));
        $this->assertNull($locator->locateWebroot($test, '/'));
    }
}
