<?php namespace Tatter\Assets\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class AssetsException extends FrameworkException implements ExceptionInterface
{
	public static function forUnsupportedExtension(string $extension = null)
	{
		return new static(lang('Assets.unsupportedExtension', [$extension]));
	}
	
	public static function forInvalidConfigItem(string $route)
	{
		return new static(lang('Assets.invalidConfigItem', [$route]));
	}
}
