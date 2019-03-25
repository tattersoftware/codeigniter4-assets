<?php namespace Tatter\Assets\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class AssetsException extends FrameworkException implements ExceptionInterface
{
	public static function forUnsupportExtension(string $extension = null)
	{
		return new static("Unsupported file extension: '{$extension}'");
	}
}
