<?php

namespace Tatter\Assets;

/**
 * Vendor Bundle Abstract Class
 *
 * A bundle of Assets specifically sourced from
 * third-party files published to the configured
 * "vendor" path (see Assets Config) and used in
 * conjunction with VendorPublisher.
 */
abstract class VendorBundle extends Bundle
{
    /**
     * The base directory, i.e. Assets directory + vendor path
     *
     * @var string|null
     */
    private static $base;

    /**
     * Returns the base path according to the configurations.
     */
    public static function base(): string
    {
        if (self::$base === null) {
            $config     = Asset::config();
            self::$base = $config->directory . '/' . $config->vendor;
        }

        return self::$base;
    }

    /**
     * Adds an Asset by its vendor path, i.e. relative to base().
     *
     * @return $this
     */
    final public function addPath(string $path)
    {
        $this->add(Asset::createFromPath(Asset::config()->vendor . trim($path, '/ ')));

        return $this;
    }
}
