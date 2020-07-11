<?php

use CodeIgniter\Config\Services;
use Tatter\Assets\Handlers\DirectoryHandler;
use Tests\Support\AssetsTestCase;

class DirectoryHandlerTest extends AssetsTestCase
{
    public function setUp(): void
    {
        parent::setUp();
		
		// Limit to just the DirectoryHandler
		$this->assets->setHandlers([DirectoryHandler::class]);
	}
	
	public function testDefaultRoute()
	{
		$expected = [
			'alert.js',
			'styles.css',
		];

		$paths = $this->assets->getPaths();
		sort($paths);
		
		$this->assertEquals($expected, $paths);
	}

	public function testBasicRoute()
	{
		$this->assets->setRoute('factories/edit');
		
		$expected = [
			'alert.js',
			'factories/edit/validate.js',
			'factories/factories.css',
			'styles.css',
		];

		$paths = $this->assets->getPaths();
		sort($paths);
		
		$this->assertEquals($expected, $paths);
	}

	public function testBasicRouteWithoutAssets()
	{
		$this->assets->setRoute('workers/index');
		
		$expected = [
			'alert.js',
			'styles.css',
		];

		$paths = $this->assets->getPaths();
		sort($paths);
		
		$this->assertEquals($expected, $paths);
	}

	public function testBasicRouteWithImplictAssets()
	{
		$this->assets->setRoute('factories');
		
		$expected = [
			'alert.js',
			'factories/factories.css',
			'factories/index/factoriesDataTable.js',
			'styles.css',
		];

		$paths = $this->assets->getPaths();
		sort($paths);
		
		$this->assertEquals($expected, $paths);
	}
}
