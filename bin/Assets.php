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
	// location of asset files in the filesystem
	public $fileBase  = FCPATH . "assets/";
	
	// location of asset files via URL; can be relative or full URL
	public $webBase =  'https://example.com/assets/';
	
	// additional assets to load per route - no leading/trailing slashes
	public $routes = [
		'' => [
			"bootstrap/dist/css/bootstrap.min.css",
			"bootstrap/dist/js/bootstrap.bundle.min.js",
		],
		'files/upload' => [
			"dropzone/dropzone.min.css",
			"dropzone/dropzone.min.js",
		],
	];
}
