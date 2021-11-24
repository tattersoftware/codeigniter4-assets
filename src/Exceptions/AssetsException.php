<?php

namespace Tatter\Assets\Exceptions;

use RuntimeException;

class AssetsException extends RuntimeException
{
    public static function forUnsupportedType(?string $type = null)
    {
        return new static(lang('Assets.unsupportedType', [$type ?? 'empty']));
    }

    public static function forInvalidConfigItem(string $route)
    {
        return new static(lang('Assets.invalidConfigItem', [$route]));
    }
}
