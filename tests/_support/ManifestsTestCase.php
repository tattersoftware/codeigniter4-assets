<?php namespace ModuleTests\Support;

use org\bovigo\vfs\vfsStream;

class ManifestsTestCase extends \CodeIgniter\Test\CIUnitTestCase
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
		
		// Start the virtual filesystem
		$this->root = vfsStream::setup();
		
		$this->config              = new \Tatter\Assets\Config\Assets;
		$this->config->silent      = false;
		$this->config->fileBase    = $this->root->url() . '/assets/';
		$this->config->publishBase = MODULESUPPORTPATH;
		
		// Create the service
		$this->manifests = new \Tatter\Assets\Libraries\Manifests($this->config);
		
		// Create an example manifest (equivalent to Widgets.json)
		$this->testManifest = (object)[
			'source'      => 'vendor/WidgetModule/dist',
			'destination' => 'vendor/widgets',
			'resources'   => [
				0 => (object)[
					'source'      => 'widget_style.css',
					'destination' => 'css',
				],
				1 => (object)[
					'source'      => 'notAsset.json',
				],
			]
		];
	}
	
	public function tearDown(): void
	{
		parent::tearDown();
		$this->root = null;
	}
}
