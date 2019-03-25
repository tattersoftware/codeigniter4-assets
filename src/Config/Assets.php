<?php namespace Tatter\Assets\Config;

use CodeIgniter\Config\BaseConfig;

class Assets extends BaseConfig
{
	// location of asset files in the filesystem
	public $fileBase  = FCPATH . "assets/";
	
	// location of asset files via URL
	public $webBase =  'assets/';
	
	// additional assets to load per route - no leading/trailing slashes
	public $routes = [ ];
}
