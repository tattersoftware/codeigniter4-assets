<?php

use Tatter\Assets\Asset;
use Tatter\Assets\Bundle;
use Tests\Support\AssetsTestCase;
use Tests\Support\Bundles\FruitSalad;

/**
 * @internal
 */
final class BundleTest extends AssetsTestCase
{
    public function testConstructorPaths()
    {
        $bundle              = new class() extends Bundle {
            protected $paths = ['apple.css'];
        };

        $assets = $bundle->getAssets();

        $this->assertCount(1, $assets);
        $this->assertInstanceOf(Asset::class, $assets[0]);
        $this->assertSame((string) Asset::createFromPath('apple.css'), (string) $assets[0]);
    }

    public function testConstructorBundles()
    {
        $bundle                = new class() extends Bundle {
            protected $bundles = [FruitSalad::class];
        };

        $assets = $bundle->getAssets();

        $this->assertCount(2, $assets);
        $this->assertSame((string) Asset::createFromPath('apple.css'), (string) $assets[0]);
        $this->assertSame((string) Asset::createFromPath('banana.js'), (string) $assets[1]);
    }

    public function testConstructorStrings()
    {
        $bundle                = new class() extends Bundle {
            protected $strings = ['foobar'];
        };

        $assets = $bundle->getAssets();

        $this->assertCount(1, $assets);
        $this->assertInstanceOf(Asset::class, $assets[0]);
        $this->assertSame('foobar', (string) $assets[0]);
    }

    public function testStringable()
    {
        $asset               = new class() extends Bundle {
            protected $paths = ['apple.css'];
        };

        $this->assertSame('<link href="http://example.com/assets/apple.css" rel="stylesheet" type="text/css" />', (string) $asset);
    }

    public function testHead()
    {
        $asset               = new class() extends Bundle {
            protected $paths = [
                'apple.css',
                'banana.js',
            ];
        };

        $this->assertSame('<link href="http://example.com/assets/apple.css" rel="stylesheet" type="text/css" />', $asset->head());
    }
}
