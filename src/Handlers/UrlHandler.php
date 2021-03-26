<?php namespace Tatter\Assets\Handlers;

use Config\Services;
use Tatter\Assets\Config\Assets as AssetsConfig;
use Tatter\Assets\Interfaces\AssetHandlerInterface;

class UrlHandler implements AssetHandlerInterface
{
	/**
	 * Our configuration instance.
	 *
	 * @var AssetsConfig
	 */
	protected $config;

	// Save the config and intiate the helper
	public function __construct(AssetsConfig $config = null)
	{
		// Save the configuration
		$this->config = $config ?? config('Assets');
	}

	// Return the route as a path if it is a valid URL (... to an external resource)
	public function gather(string $route): array
	{
		$externalResources = [];
		$urlPattern = '~(?:/[^/?#]+){2}(?=[?#]|$)~';
		//I suck at regex so I grabbed one: https://stackoverflow.com/questions/56901446/php-regex-for-detecting-url-uri

		foreach ($this->config->routes[$route] as $possibleUrl){

			if(preg_match($urlPattern, $possibleUrl) === 1){

				$externalResources[] = $possibleUrl;

			}//elseif(preg_match($pattern, $possibleUrl) === false){
				// regex error
				// raise a new exception type?
			//}

		}

		$paths = array_merge($paths, $externalResources);


		return $paths;
	}
}
