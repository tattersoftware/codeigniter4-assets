<?php

namespace Tatter\Assets;

use InvalidArgumentException;
use Tatter\Assets\Exceptions\AssetsException;

final class RouteBundle extends Bundle
{
    /**
     * Creates a new Bundle from a list of config items
     * for the given route.
     *
     * @throws InvalidArgumentException
     */
    public static function createFromRoute(string $uri): self
    {
        $config = Asset::config();

        if ([] === $items = $config->getForRoute($uri)) {
            return new self();
        }

        if ($config->useCache) {
            // Use the hash of these items for the cache key
            $key = 'assets-' . md5(serialize($items));

            // If there's a cached version then return it
            if ($bundle = cache($key)) {
                return $bundle;
            }
        }

        $bundle = new self();

        foreach ($items as $item) {
            if (! is_string($item)) {
                throw new InvalidArgumentException('Config $route items must be strings.');
            }

            // Bundle
            if (is_a($item, Bundle::class, true)) {
                $bundle->merge(new $item());
            }
            // URI
            elseif (filter_var($item, FILTER_VALIDATE_URL) !== false) {
                $bundle->add(Asset::createFromUri($item));
            }
            // File path
            elseif (is_file($config->directory . '/' . ltrim($item))) {
                $bundle->add(Asset::createFromPath($item));
            }
            // Failure
            else {
                throw AssetsException::forInvalidConfigItem($item);
            }
        }

        // Re-check the config in case one of the included items requested cache exemption
        if ($config->useCache && isset($key)) {
            cache()->save($key, $bundle);
        }

        return $bundle;
    }
}
