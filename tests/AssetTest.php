<?php

use Tatter\Assets\Asset;
use Tatter\Assets\Config\Assets as AssetsConfig;
use Tatter\Assets\Exceptions\AssetsException;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class AssetTest extends TestCase
{
    public function testStringable()
    {
        $asset = new Asset('banana');

        $this->assertSame('banana', (string) $asset);
    }

    public function testIsHead()
    {
        $asset = new Asset('banana');
        $this->assertTrue($asset->isHead());

        $asset = new Asset('banana', false);
        $this->assertFalse($asset->isHead());
    }

    public function testUsesConfig()
    {
        $config = new AssetsConfig();
        Asset::useConfig($config);

        $result = Asset::config();

        $this->assertSame($config, $result);
        $this->assertSame(config(AssetsConfig::class), $result);
    }

    public function testLoadsConfig()
    {
        Asset::useConfig(null);

        $config = Asset::config();

        $this->assertSame(config(AssetsConfig::class), $config);
    }

    public function testUseTimestamps()
    {
        $this->assets->useTimestamps = true;

        $mtime    = filemtime($this->assets->directory . 'apple.css');
        $expected = '<link href="http://example.com/assets/apple.css?v=' . $mtime . '" rel="stylesheet" type="text/css" />';
        $asset    = Asset::createFromPath('apple.css');

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame($expected, (string) $asset);
    }

    public function testCreateFromPath()
    {
        $expected = '<link href="http://example.com/assets/apple.css" rel="stylesheet" type="text/css" />';
        $asset    = Asset::createFromPath('apple.css');

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame($expected, (string) $asset);
    }

    public function testCreateFromUriCss()
    {
        $expected = '<link href="http://banana.com/main.css" rel="stylesheet" type="text/css" />';
        $asset    = Asset::createFromUri('http://banana.com/main.css');

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame($expected, (string) $asset);
        $this->assertTrue($asset->isHead());
    }

    public function testCreateFromUriImg()
    {
        $expected = '<img src="http://banana.com/cat.jpg" alt="Cat" />';
        $asset    = Asset::createFromUri('http://banana.com/cat.jpg');

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame($expected, (string) $asset);
        $this->assertFalse($asset->isHead());
    }

    public function testCreateFromUriJs()
    {
        $expected = '<script src="http://banana.com/index.js" type="text/javascript"></script>';
        $asset    = Asset::createFromUri('http://banana.com/index.js');

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame($expected, (string) $asset);
        $this->assertFalse($asset->isHead());
    }

    public function testCreateFromUriWithExtension()
    {
        $expected = '<img src="http://example.com/fruit" alt="Fruit" />';
        $asset    = Asset::createFromUri('fruit', 'img');

        $this->assertInstanceOf(Asset::class, $asset);
        $this->assertSame($expected, (string) $asset);
        $this->assertFalse($asset->isHead());
    }

    public function testCreateFromUriThrows()
    {
        $this->expectException(AssetsException::class);
        $this->expectExceptionMessage(lang('Assets.unsupportedType', ['exe']));

        $asset = Asset::createFromUri('fruit', 'exe');
    }
}
