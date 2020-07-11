<?php namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Assets\Libraries\Assets;

class AssetsTestCase extends CIUnitTestCase
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

		$this->config           = new \Tatter\Assets\Config\Assets();
		$this->config->silent   = false;
		$this->config->fileBase = SUPPORTPATH . 'assets/';

		// Create the service
		$this->assets = new Assets($this->config);
	}
}
