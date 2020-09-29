<?php namespace Tatter\Assets\Config;

use CodeIgniter\Config\BaseConfig;

class Assets extends BaseConfig
{
	// Whether to continue instead of throwing exceptions
	public $silent = true;
	
	// Extensions to use when auto-detecting assets
	public $extensions = ['css', 'js'];
	
	// Location of asset files in the filesystem
	public $fileBase = FCPATH . 'assets/';
	
	// Location of asset URLs
	public $webBase = 'assets/';
	
	// Starting directory for manifest publication
	public $publishBase = ROOTPATH . 'vendor/';
	
	// Whether to append file modification timestamps on asset tags
	public $useTimestamps = true;
	
	// Additional paths to load per route
	// Relative to fileBase, no leading/trailing slashes
	public $routes = [];
}
