<?php namespace Tatter\Libraries;

/***
* Name: Assets
* Author: Matthew Gatner
* Contact: mgatner@tattersoftware.com
* Created: 2019-02-04
*
* Description:  Lightweight asset loader for CodeIgniter 4
*
* Requirements:
* 	>= PHP 7.2
* 	>= CodeIgniter 4.0
*	CodeIgniter's Filesystem and URL helpers (loaded automatically)
*
* Configuration:
* 	Use Config/Assets.php for location overrides and custom route assets
*
* Tables:
*
* @package CodeIgniter4-Assets
* @author Matthew Gatner
* @link https://github.com/tattersoftware/codeigniter4-assets
*
***/

use CodeIgniter\Config\Services;

/*** CLASS ***/
class Assets
{
	protected $fsbase;
	protected $webbase;
	protected $routes;

	// initiate library, check for existing session
	public function __construct($config = null)
	{
		// load required helpers
		helper("filesystem");
		helper("url");
		
		// load optional configuration
		$config = $config ?? config('Assets', false);
		
		// set parameters by config override or use default
		$this->fsbase = $config->fsbase ?? FCPATH."assets/";
		$this->webbase = $config->webbase ?? base_url()."assets/";
		$this->routes = $config->routes ?? [ ];
	}

	// returns route-relevant and preconfigured assets of a given extension
	// accepts 'css' or 'js'
	public function display(string $extension)
	{
		if (! in_array($extension, ['css', 'js']))
			return false;
		
		// output all matched files as tags
		$buffer = "<!-- Local ".strtoupper($extension)." files -->".PHP_EOL;
		foreach ($this->routed($extension) as $file)
			$buffer .= $this->tag($file).PHP_EOL;
		return $buffer;
	}

	// outputs a formatted tag for a single file
	public function displayFile(string $file)
	{
		if (! file_exists($this->fsbase . $file))
			return false;
		return $this->tag($file).PHP_EOL;		
	}
	
	// given (an existing) file, formats it as a vlid tag
	protected function tag(string $file)
	{
		$path = $this->fsbase . $file;

		// get file extension
		$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

		// use last modified time for version control
		$version = filemtime($path);
		
		// create extension-specific tag
		switch ($extension):
			case "css":
				$tag = "<link rel='stylesheet' href='{$this->webbase}{$file}?v={$version}' type='text/css' />";
			break;
			
			case "js":
				$tag = "<script type='text/javascript' src='{$this->webbase}{$file}?v={$version}'></script>";
			break;
			
			case "img":
				$alt = ucfirst(pathinfo($path, PATHINFO_FILENAME));
				$tag = "<img src='{$this->webbase}{$file}?v={$version}' alt='{$alt}' />";
			break;
			
			default:
				throw new \Exception("Unsupported file extension: {$extension}");

		endswitch;
		
		return $tag;
	}
	
	// checks route-relevant folders and pre-configured array for relevant files with given extension
	protected function routed($extension)
	{
		// load services
		//$request = Services::request();
		$router = Services::router();
		$routes = Services::routes();
		
		// get the controller (controllerName less its namespace)
		// accounts for default route and subdirectories
		$controller = str_replace([$routes->getDefaultNamespace(), '\\'], "", $router->controllerName());

		// start the route from the controller
		$segments = explode(PATH_SEPARATOR, $controller);
		
		// add the method
		$segments[] = $router->methodName();

		// add file-safe versions of parameters
		foreach ($router->params() as $param)
			$segments[] = url_title($param);
		
		// always start at base
		array_unshift($segments, "");
		
		// lowercase everything
		$segments = array_map("strtolower", $segments);

		// incrementally check each segment for files (matching this extension)
		$route = "";
		$files = [ ];
		foreach ($segments as $segment):
			$route = empty($route)? $segment : $route."/".$segment; //prevents double slashes
			
			// check for custom assets from config
			if (! empty($this->routes[$route]) ):
				foreach ($this->routes[$route] as $item):
					if (is_string($item) && strtolower(pathinfo($item, PATHINFO_EXTENSION))==strtolower($extension)):
						$files[] = $item;
					endif;
				endforeach;
			endif;
			
			// check filesystem for matching assets
			if ($items = directory_map($this->fsbase . $route, 1)):
				foreach ($items as $item):
					if (strtolower(pathinfo($item, PATHINFO_EXTENSION))==strtolower($extension)):
						$files[] = empty($route)? $item : $route."/".$item;
					endif;
				endforeach;
			endif;

		endforeach;
		
		return array_unique($files);
	}
	
}
