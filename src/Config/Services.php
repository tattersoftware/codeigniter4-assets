<?php namespace Tatter\Assets\Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\View\RendererInterface;
use Tatter\Assets\Libraries\Assets;
use Tatter\Assets\Libraries\Manifests;
use Tatter\Assets\Config\Assets as AssetsConfig;

class Services extends BaseService
{
    public static function assets(AssetsConfig $config = null, bool $getShared = true)
    {
		if ($getShared)
		{
			return static::getSharedInstance('assets', $config);
		}

		// If no config was injected then load one
		// Prioritizes app/Config if found
		if (empty($config))
		{
			$config = config('Assets');
		}
		return new Assets($config);
	}
	
	/**
	 * @deprecated
	 */
    public static function manifests(AssetsConfig $config = null, bool $getShared = true)
    {
		if ($getShared)
		{
			return static::getSharedInstance('manifests', $config);
		}

		// If no config was injected then load one
		// Prioritizes app/Config if found
		if (empty($config))
		{
			$config = config('Assets');
		}
		return new Manifests($config);
	}
}
