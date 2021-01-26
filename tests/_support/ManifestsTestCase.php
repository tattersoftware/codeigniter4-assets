<?php namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Assets\Config\Assets as AssetsConfig;
use Tatter\Assets\Libraries\Manifests;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use stdClass;

class ManifestsTestCase extends CIUnitTestCase
{
	/**
	 * @var vfsStreamDirectory|null
	 */
	protected $root;

	/**
	 * @var Manifests
	 */
	protected $manifests;
	
	/**
	 * @var AssetsConfig
	 */
	protected $config;

	/**
	 * @var stdClass
	 */
	protected $testManifest;

    public function setUp(): void
    {
        parent::setUp();
		
		// Start the virtual filesystem
		$this->root = vfsStream::setup();
		
		$this->config              = new \Tatter\Assets\Config\Assets();
		$this->config->silent      = false;
		$this->config->fileBase    = $this->root->url() . '/assets/';
		$this->config->publishBase = SUPPORTPATH;
		
		// Create the service
		$this->manifests = new Manifests($this->config);
		
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
