<?php namespace Tatter\Assets\Libraries;

/***
* Name: Assets
* Author: Matthew Gatner
* Contact: mgatner@tattersoftware.com
* Created: 2019-02-04
*
* Description: Lightweight asset loader for CodeIgniter 4
*
* Requirements:
* 	>= PHP 7.2
* 	>= CodeIgniter 4.0
*	CodeIgniter's Filesystem, HTML, and URL helpers (loaded automatically)
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

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Config\Config;
use CodeIgniter\Config\Services;
use CodeIgniter\Router\RouteCollectionInterface;
use Tatter\Assets\Config\Assets as AssetsConfig;
use Tatter\Assets\Exceptions\AssetsException;

/*** CLASS ***/
class Assets
{
	/**
	 * Our configuration instance.
	 *
	 * @var AssetsConfig
	 */
	protected $config;

	/**
	 * Array of asset paths detected for the current route
	 *
	 * @var array|null
	 */
	protected $paths;

	/**
	 * Array of classes for valid handlers
	 *
	 * @var array
	 */
	protected $handlers = [
		'\Tatter\Assets\Handlers\DirectoryHandler',
		'\Tatter\Assets\Handlers\ConfigHandler',
	];

	/**
	 * The URI to use for matching routed assets.
	 * Defaults to uri_string()
	 *
	 * @var string|null
	 */
	protected $route;

	/**
	 * Whether the route has been sanitized
	 * Prevents re-processing on multiple display calls
	 *
	 * @var bool
	 */
	protected $sanitized = false;

	/**
	 * Route collection used to determine the default route (if needed).
	 *
	 * @var RouteCollectionInterface|null
	 */
	protected $collection;

	// Initiate the helpers
	public function __construct($config = null)
	{
		// Load required helpers
		helper('html');
		helper('url');
		
		// Save the configuration
		$this->config = $config;
		
		// If no webBase is set then use the base URL
		if (empty($this->config->webBase))
		{
			$this->config->webBase = base_url();
		}
		// If the URL is relative then expand it
		elseif (! filter_var($this->config->webBase, FILTER_VALIDATE_URL))
		{
			$this->config->webBase = base_url($this->config->webBase);
		}
		
		// Make sure webBase has a trailing slash
		$this->config->webBase = rtrim($this->config->webBase, '/') . '/';
	}

	// Return the code block for gathered CSS assets as tags
	public function css()
	{
		// Buffer all matched files as tags
		$buffer = '<!-- Local CSS files -->' . PHP_EOL;
		
		foreach ($this->getPaths('css') as $path)
		{
			$buffer .= $this->tag($path) . PHP_EOL;
		}
		
		return $buffer;
	}

	// Return the code block for gathered JS assets as tags
	public function js()
	{
		// Buffer all matched files as tags
		$buffer = '<!-- Local JS files -->' . PHP_EOL;
		
		foreach ($this->getPaths('js') as $path)
		{
			$buffer .= $this->tag($path) . PHP_EOL;
		}
		
		return $buffer;
	}

	// Return gathered paths optionally filtered by an extension.
	public function getPaths($extension = null): array
	{
		// Make sure paths are all gathered
		$this->gather();
		
		if (empty($extension))
		{
			return $this->paths;
		}
		
		$tmpPaths = [];
		foreach ($this->paths as $path)
		{
			if (strtolower(pathinfo($path, PATHINFO_EXTENSION)) == $extension)
			{
				$tmpPaths[] = $path;
			}
		}
		
		return $tmpPaths;		
	}
	
	// Gathers asset paths for this route from all handlers
	protected function gather(): array
	{
		if (! is_null($this->paths))
		{
			return $this->paths;
		}

		$this->sanitizeRoute();
		$this->paths = [];

		foreach ($this->handlers as $class)
		{
			$handler     = new $class($this->config);
			$this->paths = array_merge($handler->gather($this->route), $this->paths);
		}
		
		$this->paths = array_unique($this->paths);
		
		return $this->paths;
	}
	
	// Return an HTML tag from an asset path
	public function tag(string $path)
	{
		// Build the URL
		$url = $this->config->webBase . $path;
		
		// Check for the actual file for version control
		$file = $this->config->fileBase . $path;
		if (is_file($file) && $this->config->useTimestamps)
		{
			$url .=  '?v=' . filemtime($file);
		}
		
		// Create the extension-specific tag
		$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		switch ($extension)
		{
			case 'css':
				return link_tag($url);
			
			case 'js':
				return script_tag($url);
			
			case 'img':
				$alt = ucfirst(pathinfo($path, PATHINFO_FILENAME));
				return img($url, false, ['alt' => $alt]);
			
			default:
				if ($this->config->silent)
				{
					log_message('warning', lang('Assets.unsupportedExtension', [$extension]));
					return;
				}
				
				throw AssetsException::forUnsupportedExtension($extension);
		}
	}

	// Set the URI to use for matching routed assets.
	public function setRoute(string $route)
	{
		$this->route     = $route;
		$this->sanitized = false;
		$this->paths     = null;
		
		return $this;
	}

	// Get the sanitized route; mostly just for testing.
	public function getRoute(): string
	{
		$this->sanitizeRoute();
		return $this->route;
	}

	// Set the handlers; mostly just for testing.
	public function setHandlers(array $classes)
	{
		$this->handlers = $classes;
		
		return $this;
	}

	// Set the route collection used to resolve implicit routes.
	public function setCollection(RouteCollectionInterface $collection)
	{
		$this->collection = $collection;
		return $this;
	}

	// Cleans up the route and expands implicit segments
	protected function sanitizeRoute()
	{
		if ($this->sanitized)
		{
			return;
		}

		// If no route was specified then load the current URI string
		if (is_null($this->route))
		{
			$this->route = uri_string();
		}	

		// If no collection was specified then load the default shared
		if (is_null($this->collection))
		{
			$this->collection = Services::routes();
		}	

		// Sanitize characters
		$this->route = filter_var($this->route, FILTER_SANITIZE_URL);

		// Clean up slashes
		$this->route = trim($this->route, '/');

		// Verify for {locale}
		if (Config::get('App')->negotiateLocale)
		{
			$route = explode('/', $this->route);
			if (count($route) && $route[0] == Services::request()->getLocale()) // @phpstan-ignore-line
			{
				unset($route[0]);
			}
			$this->route = implode('/',$route);
		}

		// If the route is empty then assume the default controller

		if (empty($this->route))
		{
			$this->route = strtolower($this->collection->getDefaultController());
		}
		
		// Always check the default method in case the route is implicit
		$defaultMethod = $this->collection->getDefaultMethod();
		if (! preg_match('/' . $defaultMethod . '$/', $this->route))
		{
			$this->route .= '/' . $defaultMethod;
		}
		
		$this->sanitized = true;
	}
}
