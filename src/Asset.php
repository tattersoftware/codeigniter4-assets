<?php namespace Tatter\Assets;

use CodeIgniter\Files\File;
use Tatter\Assets\Config\Assets as AssetsConfig;
use Tatter\Assets\Exceptions\AssetsException;

/**
 * Asset Class
 *
 * An object representation of a single Asset.
 */
final class Asset
{
	public const IMAGE_EXTENSIONS = [
		'apng',
		'avif',
		'gif',
		'bmp',
		'ico',
		'img',
		'jpeg',
		'jpg',
		'png',
		'svg',
		'tff',
		'webp',
	];

	/**
	 * @var AssetsConfig|null
	 */
	private static $config;

	/**
	 * Asset content, ready for injection.
	 *
	 * @var string
	 */
	private $tag;

	/**
	 * Whether the content should be placed in the head tag.
	 *
	 * @var bool
	 */
	private $head;

	//--------------------------------------------------------------------
	// Configuration
	//--------------------------------------------------------------------

	/**
	 * Loads and returns the configuration.
	 *
	 * @return AssetsConfig
	 */	 
	public static function config(): AssetsConfig
	{
		if (self::$config === null)
		{
			self::$config = config(AssetsConfig::class);

			// Standardize formats
			self::$config->url       = rtrim(self::$config->url, '/') . '/';
			self::$config->directory = rtrim(self::$config->directory, '/') . '/';
		}

		return self::$config;
	}

	/**
	 * Changes the configuration. Should only be used during testing.
	 *
	 * @param AssetsConfig|null $config
	 *
	 * @internal
	 */	 
	public static function useConfig(?AssetsConfig $config)
	{
		self::$config = $config;
	}

	//--------------------------------------------------------------------
	// Named Constructors
	//--------------------------------------------------------------------

	/**
	 * Creates a new Asset from a local file.
	 *
	 * @param string $path File path relative to the configured directory
	 *
	 * @return self
	 */
	public static function createFromPath(string $path): self
	{
		$config = self::config();
		$path   = ltrim($path, '/');
		$file   = new File($config->directory . $path, true);

		// Build the URI
		$uri = $config->uri . $path;

		// Append a timestamp if requested
		if ($config->useTimestamps)
		{
			$uri .=  '?v=' . $file->getMTime();
		}

		return self::createFromUri($uri);
	}

	/**
	 * Creates a new Asset from a remote file.
	 * Note that the framework's link_tag() does not support integrity and crossorigin
	 * fields, so most CDN assets should be created directly.
	 *
	 * @param string $uri
	 * @param string|null $type One of: 'css', 'js', 'img'; or null to detect from extension
	 *
	 * @return self
	 */
	public static function createFromUri(string $uri, string $type = null): self
	{
		helper(['html']);

		if ($type === null)
		{
			$extension = pathinfo(strtok($uri, '?'), PATHINFO_EXTENSION); // Query safe

			// Check for one of the numerous image extension
			if (in_array($extension, self::IMAGE_EXTENSIONS))
			{
				$type = 'img';
			}
			else
			{
				$type = $extension;
			}
		}

		if ($type === 'css')
		{
			return new self(link_tag($uri));
		}
		if ($type === 'js')
		{
			return new self(script_tag($uri), false);
		}
		if ($type === 'img')
		{
			$alt = ucfirst(pathinfo($uri, PATHINFO_FILENAME)); // Query safe
			return new self(img($uri, false, ['alt' => $alt]), false);
		}			

		throw AssetsException::forUnsupportedType($type);
	}

	//--------------------------------------------------------------------
	// Class Methods
	//--------------------------------------------------------------------

	public function __construct(string $tag, bool $head = true)
	{
		$this->tag  = $tag;
		$this->head = $head;
	}

	public function __toString(): string
	{
		return $this->tag;
	}

	public function isHead(): bool
	{
		return $this->head;
	}
}
