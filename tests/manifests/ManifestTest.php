<?php

use CodeIgniter\Config\Services;
use org\bovigo\vfs\vfsStream;

class ManifestTest extends \CodeIgniter\Test\CIUnitTestCase
{
	/**
	 * @var \Tatter\Assets\Libraries\Manifests
	 */
	protected $manifests;
	
	/**
	 * @var \Tatter\Assets\Config\Assets
	 */
	protected $config;

    public function setUp(): void
    {
        parent::setUp();
		
		$this->config           = new \Tatter\Assets\Config\Assets;
		$this->config->silent   = false;
		$this->config->fileBase = SUPPORTPATH . 'assets/';
		
		// Create the service
		$this->manifests = new \Tatter\Assets\Libraries\Manifests($this->config);
		
		// Start the virtual filesystem
		$this->root = vfsStream::setup();
	}
	
	public function tearDown(): void
	{
		parent::tearDown();
		$this->root = null;
	}
	
	public function testLocate()
	{
		$expected = [
			MODULESUPPORTPATH . 'Manifests/Widgets.json',
			MODULESUPPORTPATH . 'Manifests/frontend.json',
			MODULESUPPORTPATH . 'Manifests/LawyerPack.json',
		];
		$paths = $this->manifests->locate();

		$this->assertEquals($expected, $paths);
	}
	
	public function testManifestFromFile()
	{
		$method = $this->getPrivateMethodInvoker($this->manifests, 'manifestFromFile');
		$path = SUPPORTPATH . 'Manifests/Widgets.json';
		
		$manifest = $method($path);
		
		$expected = (object)[
			'source'      => 'vendor/WidgetModule/dist',
			'destination' => 'vendor/widgets',
			'resources'   => [
				0 => (object)[
					'source'      => 'widget_style.css',
					'destination' => 'vendor/widgets/css',
				],
				1 => (object)[
					'source'      => 'notAsset.json',
				],
			]
		];

		$this->assertEquals($expected, $manifest);
	}
	
	public function testAddIndexToDirectory()
	{
		$method = $this->getPrivateMethodInvoker($this->manifests, 'addIndexToDirectory');
		
		$result = $method($this->root->url() . '/');
		$this->assertTrue($result);
		
		$this->assertTrue($this->root->hasChild('index.html'));
	}
/*
	public function testSecureDirectory()
	{
		$method = $this->getPrivateMethodInvoker($this->manifests, 'addIndexToDirectory');
		
		$result = $method($this->root->url() . '/assets/');
		$this->assertTrue($result);
		
		$this->assertTrue($this->root->hasChild('index.html'));
		$this->assertTrue($this->root->hasChild('assets/index.html'));
*/
}
