<?php

use Tatter\Assets\Exceptions\AssetsException;
use Tatter\Assets\RouteBundle;
use Tests\Support\Bundles\FruitSalad;
use Tests\Support\Bundles\LunchBreak;
use Tests\Support\TestCase;

/**
 * @internal
 */
final class RouteBundleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->assets->routes = [
            '*' => [
                'https://pagecdn.io/lib/cleave/1.6.0/cleave.min.js',
                FruitSalad::class,
            ],
            'admin/*' => [
                LunchBreak::class,
                'directory/machines.js',
            ],
        ];
    }

    public function testCreateFromRoute()
    {
        $expected = <<<'EOD'
            <script src="https://pagecdn.io/lib/cleave/1.6.0/cleave.min.js"></script>
            <link href="https://example.com/assets/apple.css" rel="stylesheet" type="text/css">
            <script src="https://example.com/assets/banana.js"></script>
            <link href="https://water.com/glassof.css" rel="stylesheet" type="text/css">
            <script src="https://example.com/assets/directory/machines.js"></script>
            EOD;

        $result = RouteBundle::createFromRoute('admin/foo');

        $this->assertInstanceOf(RouteBundle::class, $result);
        $this->assertCount(5, $result->getAssets());
        $this->assertSame($expected, (string) $result);
    }

    public function testCreateFromRouteUsesCache()
    {
        $key = 'assets-' . md5(serialize([
            'https://pagecdn.io/lib/cleave/1.6.0/cleave.min.js',
            FruitSalad::class,
        ]));

        $this->assets->useCache = true;
        $this->assertEmpty(cache()->getCacheInfo());

        // Place a fake bundle in the cache
        cache()->save($key, new RouteBundle());

        $result = RouteBundle::createFromRoute('foo');

        $this->assertSame((string) (new RouteBundle()), (string) $result);
    }

    public function testCreateFromRouteSavesToCache()
    {
        $this->assets->useCache = true;
        $this->assertEmpty(cache()->getCacheInfo());

        RouteBundle::createFromRoute('foo');

        $this->assertNotEmpty(cache()->getCacheInfo());
    }

    public function testCreateFromRouteEmpty()
    {
        $this->assets->routes['*'] = [];

        $result = RouteBundle::createFromRoute('foo');

        $this->assertInstanceOf(RouteBundle::class, $result);
        $this->assertSame([], $result->getAssets());
    }

    public function testCreateFromRouteThrowsNotString()
    {
        $this->assets->routes['invalid'] = [true];

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Config $route items must be strings.');

        RouteBundle::createFromRoute('invalid');
    }

    public function testCreateFromRouteThrowsInvalidType()
    {
        $this->assets->routes['invalid'] = ['filthyflarmflam'];

        $this->expectException(AssetsException::class);
        $this->expectExceptionMessage(lang('Assets.invalidConfigItem', ['']));

        RouteBundle::createFromRoute('invalid');
    }
}
