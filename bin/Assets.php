<?php namespace Config;

/***
*
* This file contains example values to override or augment default library behavior.
* Recommended usage:
*	1. Copy the file to app/Config/Assets.php
*	2. Set any override variables
*	3. Add additional route-specific assets to $routes
*	4. Remove any lines to fallback to defaults
*
***/

class Assets extends \Tatter\Assets\Config\Assets
{
	// Whether to continue instead of throwing exceptions
	public $silent = true;
	
	// Extensions to use when auto-detecting assets
	public $extensions = ['css', 'js'];
	
	// Location of asset files in the filesystem
	public $fileBase = FCPATH . 'assets/';
	
	// Location of asset URLs
	public $webBase = 'https://example.com/assets/';
	
	// Starting directory for manifest publication
	public $publishBase = ROOTPATH . 'vendor/';
	
	// Additional paths to load per route
	// Relative to fileBase, no leading/trailing slashes
	public $routes = [
		'' => [
			'bootstrap/dist/css/bootstrap.min.css',
			'bootstrap/dist/js/bootstrap.bundle.min.js',
		],
		'files/upload' => [
			'dropzone/',
		],
	];
}
