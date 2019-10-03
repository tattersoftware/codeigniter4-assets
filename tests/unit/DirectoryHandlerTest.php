<?php

use CodeIgniter\Config\Services;

class DirectoryHandlerTest extends \ModuleTests\Support\AssetsTestCase
{
    public function setUp(): void
    {
        parent::setUp();
		
		// Limit to just the DirectoryHandler
		$this->assets->setHandlers(['\Tatter\Assets\Handlers\DirectoryHandler']);
	}
	
	public function testDefaultRoute()
	{
		$expected = [
			'styles.css',
			'alert.js',
		];

		$paths = $this->assets->getPaths();
		
		$this->assertEquals($expected, $paths);
	}

	public function testBasicRoute()
	{
		$this->assets->setRoute('factories/edit');
		
		$expected = [
			'styles.css',
			'alert.js',
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
			'styles.css',
			'alert.js',
		];

		$paths = $this->assets->getPaths();
		
		$this->assertEquals($expected, $paths);
	}

	public function testBasicRouteWithImplictAssets()
	{
		$this->assets->setRoute('factories');
		
		$expected = [
			'styles.css',
			'alert.js',
			'factories/factories.css',
			'factories/index/factoriesDataTable.js',
		];

		$paths = $this->assets->getPaths();
		
		$this->assertEquals($expected, $paths);
	}
}
