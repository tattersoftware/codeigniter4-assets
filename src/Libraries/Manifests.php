<?php namespace Tatter\Assets\Libraries;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Files\File;
use CodeIgniter\Files\Exceptions\FileException;
use CodeIgniter\Files\Exceptions\FileNotFoundException;
use Tatter\Assets\Exceptions\ManifestsException;

class Manifests
{
	/**
	 * Our configuration instance.
	 *
	 * @var \Tatter\Assets\Config\Assets
	 */
	protected $config;
	
	public function __construct($config = null)
	{
		// Save the configuration
		$this->config = $config;
	}

	// Scan all namespaces for manifest files
	public function locate(): array
	{
		// Get files from all namespaces in the "Manifests/" subfolder
		$locator = service('locator');
		$files = $locator->listFiles('Manifests');
		
		// Filter by .json extension
		return preg_grep("/.+\.json$/i", $files);
	}

	// Publish assets from a single manifest
	public function publish($path): bool
	{
		// Make sure the file is valid and accessible
		$file = new File($path);

		if (! $file->isFile())
		{
			if ($this->config->silent)
			{
				log_message('warning', lang('Files.fileNotFound', [$path]));
				return false;
			}
			
			throw FileNotFoundException::forFileNotFound($path);
		}
		
		// Make sure the file is JSON
		$manifest = file_get_contents($file->getRealPath());
		$manifest = json_decode($manifest);

		if ($manifest === NULL)
		{
			$errornum = json_last_error();
			
			if ($this->config->silent)
			{
				log_message('warning', 'JSON Error #' . $errornum);
				log_message('warning', lang('Manifests.invalidFileFormat', [$path]));
				return false;
			}
			
			throw ManifestsException::forInvalidFileFormat($path);
		}
		
		//WIP
	}
}
