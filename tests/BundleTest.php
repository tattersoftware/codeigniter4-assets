<?php

use Tatter\Assets\Asset;
use Tatter\Assets\Bundle;
use Tatter\Assets\Config\Assets as AssetsConfig;
use Tests\Support\AssetsTestCase;

final class BundleTest extends AssetsTestCase
{
	public function testConstructorPaths()
	{
		$bundle = new class extends Bundle {
			protected $paths = ['apple.css'];
		};

		$assets = $bundle->getAssets();

		$this->assertCount(1, $assets);
		$this->assertInstanceOf(Asset::class, $assets[0]);
		$this->assertEquals(Asset::createFromPath('apple.css'), $assets[0]);
	}

	public function testStringable()
	{
		$asset = new class extends Bundle {
			protected $paths = ['apple.css'];
		};

		$this->assertSame('<link href="http://example.com/assets/apple.css" rel="stylesheet" type="text/css" />', (string) $asset);
	}

	public function testHead()
	{
		$asset = new class extends Bundle {
			protected $paths = [
				'apple.css',
				'banana.js',
			];
		};

		$this->assertSame('<link href="http://example.com/assets/apple.css" rel="stylesheet" type="text/css" />', $asset->head());
	}
}
