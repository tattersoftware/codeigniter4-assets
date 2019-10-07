<?php

class ConfigHandlerTest extends \CodeIgniter\Test\CIUnitTestCase
{
	/**
	 * @var \Tatter\Assets\Libraries\Assets
	 */
	protected $assets;
	
	/**
	 * @var \Tatter\Assets\Config\Assets
	 */
	protected $config;

    public function setUp(): void
    {
        parent::setUp();
		
		$this->config           = new \Tatter\Assets\Config\Assets;
		$this->config->silent   = false;
		$this->config->fileBase = MODULESUPPORTPATH . 'assets/';
		
		// Add mock route paths
		$this->config->routes = [
			'' => [
				'unrouted/machines.js'
			],
			'factories/show' => [
				'vendor/widget',
			],
		];
		
		// Create the service
		$this->assets = new \Tatter\Assets\Libraries\Assets($this->config);
		
		// Limit to just the ConfigHandler
		$this->assets->setHandlers(['\Tatter\Assets\Handlers\ConfigHandler']);
	}

	public function testBasicRoute()
	{
		$this->assets->setRoute('workers/index');
		
		$expected = [
			'unrouted/machines.js',
		];
		
		$paths = $this->assets->getPaths();
		
		$this->assertEquals($expected, $paths);
	}

	public function testRouteWithDirectory()
	{
		$this->assets->setRoute('factories/show');
		
		$expected = [
			'unrouted/machines.js',
			'vendor/widget/colorful.css',
			'vendor/widget/forms.css',
		];
		
		$paths = $this->assets->getPaths();
		
		$this->assertEquals($expected, $paths);
	}
}
