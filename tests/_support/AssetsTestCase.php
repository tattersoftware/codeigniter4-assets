<?php namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Assets\Config\Assets as AssetsConfig;
use Tatter\Assets\Asset;

abstract class AssetsTestCase extends CIUnitTestCase
{
	/**
	 * @var AssetsConfig
	 */
	protected $config;

	/**
	 * Preps the config for the test directory.
	 */
    protected function setUp(): void
    {
        parent::setUp();

		$this->config                = config(AssetsConfig::class);
		$this->config->directory     = SUPPORTPATH . 'Files/';
		$this->config->useTimestamps = false; // These make testing much harder

		Asset::useConfig($this->config);
	}
}
