<?php namespace Tatter\Assets\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

class ManifestsException extends FrameworkException implements ExceptionInterface
{
	public static function forInvalidFileFormat(string $path)
	{
		return new static(lang('Manifests.invalidFileFormat', [$path]));
	}
}
