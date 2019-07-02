<?php namespace Tatter\Assets\Config;

use CodeIgniter\Config\BaseService;
use CodeIgniter\View\RendererInterface;

class Services extends BaseService
{
    public static function assets(BaseConfig $config = null, RendererInterface $view = null, bool $getShared = true)
    {
		if ($getShared):
			return static::getSharedInstance('assets', $config, $view);
		endif;

		// If no config was injected then load one
		// Prioritizes app/Config if found
		if (empty($config))
			$config = config('Assets');

		return new \Tatter\Assets\Assets($config, $view);
	}
}
