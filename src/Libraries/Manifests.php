<?php namespace Tatter\Assets\Libraries;

use CodeIgniter\Files\File;
use CodeIgniter\Files\Exceptions\FileException;
use CodeIgniter\Files\Exceptions\FileNotFoundException;
use Tatter\Assets\Exceptions\ManifestsException;
use stdClass;

/**
 * @deprecated Use the framework's Publisher instead
 */
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
		
		// Load the helper
		helper('filesystem');
	}

	// Clear and return status messages
	public function getMessages(): array
	{
		$messages = $this->messages;
		$this->messages = [];
		return $messages;
	}

	// Scan all namespaces for manifest files
	public function locate(): array
	{
		// Get files from all namespaces in the "Manifests/" subfolder
		$locator = service('locator');
		$files = $locator->listFiles('Manifests');
		
		// Filter by .json extension
		return array_unique(preg_grep("#.+\.json$#i", $files));
	}

	// Publish assets from a single manifest
	public function publish(object $manifest): bool
	{
		// Validate the manifest
		if (! $this->validate($manifest))
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
	public function parse(string $file): ?object
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
			if ($this->config->silent)
			{
				$error = lang('Manifests.invalidFileFormat', [$file, json_last_error_msg()]);
				log_message('warning', $error);
				$this->messages[] = [$error, 'red'];
				return null;
			}
			
			throw ManifestsException::forInvalidFileFormat($file, json_last_error_msg());
		}

		// Validate the manifest
		if (! $this->validate($manifest))
		{
			return null;
		}
		
		return $manifest;
	}

	// Validate the required fields
	public function validate(object $manifest): bool
	{
		// Check for the necessary fields
		foreach (['source', 'destination', 'resources'] as $field)
		{
			if (empty($manifest->{$field}))
			{
				if ($this->config->silent)
				{
					$error = lang('Manifests.missingField', [$field]);
					log_message('warning', $error);
					$this->messages[] = [$error, 'red'];
					return false;
				}
		
				throw ManifestsException::forMissingField($field);
			}
		}
		
		// Check each resource for a source
		foreach ($manifest->resources as $resource)
		{
			if (empty($resource->source))
			{
				if ($this->config->silent)
				{
					$error = lang('Manifests.missingField', ['resource->source']);
					log_message('warning', $error);
					$this->messages[] = [$error, 'red'];
					return false;
				}
		
				throw ManifestsException::forMissingField('resource->source');
			}
		}
		
		return true;
	}
	
	// Verify or create a destination folder within fileBase (and all folders up to it)
	protected function secureDestination($path): bool
	{
		$directory = rtrim($this->config->fileBase, '/');

		// Make sure $path is relative and has no trailing slash
		$path = trim(str_replace($directory, '', $path), '/');

		$segments = explode('/', $path);
		if ($segments[0] != '')
		{
			array_unshift($segments, '');			
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
			mkdir($directory, 0755, true);
		}
		
		// Make sure there's a directory there now
		if (! is_dir($directory))
		{
			if ($this->config->silent)
			{
				$error = lang('Manifests.cannotCreateDirectory', [$directory]);
				log_message('warning', $error);
				$this->messages[] = [$error, 'red'];
				return false;
			}

			throw ManifestsException::forCannotCreateDirectory($directory);
		}
		
		// Make sure it is writable
		if (! is_writable($directory))
		{
			if ($this->config->silent)
			{
				$error = lang('Manifests.directoryNotWritable', [$directory]);
				log_message('warning', $error);
				$this->messages[] = [$error, 'red'];
				return false;
			}

			throw ManifestsException::forDirectoryNotWritable($directory);
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
		if (! is_writable(dirname($file)))
		{
			$error = lang('Manifests.directoryNotWritable', [$directory]);
			log_message('warning', $error);
			$this->messages[] = [$error, 'red'];
			return false;
		}
		
		// Do it
		if (file_put_contents($file, $this->getIndexHtml()) === false)
		{
			$error = lang('Manifests.cannotCreateIndexFile', [$file]);
			log_message('warning', $error);
			$this->messages[] = [$error, 'red'];
			return false;
		}
		
		return true;
	}
	
	// Generate content for index.html
	public function getIndexHtml(): string
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
	
	// Expand resource paths relative to their configured bases and the manifest property
	public function expandPaths(&$resource, &$manifest)
	{
		$resource->source = 
			rtrim($this->config->publishBase, '/') . '/' .
			trim($manifest->source, '/') . '/' .
			trim($resource->source, '/');

		$resource->destination =
			rtrim($this->config->fileBase, '/') . '/' .
			trim($manifest->destination, '/') . '/' .
			trim($resource->destination ?? '', '/') . '/';
	}

	/**
	 * Parses a resource and copies it to
	 * the determined destination.
	 *
	 * @param stdClass $resource
	 *
	 * @return bool
	 */
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
		
		if (is_dir($resource->source))
		{
			$resource->source = rtrim($resource->source, '/') . '/';
			return $this->publishResourceDirectory($resource);
		}
		
		return $this->publishFile($resource->source, $resource->destination);
	}


	/**
	 * Scans a directory and applies filters,
	 * publishing each file.
	 *
	 * @param stdClass $resource
	 *
	 * @return bool
	 */
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
				if (is_file($file))
				{
					$result = $this->publishFile($file, $resource->destination);
				}
			}

			return $result;
		}
		
		// Recursive, not flatten
		if (! empty($resource->recursive))
		{
			$items = directory_map($resource->source);
			return $this->publishDirectory($items, $resource->source, $resource->destination, $resource->filter ?? null);
		}
		
		// Single directory
		$items = directory_map($resource->source);
		$items = array_filter($items, 'is_string');
		return $this->publishDirectory($items, $resource->source, $resource->destination, $resource->filter ?? null);
	}

	// Recursive-safe directory publish
	protected function publishDirectory(array $items, string $source, string $destination, $filter = null): bool
	{
		$result = true;

		foreach ($items as $dir => $item)
		{
			// Directory
			if (is_array($item))
			{
				$result = $result && $this->publishDirectory($item, $source . $dir, $destination . $dir, $filter);
			}
			// File, no filter
			elseif (empty($filter))
			{
				$result = $result && $this->publishFile($source . $item, $destination);
			}
			// File passes filter
			elseif (preg_match($filter, $item))
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

		if (copy($source, $destination . basename($source)))
		{
			return true;
		}

		if ($this->config->silent)
		{
			$error = lang('Files.cannotMove', [$source, $destination, 'unknown error']);
			log_message('warning', $error);
			$this->messages[] = [$error, 'red'];

			return false;
		}
	
		throw FileException::forUnableToMove($source, $destination, 'unknown error');
	}
}
