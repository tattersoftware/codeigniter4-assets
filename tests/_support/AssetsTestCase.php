<?php namespace ModuleTests\Support;

class AssetsTestCase extends \CodeIgniter\Test\CIUnitTestCase
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
		
		// Create the service
		$this->assets = new \Tatter\Assets\Libraries\Assets($this->config);
	}
}
