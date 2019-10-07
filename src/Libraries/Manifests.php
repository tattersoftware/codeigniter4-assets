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
			$this->expandPaths($resource, $manifest);
			$result = $result && $this->publishResource($resource);
		}
		
		return $result;
	}

	// Read in and verify a manifest from a file
	protected function manifestFromFile($file): ?object
	{
		// Make sure the file is valid and accessible
		if (! is_file($file))
		{
			if ($this->config->silent)
			{
				$error = lang('Files.fileNotFound', [$file]);
				log_message('warning', $error);
				$this->messages[] = [$error, 'red'];

				return null;
			}
			
			throw FileNotFoundException::forFileNotFound($file);
		}
		
		// Make sure the file is JSON
		$manifest = file_get_contents($file);
		$manifest = json_decode($manifest);
		if ($manifest === NULL)
		{
			$errornum = json_last_error();
			
			if ($this->config->silent)
			{
				$error = 'JSON Error #' . $errornum . '. ' . lang('Manifests.invalidFileFormat', [$file]);
				log_message('warning', $error);
				$this->messages[] = [$error, 'red'];
				return null;
			}
			
			throw ManifestsException::forInvalidFileFormat($file);
		}
		
		// Verify necessary fields
		foreach (['source', 'destination', 'resources'] as $field)
		{
			if (empty($manifest->{$field}))
			{
				if ($this->config->silent)
				{
					$error = lang('Manifests.fieldMissingFromFile', [$field, $file]);
					log_message('warning', $error);
					$this->messages[] = [$error, 'red'];
					return null;
				}
			
				throw ManifestsException::forFieldMissingFromFile($field, $file);
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
	{
		// Check for existence
		if (! file_exists($directory))
		{
			mkdir($directory, 0644, true);
		}
		
		// Make sure its a directory
		if (! is_dir($directory))
		{
			$error = lang('Manifests.cannotCreateDirectory', [$directory]);
			log_message('warning', $error);
			$this->messages[] = [$error, 'red'];
			return false;
		}
		
		// Make sure it is writable
		if (! is_writable($directory))
		{
			$error = lang('Manifests.directoryNotWritable', [$directory]);
			log_message('warning', $error);
			$this->messages[] = [$error, 'red'];
			return false;
		}
		
		return true;
	}
	
	// Create index.html in the destination to prevent list access
	protected function addIndexToDirectory($directory): bool
	{
		$file = rtrim($directory, '/') . '/' . 'index.html';

		// Check for existing file
		if (is_file($file))
		{
			return true;
		}
		
		// Directory should be writable but just in case...
		if (! is_writable($file))
		{
			$error = lang('Manifests.directoryNotWritable', [$directory]);
			log_message('warning', $error);
			$this->messages[] = [$error, 'red'];
			return false;
		}
		
		// Do it
		if (file_put_contents($file, $this->getIndexHtml) === false)
		{
			$error = lang('Manifests.cannotCreateIndexFile', [$file]);
			log_message('warning', $error);
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
	
	// Expand resource paths relative to the configured publish root and the manifest property
	protected function expandPaths(&$resource, &$manifest)
	{
		$resource->source = 
			rtrim($this->config->publishRoot, '/') . '/' .
			trim($manifest->source, '/') . '/' .
			ltrim($resource->source, '/');

		$resource->destination =
			rtrim($this->config->publishRoot, '/') . '/' .
			trim($manifest->destination, '/') . '/' .
			ltrim($resource->destination ?? '', '/');
	}

	// Parse a resource and copy it to the determined destination
	protected function publishResource($resource): bool
	{
		// Validate the source
		if (! isset($resource->source))
		{
			return false;
		}
		
		// Make sure the source exists
		if (! file_exists($resource->source))
		{
			if ($this->config->silent)
			{
				$error = lang('Files.fileNotFound', [$resource->source]);
				log_message('warning', $error);
				$this->messages[] = [$error, 'red'];

				return false;
			}
			
			throw FileNotFoundException::forFileNotFound($resource->source);
		}
		
		return is_dir($resource->source) ?
			$this->publishResourceDirectory($resource) :
			$this->publishFile($resource->source, $resource->destination);
	}

	// Scan a directory and apply filters, publishing each file
	protected function publishResourceDirectory($resource): bool
	{
		$result = true;
		
		// Recursive, flatten
		if (! empty($resource->recursive) && ! empty($resource->flatten))
		{
			$files = get_filenames($resource->source, true);
			if (! empty($resource->filter))
			{
				$files = preg_grep($resource->filter, $files);
			}
			
			// Publish every file back to the destination
			foreach ($files as $file)
			{
				$result = $this->publishFile($file, $resource->destination);
			}

			return $result;
		}
		
		// Recursive, not flatten
		elseif (! empty($resource->recursive))
		{
			$items = directory_map($resource->source);
			return $this->publishDirectoryRecursive($items, $resource->source, $resource->destination);
		}
		
		// Publish every file back to the destination
		foreach ($files as $source => $destination)
		{
			$result = $this->publishFile($source, $destination);
		}
		
		return $result;
	}

	// Recursive-safe directory publish
	protected function publishDirectoryRecursive(array $items, string $source, string $destination): bool
	{
		$result = true;
		
		foreach ($items as $dir => $item)
		{
			// Directory
			if (is_array($item))
			{
				$result = $result && $this->publishDirectoryRecursive($item, $source . $dir, $destination . $dir);
			}
			// File
			else
			{
				$result = $result && $this->publishFile($source . $item, $destination);
			}
		}
		
		return $result;
	}

	// Copy a file into place, creating and securing missing directories
	protected function publishFile(string $source, string $destination): bool
	{
		if (! $this->secureDestination($destination))
		{
			return false;
		}
		
		if (copy($source, $destination))
		{
			return true;
		}

		if ($this->config->silent)
		{
			$error = lang('Files.cannotMove', [$source, $destination, -1]);
			log_message('warning', $error);
			$this->messages[] = [$error, 'red'];

			return false;
		}
	
		throw FileException::forUnableToMove($source, $destination, -1);
	}
}
