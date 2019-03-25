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

		// prioritizes user config in app/Config if found
		if (empty($config)):
			if (class_exists('\Config\Assets')):
				$config = new \Config\Assets();
			else:
				$config = new \Tatter\Assets\Config\Assets();
			endif;
		endif;

		return new \Tatter\Assets\Assets($config, $view);
	}
}
