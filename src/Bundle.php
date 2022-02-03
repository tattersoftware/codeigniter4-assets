<?php

namespace Tatter\Assets;

/**
 * Bundle Class
 *
 * Bundles are collections of Assets, either
 * predefined by their properties or built
 * dynamically via the define() method.
 * Bundles can stitch together their Assets
 * into a single view block of tags ready
 * for injection in <head> or <body>.
 */
abstract class Bundle
{
    //--------------------------------------------------------------------
    // Initial Assets
    //--------------------------------------------------------------------

    /**
     * The Assets.
     *
     * @var Asset[]
     */
    protected array $assets = [];

    /**
     * Bundle classes to merge with this Bundle.
     *
     * @var string[]
     */
    protected array $bundles = [];

    /**
     * Paths to include in this Bundle.
     *
     * @var string[]
     */
    protected array $paths = [];

    /**
     * URIs to include in this Bundle.
     *
     * @var string[]
     */
    protected array $uris = [];

    /**
     * Strings to include in this Bundle.
     *
     * @var string[]
     */
    protected array $strings = [];

    //--------------------------------------------------------------------
    // Asset Handling
    //--------------------------------------------------------------------

    /**
     * Processes the properties into Assets and calls define().
     */
    final public function __construct()
    {
        // Put child Bundles first so they are more likely to be overwritten
        foreach ($this->bundles as $bundle) {
            $this->merge(new $bundle());
        }

        // Create Assets out of the remaining preoperties
        foreach ($this->uris as $uri) {
            $this->assets[] = Asset::createFromUri($uri);
        }

        foreach ($this->paths as $uri) {
            $this->assets[] = Asset::createFromPath($uri);
        }

        foreach ($this->strings as $string) {
            $this->assets[] = new Asset($string);
        }

        $this->define();
    }

    /**
     * Applies any initial inputs after the constructor.
     * This method is a stub to be implemented by child classes.
     */
    protected function define(): void
    {
    }

    /**
     * Appends an Asset to the list.
     *
     * @return $this
     */
    final public function add(Asset $asset)
    {
        $this->assets[] = $asset;

        return $this;
    }

    /**
     * Merges Assets from another Bundle.
     *
     * @return $this
     */
    final public function merge(Bundle $bundle)
    {
        foreach ($bundle->getAssets() as $asset) {
            $this->add($asset);
        }

        return $this;
    }

    /**
     * Optimizes and returns the list of Assets.
     *
     * @return Asset[]
     */
    final public function getAssets(): array
    {
        $this->assets = array_values(array_unique($this->assets)); // array_unique works on stringables

        return $this->assets;
    }

    //--------------------------------------------------------------------
    // Display Methods
    //--------------------------------------------------------------------

    /**
     * Concatenates Assets for the <head> tag.
     */
    final public function head(): string
    {
        return $this->toString(true);
    }

    /**
     * Concatenates Assets for the <body> tag.
     */
    final public function body(): string
    {
        return $this->toString(false);
    }

    /**
     * Concatenates all Assets. Probably never very useful.
     */
    final public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * Concatenates Assets for the <body> tag.
     *
     * @param bool|null $head Whether to filter on head/body tag; null returns both
     */
    private function toString(?bool $head = null): string
    {
        $lines = [];

        foreach ($this->getAssets() as $asset) {
            if ($head === null || $head === $asset->isHead()) {
                $lines[] = (string) $asset;
            }
        }

        return implode(PHP_EOL, $lines);
    }

    //--------------------------------------------------------------------
    // Caching
    //--------------------------------------------------------------------

    /**
     * Prepares the bundle for caching.
     *
     * @return Asset[]
     */
    public function __serialize(): array
    {
        return $this->getAssets();
    }

    /**
     * Restores the bundle from cached version.
     *
     * @param Asset[] $data
     */
    public function __unserialize(array $data): void
    {
        foreach ($data as $asset) {
            $this->add($asset);
        }
    }
}
