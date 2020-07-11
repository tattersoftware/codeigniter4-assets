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
		
		$this->assertEquals($expected, $paths);
	}

	public function testBasicRoute()
	{
		$this->assets->setRoute('factories/edit');
		
		$expected = [
			'alert.js',
			'styles.css',
			'factories/factories.css',
			'factories/edit/validate.js',
		];

		$paths = $this->assets->getPaths();
		
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
		
		$this->assertEquals($expected, $paths);
	}

	public function testBasicRouteWithImplictAssets()
	{
		$this->assets->setRoute('factories');
		
		$expected = [
			'alert.js',
			'styles.css',
			'factories/factories.css',
			'factories/index/factoriesDataTable.js',
		];

		$paths = $this->assets->getPaths();
		
		$this->assertEquals($expected, $paths);
	}
}
