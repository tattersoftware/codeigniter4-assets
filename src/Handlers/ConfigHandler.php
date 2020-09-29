<?php namespace Tatter\Assets\Handlers;

use Config\Services;
use Tatter\Assets\Config\Assets as AssetsConfig;
use Tatter\Assets\Exceptions\AssetsException;
use Tatter\Assets\Handlers\DirectoryHandler;
use Tatter\Assets\Interfaces\AssetHandlerInterface;

class ConfigHandler implements AssetHandlerInterface
{
	/**
	 * Our configuration instance.
	 *
	 * @var AssetsConfig
	 */
	protected $config;

	/**
	 * Instance of the directory handler for config routes
	 * that point to directories instead of files.
	 *
	 * @var DirectoryHandler|null
	 */
	protected $directoryHandler;
	
	// Save the config
	public function __construct(AssetsConfig $config = null)
	{
		// Save the configuration
		$this->config = $config ?? config('Assets');
	}
	
	// Search the config property for each segment
	public function gather(string $route): array
	{
		$tmpRoute = '';
		$paths = $this->gatherFromConfigRoute($tmpRoute);
		
		foreach (explode('/', $route) as $segment)
		{
			$tmpRoute = empty($tmpRoute) ? $segment : $tmpRoute . '/' . $segment;
			$paths = array_merge($paths, $this->gatherFromConfigRoute($tmpRoute));
		}

		return $paths;
	}
	
	// Gather asset files from a single config route
	protected function gatherFromConfigRoute(string $route): array
	{
		if (! isset($this->config->routes[$route]))
		{
			return [];
		}
		
		$paths = [];
		foreach ($this->config->routes[$route] as $item)
		{
			$extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
			
			// Check empty extensions for a valid directory
			if (empty($extension))
			{
				$directory = rtrim($this->config->fileBase, '/') . '/' . $item;
				if (is_dir($directory))
				{
					$paths = array_merge($paths, $this->gatherFromDirectory($directory));
				}
				elseif (! $this->config->silent)
				{
					throw AssetsException::forInvalidConfigItem($item);
				}
				else
				{
					log_message('warning', lang('Assets.invalidConfigItem', [$item]));
				}
			}
			elseif (in_array($extension, $this->config->extensions))
			{
				$paths[] = $item;
			}
			elseif (! $this->config->silent)
			{
				throw AssetsException::forUnsupportedExtension($extension);
			}
			else
			{
				log_message('warning', lang('Assets.unsupportedExtension', [$extension]));
			}
		}
		
		return $paths;
	}
	
	// Load and call the directory handler
	protected function gatherFromDirectory(string $directory): array
	{
		if (is_null($this->directoryHandler))
		{
			$this->directoryHandler = new DirectoryHandler($this->config);
		}
		
		return $this->directoryHandler->gatherFromDirectory($directory);
	}
}
