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

	/**
	 * Messages for CLI.
	 *
	 * @var array [text, color]
	 */
	protected $messages = [];
	
	public function __construct($config = null)
	{
		// Save the configuration
		$this->config = $config;
	}

	// Scan all namespaces for manifest files
	public function getMessages(): array
	{
		return $this->messages;
	}

	// Scan all namespaces for manifest files
	public function locate(): array
	{
		// Get files from all namespaces in the "Manifests/" subfolder
		$locator = service('locator');
		$files = $locator->listFiles('Manifests');
		
		// Filter by .json extension
		return array_unique(preg_grep("/.+\.json$/i", $files));
	}

	// Publish assets from a single manifest
	public function publish($path): bool
	{
		// Verify the manifest
		$manifest = $this->manifestFromFile($path);
		if ($manifest === null)
		{
			return false;
		}
		
		// Verify the destination
		if (! $this->secureDestination($manifest->destination))
		{
			return false;
		}
		
		// Proceed resource by resource
		$result = true;
		foreach ($manifest->resources as $resource)
		{
			$result = $result && $this->publishResource($resource);
		}
		
		return $result;
	}

	// Read in and verify a manifest from a file path
	protected function manifestFromFile($path): ?object
	{
		// Make sure the file is valid and accessible
		$file = new File($path);

		if (! $file->isFile())
		{
			if ($this->config->silent)
			{
				log_message('warning', lang('Files.fileNotFound', [$path]));
				return;
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
				$error = 'JSON Error #' . $errornum . '. ' . lang('Manifests.invalidFileFormat', [$path]);
				log_message('warning', $error));
				$this->messages[] = [$error, 'red'];
				return;
			}
			
			throw ManifestsException::forInvalidFileFormat($path);
		}
		
		// Verify necessary fields
		foreach (['source', 'destination', 'paths'] as $field)
		{
			if (empty($manifest->{$field}))
			{
				if ($this->config->silent)
				{
					$error = lang('Manifests.fieldMissingFromFile', [$field, $path]);
					log_message('warning', $error));
					$this->messages[] = [$error, 'red'];
					return;
				}
			
				throw ManifestsException::forFieldMissingFromFile($field, $path);
			}
		}
		
		return $manifest;
	}
	
	// Verify or create a destination folder and all folders up to it
	protected function secureDestination($path): bool
	{
		$directory = rtrim($this->config->fileBase, '/');
		
		$segments = explode('/', $path);
		if ($segments[0] != '')
		{
			$segments = array_unshift($segments, '');			
		}

		// Secure each directory up to the destination
		foreach ($segments as $segment)
		{
			$directory .= $segment . '/';
			
			if (! $this->ensureDirectory($directory))
			{
				return false;
			}
			
			if (! $this->addIndexToDirectory($directory))
			{
				return false;
			}
		}
		
		return true;
	}
	
	// Make sure a directory exists and is writable
	protected function ensureDirectory($directory): bool
		// Check for existence
		if (! file_exists($directory))
		{
			mkdir($directory, 0644, true);
		}
		
		// Make sure its a directory
		if (! is_dir($directory))
		{
			$error = lang('Manifests.cannotCreateDirectory', [$directory]);
			log_message('warning', $error));
			$this->messages[] = [$error, 'red'];
			return false;
		}
		
		// Make sure it is writable
		if (! is_writable($directory))
		{
			$error = lang('Manifests.directoryNotWritable', [$directory]);
			log_message('warning', $error));
			$this->messages[] = [$error, 'red'];
			return false;
		}
		
		return true;
	}
	
	// Create index.html in the destination to prevent list access
	protected function addIndexToDirectory($directory): bool
	{
		$path = $directory . 'index.html';
		$file = new File($path);
		
		// Check for existing file
		if ($file->isFile())
		{
			return true;
		}
		
		// Directory should be writable but jsut in case...
		if (! $file->isWritable)
		{
			$error = lang('Manifests.directoryNotWritable', [$directory]);
			log_message('warning', $error));
			$this->messages[] = [$error, 'red'];
			return false;
		}
		
		// Do it
		$file = $file->openFile('w');
		if (! $file->fwrite($this->getIndexHtml))
		{
			$error = lang('Manifests.cannotCreateIndexFile', [$path]);
			log_message('warning', $error));
			$this->messages[] = [$error, 'red'];
			return false;
		}
		
		return true;
	}
	
	// Generate content for index.html
	protected function getIndexHtml(): string
	{
		return '<!DOCTYPE html>
<html>
<head>
	<title>403 Forbidden</title>
</head>
<body>

<p>Directory access is forbidden.</p>

</body>
</html>
';
	}

	// Parse a resource and copy it to the determined destination
	protected function publishResource($resource): ?object
	{
		// Validate the source
		if (! isset($resource->source))
		{
			return false;
		}
		
		// Make sure the source exists
		$file = new File($resource->source);
		//WIP

	}
}
