<?php namespace Tatter\Assets\Exceptions;

use CodeIgniter\Exceptions\ExceptionInterface;
use CodeIgniter\Exceptions\FrameworkException;

/**
 * @deprecated
 */
class ManifestsException extends FrameworkException implements ExceptionInterface
{
	public static function forInvalidFileFormat(string $path, string $reason)
	{
		return new static(lang('Manifests.invalidFileFormat', [$path, $reason]));
	}
	
	public static function forMissingField(string $field)
	{
		return new static(lang('Manifests.missingField', [$field]));
	}
	
	public static function forCannotCreateDirectory(string $path)
	{
		return new static(lang('Manifests.cannotCreateDirectory', [$path]));
	}
	
	public static function forDirectoryNotWritable(string $path)
	{
		return new static(lang('Manifests.directoryNotWritable', [$path]));
	}
}
