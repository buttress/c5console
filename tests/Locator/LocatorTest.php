<?php

namespace Buttress\Concrete\Locator;

use Buttress\Concrete\Locator\Detector\Detector;
use Buttress\Concrete\Locator\Detector\LegacyDetector;
use Buttress\Concrete\Locator\Detector\ModernDetector;
use PHPUnit\Framework\TestCase;

class LocatorTest extends TestCase
{

    public function testAddingDetectors()
    {
        $webroot = $this->createMock(WebrootLocator::class);
        $modern = new ModernDetector();
        $legacy = new LegacyDetector();
        $testDetector = $this->getMockForAbstractClass(Detector::class);

        $locator = new Locator($webroot, $modern, $legacy);
        $locator->addDetector($testDetector);

        $property = (new \ReflectionClass($locator))->getProperty('detectors');
        $property->setAccessible(true);

        $this->assertEquals([
            $modern,
            $legacy,
            $testDetector
        ], $property->getValue($locator));
    }

    public function testDetectsModernConcrete()
    {
        $webroot = $this->createMock(WebrootLocator::class);
        $webroot->method('locateWebroot')->willReturnArgument(1);

        $modern = new ModernDetector();
        $legacy = new LegacyDetector();

        $dir = __DIR__ . '/Fixtures/modern';
        $locator = new Locator($webroot, $modern, $legacy);

        $this->assertEquals($dir, $locator->getLocation($dir)->getPath());
    }

    public function testDetectsLegacyConcrete()
    {
        $webroot = $this->createMock(WebrootLocator::class);
        $webroot->method('locateWebroot')->willReturnArgument(1);

        $modern = new ModernDetector();
        $legacy = new LegacyDetector();

        $dir = __DIR__ . '/Fixtures/legacy';
        $locator = new Locator($webroot, $modern, $legacy);

        $this->assertEquals($dir, $locator->getLocation($dir)->getPath());
    }

    public function testDoesntDetectBustedConcrete()
    {
        $webroot = $this->createMock(WebrootLocator::class);
        $webroot->method('locateWebroot')->willReturnArgument(1);

        $modern = new ModernDetector();
        $legacy = new LegacyDetector();

        $dir = __DIR__ . '/Fixtures/busted';
        $locator = new Locator($webroot, $modern, $legacy);

        $this->assertNull($locator->getLocation($dir));
    }
}
