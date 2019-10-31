<?php namespace Tatter\Assets\Handlers;

use CodeIgniter\Config\BaseConfig;
use Config\Services;
use Tatter\Assets\Interfaces\AssetHandlerInterface;

class DirectoryHandler implements AssetHandlerInterface
{	
	// Save the config and intiate the helper
	public function __construct(BaseConfig $config = null)
	{
		// Save the configuration
		$this->config = $config ?? config('Assets');
		helper('filesystem');
	}
	
	// Search the config directory and each segment
	public function gather(string $route): array
	{
		$directory = rtrim($this->config->fileBase, '/') . '/';
		$paths = $this->gatherFromDirectory($directory);
		
		foreach (explode('/', $route) as $segment)
		{
			$directory .= $segment . '/';
			$paths = array_merge($paths, $this->gatherFromDirectory($directory));
		}
		
		return $paths;
	}
	
	// Gather asset files from a single directory
	public function gatherFromDirectory(string $directory): array
	{
		$directory = rtrim($directory, '/') . '/';
		if (! is_dir($directory))
		{
			return [];
		}

		$paths = [];
		foreach (directory_map($directory, 1) as $item)
		{
			// Make sure it is a desired asset file
			if (in_array(strtolower(pathinfo($item, PATHINFO_EXTENSION)), $this->config->extensions))
			{
				// Just add the part of the path after the base directory
				$paths[] = str_replace($this->config->fileBase, '', $directory . $item);
			}
		}
		
		return $paths;
	}
}
