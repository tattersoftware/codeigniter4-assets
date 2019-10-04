<?php

use CodeIgniter\Config\Services;

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
	}
	
	public function testLocate()
	{
		$expected = [
			SUPPORTPATH . 'Manifests/Widgets.json',
			SUPPORTPATH . 'Manifests/frontend.json',
			SUPPORTPATH . 'Manifests/LawyerPack.json',
		];
		$paths = $this->manifests->locate();

		$this->assertEquals($expected, $paths);
	}
}
